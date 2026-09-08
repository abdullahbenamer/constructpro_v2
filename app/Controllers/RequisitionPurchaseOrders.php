<?php

class RequisitionPurchaseOrders extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CREATE PO FROM RESOURCE REQUISITION
    |--------------------------------------------------------------------------
    */

    public function create($requisition_id)
    {
        AuthHelper::can('purchase_orders.create');

        $fulfillmentModel =
            $this->model('ResourceRequisitionFulfillment');

        $supplierModel =
            $this->model('Supplier');

        $requisition =
            $fulfillmentModel->getRequisition(
                (int)$requisition_id
            );

        if (!$requisition) {

            FlashHelper::error(
                'Resource Requisition not found.'
            );

            header(
                'Location: ' .
                URLROOT .
                '/ResourceRequisitions'
            );

            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | ONLY APPROVED / PARTIAL RR
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $requisition->status,
                ['APPROVED', 'PARTIAL'],
                true
            )
        ) {

            FlashHelper::error(
                'Only approved or partially fulfilled requisitions can create a Purchase Order.'
            );

            header(
                'Location: ' .
                URLROOT .
                '/ResourceRequisitions/details/' .
                $requisition_id
            );

            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | GET REMAINING RR ITEMS
        |--------------------------------------------------------------------------
        */

        $allItems =
            $fulfillmentModel->getFulfillableItems(
                (int)$requisition_id
            );

        /*
        |--------------------------------------------------------------------------
        | ONLY INVENTORY MATERIAL ITEMS
        |--------------------------------------------------------------------------
        */

        $items = [];

        foreach ($allItems as $item) {

            if (
                $item->resource_source === 'INVENTORY'
                &&
                !empty($item->inventory_id)
                &&
                (float)$item->remaining_quantity > 0
            ) {

                $items[] = $item;
            }
        }

        if (empty($items)) {

            FlashHelper::error(
                'There are no remaining inventory materials to purchase.'
            );

            header(
                'Location: ' .
                URLROOT .
                '/ResourceRequisitions/details/' .
                $requisition_id
            );

            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | HANDLE POST
        |--------------------------------------------------------------------------
        */

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            try {

                $supplierId =
                    (int)($_POST['supplier_id'] ?? 0);

                if ($supplierId <= 0) {

                    throw new Exception(
                        'Please select a supplier.'
                    );
                }

                $orderDate =
                    $_POST['order_date']
                    ?? date('Y-m-d');

                $expectedDate =
                    !empty($_POST['expected_date'])
                    ? $_POST['expected_date']
                    : ($requisition->required_date ?? null);

                $postedItems =
                    $_POST['items'] ?? [];

                $poItems = [];

                foreach ($items as $item) {

                    $itemId =
                        (int)$item->id;

                    $quantity =
                        (float)(
                            $postedItems[$itemId]['quantity']
                            ?? 0
                        );

                    /*
                    |--------------------------------------------------------------
                    | SKIP ZERO QUANTITY
                    |--------------------------------------------------------------
                    */

                    if ($quantity <= 0) {
                        continue;
                    }

                    $remaining =
                        (float)$item->remaining_quantity;

                    /*
                    |--------------------------------------------------------------
                    | NEVER EXCEED RR REMAINING
                    |--------------------------------------------------------------
                    */

                    if ($quantity > $remaining) {

                        throw new Exception(
                            'Quantity for "' .
                            $item->inventory_name .
                            '" cannot exceed the RR remaining quantity.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------
                    | UNIT COST
                    |--------------------------------------------------------------
                    |
                    | RR already has estimated_unit_cost.
                    | User may change it for the actual purchase.
                    |
                    */

                 if (
    !isset($postedItems[$itemId]['unit_cost'])
    || $postedItems[$itemId]['unit_cost'] === ''
) {
    throw new Exception(
        'Please enter the actual supplier unit cost for "' .
        $item->inventory_name .
        '".'
    );
}

$unitCost = (float)$postedItems[$itemId]['unit_cost'];

if ($unitCost < 0) {
    throw new Exception(
        'Unit cost cannot be negative.'
    );
}

                    $poItems[] = [

                        'inventory_id' =>
                            (int)$item->inventory_id,

                        'quantity' =>
                            $quantity,

                        'unit_cost' =>
                            $unitCost
                    ];
                }

                if (empty($poItems)) {

                    throw new Exception(
                        'Please enter a quantity for at least one material.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | CREATE NORMAL PO
                |--------------------------------------------------------------------------
                */

                $poModel =
                    $this->model('PurchaseOrder');

                $poItemModel =
                    $this->model('PurchaseOrderItem');

                $poNumber =
                    'PO-' . date('ymdHis');

           $poId =
    $poModel->create([

        'po_number' =>
            $poNumber,

        'supplier_id' =>
            $supplierId,

        'project_id' =>
            $requisition->project_id ?? null,

        'requisition_id' =>
            (int)$requisition_id,

        'target_warehouse_id' =>
            $requisition->target_warehouse_id ?? null,

        'delivery_method' =>
            $requisition->delivery_method ?? 'WAREHOUSE',

        'order_date' =>
            $orderDate,

        'expected_date' =>
            $expectedDate,

        'notes' =>
            'Created from Resource Requisition ' .
            (
                $requisition->req_number
                ?? $requisition->requisition_no
                ?? $requisition_id
            )

    ]);

                /*
                |--------------------------------------------------------------------------
                | ADD PO ITEMS
                |--------------------------------------------------------------------------
                */

                foreach ($poItems as $item) {

                    $poItemModel->create([

                        'purchase_order_id' =>
                            $poId,

                        'inventory_id' =>
                            $item['inventory_id'],

                        'quantity' =>
                            $item['quantity'],

                        'unit_cost' =>
                            $item['unit_cost']
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | UPDATE TOTAL
                |--------------------------------------------------------------------------
                */

                $poModel->updateTotals($poId);

                FlashHelper::success(
                    'Purchase Order created successfully from Resource Requisition.'
                );

                header(
                    'Location: ' .
                    URLROOT .
                    '/purchaseorders/details/' .
                    $poId
                );

                exit;

            } catch (Throwable $e) {

                FlashHelper::error(
                    $e->getMessage()
                );

                header(
                    'Location: ' .
                    URLROOT .
                    '/requisitionpurchaseorders/create/' .
                    $requisition_id
                );

                exit;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | LOAD FORM
        |--------------------------------------------------------------------------
        */

        $data = [

            'requisition' =>
                $requisition,

            'items' =>
                $items,

            'suppliers' =>
                $supplierModel->getAll()
        ];

        $this->view(
            'requisition-purchase-orders/create',
            $data
        );
    }
}