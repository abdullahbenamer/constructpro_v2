<?php

require_once '../app/Services/BaseService.php';

class GoodsReturnService extends BaseService
{
    private GoodsReceiptItemModel $receiptItemModel;
    private GoodsReturnModel $returnModel;
    private GoodsReturnItemModel $returnItemModel;
    private SupplierLedgerModel $ledgerModel;
    private InventoryService $inventoryService;

    public function __construct(
        GoodsReceiptItemModel $receiptItemModel,
        GoodsReturnModel $returnModel,
        GoodsReturnItemModel $returnItemModel,
        SupplierLedgerModel $ledgerModel,
        InventoryService $inventoryService
    ) {
        parent::__construct();

        $this->receiptItemModel = $receiptItemModel;
        $this->returnModel      = $returnModel;
        $this->returnItemModel  = $returnItemModel;
        $this->ledgerModel      = $ledgerModel;
        $this->inventoryService = $inventoryService;
    }

    /*
    |--------------------------------------------------------------------------
    | RETURN GOODS TO SUPPLIER
    |--------------------------------------------------------------------------
    */

    public function returnGoods(array $data): int
    {
        return $this->transaction(function () use ($data) {

            /*
            |--------------------------------------------------------------------------
            | 1. BASIC INPUT
            |--------------------------------------------------------------------------
            */

            $receiptItemId =
                (int)($data['goods_receipt_item_id'] ?? 0);

            $locationId =
                (int)($data['location_id'] ?? 0);

            $quantity =
                (float)($data['quantity'] ?? 0);

            if ($receiptItemId <= 0) {
                throw new Exception(
                    'Invalid goods receipt item.'
                );
            }

            if ($locationId <= 0) {
                throw new Exception(
                    'Invalid warehouse location.'
                );
            }

            if ($quantity <= 0) {
                throw new Exception(
                    'Return quantity must be greater than zero.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 2. LOAD ORIGINAL RECEIPT ITEM
            |--------------------------------------------------------------------------
            */

            $receiptItem =
                $this->receiptItemModel->getById(
                    $receiptItemId
                );

            if (!$receiptItem) {
                throw new Exception(
                    'Goods receipt item not found.'
                );
            }

            $receiptLocationId =
    (int)($receiptItem->location_id ?? 0);

if ($receiptLocationId <= 0) {
    throw new Exception(
        'The original goods receipt does not have a receiving location.'
    );
}

if ($locationId !== $receiptLocationId) {
    throw new Exception(
        'The selected warehouse does not match the warehouse where this goods receipt was received.'
    );
}

            /*
            |--------------------------------------------------------------------------
            | 3. CHECK PREVIOUS RETURNS
            |--------------------------------------------------------------------------
            */

            $alreadyReturned =
                $this->returnItemModel
                    ->getReturnedQuantity(
                        $receiptItemId
                    );

            $receivedQuantity =
                (float)$receiptItem->quantity;

            $returnable =
                $receivedQuantity - $alreadyReturned;

            if ($returnable <= 0) {
                throw new Exception(
                    'This goods receipt item has already been fully returned.'
                );
            }

            if ($quantity > $returnable) {
                throw new Exception(
                    'Cannot return '
                    . number_format($quantity, 2)
                    . ' units. Only '
                    . number_format($returnable, 2)
                    . ' units remain returnable.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 4. AUTHORITATIVE VALUES FROM ORIGINAL GRN
            |--------------------------------------------------------------------------
            */

            $inventoryId =
                (int)$receiptItem->inventory_id;

            $supplierId =
                (int)$receiptItem->supplier_id;

            $purchaseOrderId =
                (int)$receiptItem->purchase_order_id;

            $goodsReceiptId =
                (int)$receiptItem->goods_receipt_id;

            $unitCost =
                (float)$receiptItem->unit_cost;

            $total =
                $quantity * $unitCost;

            /*
            |--------------------------------------------------------------------------
            | 5. VERIFY PHYSICAL STOCK
            |--------------------------------------------------------------------------
            */

            $available =
                $this->inventoryService->available(
                    $inventoryId,
                    $locationId
                );

            if ($quantity > $available) {
                throw new Exception(
                    'Not enough stock in the selected warehouse. '
                    . 'Available quantity: '
                    . number_format($available, 2)
                    . '.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 6. CREATE RETURN HEADER
            |--------------------------------------------------------------------------
            */

            $returnNumber =
                $this->returnModel->nextNumber();

            $returnId =
                $this->returnModel->create([

                    'return_number' =>
                        $returnNumber,

                    'supplier_id' =>
                        $supplierId,

                    'goods_receipt_id' =>
                        $goodsReceiptId,

                    'purchase_order_id' =>
                        $purchaseOrderId,

                    'return_date' =>
                        $data['return_date']
                        ?? date('Y-m-d'),

                    'reason' =>
                        $data['reason'] ?? null,

                    'notes' =>
                        $data['notes'] ?? null,

                    'total_amount' =>
                        $total,

                    'created_by' =>
                        $this->currentUserId()
                ]);

            /*
            |--------------------------------------------------------------------------
            | 7. CREATE RETURN ITEM
            |--------------------------------------------------------------------------
            */

            $this->returnItemModel->create([

                'goods_return_id' =>
                    $returnId,

                'goods_receipt_item_id' =>
                    $receiptItemId,

                'inventory_id' =>
                    $inventoryId,

                'location_id' =>
                    $locationId,

                'quantity' =>
                    $quantity,

                'unit_cost' =>
                    $unitCost,

                'total_cost' =>
                    $total
            ]);

            /*
            |--------------------------------------------------------------------------
            | 8. ISSUE STOCK
            |--------------------------------------------------------------------------
            */

            $this->inventoryService->issue([

                'inventory_id' =>
                    $inventoryId,

                'location_id' =>
                    $locationId,

                'quantity' =>
                    $quantity,

                'unit_cost' =>
                    $unitCost,

                'supplier_id' =>
                    $supplierId,

                'reference' =>
                    $returnNumber,

                'notes' =>
                    'Return to supplier'
                    . (
                        !empty($data['reason'])
                        ? ': ' . $data['reason']
                        : ''
                    ),

                'created_by' =>
                    $this->currentUserId()
            ]);

            /*
            |--------------------------------------------------------------------------
            | 9. SUPPLIER LEDGER CREDIT
            |--------------------------------------------------------------------------
            */

            $this->ledgerModel->add([

                'supplier_id' =>
                    $supplierId,

                'type' =>
                    'RETURN',

                'reference_type' =>
                    'GoodsReturn',

                'reference_id' =>
                    $returnId,

                'amount' =>
                    $total,

                'direction' =>
                    'CREDIT'
            ]);

            /*
            |--------------------------------------------------------------------------
            | 10. COMPLETE
            |--------------------------------------------------------------------------
            */

            return $returnId;
        });
    }
}