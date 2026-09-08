<?php

class SupplierQuotations extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LIST
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        AuthHelper::can('purchase_orders.view');

        $model = $this->model('SupplierQuotation');

        $data['quotations'] = $model->getAll();

        $this->view(
            'supplier-quotations/index',
            $data
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE QUOTATION
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        AuthHelper::can('purchase_orders.create');

        $supplierModel = $this->model('Supplier');
        $model = $this->model('SupplierQuotation');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            try {

                $supplierId =
                    (int)($_POST['supplier_id'] ?? 0);

                if ($supplierId <= 0) {
                    throw new Exception(
                        'Please select a supplier.'
                    );
                }

                if (empty($_POST['quotation_date'])) {
                    throw new Exception(
                        'Quotation date is required.'
                    );
                }

                $id = $model->create([

                    'quotation_number' =>
                    $model->nextNumber(),

                    'supplier_id' =>
                    $supplierId,

                    'supplier_reference' =>
                    trim(
                        $_POST['supplier_reference'] ?? ''
                    ),

                    'procurement_reference' =>
                    trim(
                        $_POST['procurement_reference'] ?? ''
                    ),

                    'quotation_date' =>
                    $_POST['quotation_date'],

                    'valid_until' =>
                    !empty($_POST['valid_until'])
                        ? $_POST['valid_until']
                        : null,

                    'required_delivery_date' =>
                    !empty($_POST['required_delivery_date'])
                        ? $_POST['required_delivery_date']
                        : null,

                    'promised_delivery_date' =>
                    !empty($_POST['promised_delivery_date'])
                        ? $_POST['promised_delivery_date']
                        : null,

                    'notes' =>
                    trim(
                        $_POST['notes'] ?? ''
                    ),

                    'evaluation_notes' =>
                    trim(
                        $_POST['evaluation_notes'] ?? ''
                    )

                ]);

                header(
                    'Location: ' .
                        URLROOT .
                        '/supplierquotations/details/' .
                        $id
                );

                exit;
            } catch (Throwable $e) {

                FlashHelper::error(
                    $e->getMessage()
                );

                header(
                    'Location: ' .
                        URLROOT .
                        '/supplierquotations/create'
                );

                exit;
            }
        }

        $data['suppliers'] =
            $supplierModel->getAll();

        $this->view(
            'supplier-quotations/create',
            $data
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DETAILS
    |--------------------------------------------------------------------------
    */

    public function details($id)
    {
        AuthHelper::can('purchase_orders.view');

        $model =
            $this->model('SupplierQuotation');

        $quotation =
            $model->getById($id);

        if (!$quotation) {

            header(
                'Location: ' .
                    URLROOT .
                    '/supplierquotations'
            );

            exit;
        }

        $inventoryModel =
            $this->model('Inventory');

        $unitModel =
            $this->model('Unit');

        $data['quotation'] =
            $quotation;

        $data['items'] =
            $model->getItems($id);

        $data['inventory'] =
            $inventoryModel->getAll();

        $data['units'] =
            $unitModel->getAll();

        $this->view(
            'supplier-quotations/details',
            $data
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ADD ITEM
    |--------------------------------------------------------------------------
    */

    public function addItem($quotation_id)
    {
        AuthHelper::can('purchase_orders.create');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

            header(
                'Location: ' .
                    URLROOT .
                    '/supplierquotations/details/' .
                    $quotation_id
            );

            exit;
        }

        $model =
            $this->model('SupplierQuotation');

        if (!$model->isEditable($quotation_id)) {

            FlashHelper::error(
                'Quotation is locked and cannot be modified.'
            );

            header(
                'Location: ' .
                    URLROOT .
                    '/supplierquotations/details/' .
                    $quotation_id
            );

            exit;
        }

        try {

            $description =
                trim(
                    $_POST['description'] ?? ''
                );

            $quantity =
                (float)($_POST['quantity'] ?? 0);

            $unitPrice =
                (float)($_POST['unit_price'] ?? 0);

            if ($description === '') {
                throw new Exception(
                    'Item description is required.'
                );
            }

            if ($quantity <= 0) {
                throw new Exception(
                    'Quantity must be greater than zero.'
                );
            }

            if ($unitPrice < 0) {
                throw new Exception(
                    'Unit price cannot be negative.'
                );
            }
            $qualityStatus =
                $_POST['quality_status'] ?? '';

            if (
                $qualityStatus !== ''
                &&
                !in_array(
                    $qualityStatus,
                    ['MEETS', 'PARTIAL', 'DOES_NOT_MEET'],
                    true
                )
            ) {
                throw new Exception(
                    'Invalid quality status.'
                );
            }

            $model->addItem([

                'supplier_quotation_id' =>
                $quotation_id,

                'inventory_id' =>
                !empty($_POST['inventory_id'])
                    ? (int)$_POST['inventory_id']
                    : null,

                'description' =>
                $description,

                'specification' =>
                trim(
                    $_POST['specification'] ?? ''
                ),

                'unit_id' =>
                !empty($_POST['unit_id'])
                    ? (int)$_POST['unit_id']
                    : null,

                'quantity' =>
                $quantity,

                'unit_price' =>
                $unitPrice,

                // 'quality_status' =>
                // !empty($_POST['quality_status'])
                //     ? $_POST['quality_status']
                //     : null,

                // use $qualityStatus rather than reading $_POST directly
                'quality_status' =>
                $qualityStatus ?: null,

                'quality_notes' =>
                trim(
                    $_POST['quality_notes'] ?? ''
                ),

                'notes' =>
                trim(
                    $_POST['item_notes'] ?? ''
                )
            ]);

            FlashHelper::success(
                'Quotation item added successfully.'
            );
        } catch (Throwable $e) {

            FlashHelper::error(
                $e->getMessage()
            );
        }

        header(
            'Location: ' .
                URLROOT .
                '/supplierquotations/details/' .
                $quotation_id
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE ITEM
    |--------------------------------------------------------------------------
    */

    public function deleteItem($id)
    {
        AuthHelper::can('purchase_orders.create');

        $model =
            $this->model('SupplierQuotation');

        $item =
            $model->getItemById($id);

        if (!$item) {

            header(
                'Location: ' .
                    URLROOT .
                    '/supplierquotations'
            );

            exit;
        }

        if (
            !$model->isEditable(
                $item->supplier_quotation_id
            )
        ) {

            FlashHelper::error(
                'Quotation is locked and cannot be modified.'
            );

            header(
                'Location: ' .
                    URLROOT .
                    '/supplierquotations/details/' .
                    $item->supplier_quotation_id
            );

            exit;
        }

        $model->deleteItem($id);

        header(
            'Location: ' .
                URLROOT .
                '/supplierquotations/details/' .
                $item->supplier_quotation_id
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | ACCEPT
    |--------------------------------------------------------------------------
    */

    public function accept($id)
    {
        AuthHelper::can('purchase_orders.edit');

        $model =
            $this->model('SupplierQuotation');

        $quotation =
            $model->getById($id);

        if (!$quotation) {

            FlashHelper::error(
                'Quotation not found.'
            );

            header(
                'Location: ' .
                    URLROOT .
                    '/supplierquotations'
            );

            exit;
        }

        if ($quotation->status !== 'DRAFT') {

            FlashHelper::error(
                'Only draft quotations can be accepted.'
            );

            header(
                'Location: ' .
                    URLROOT .
                    '/supplierquotations/details/' .
                    $id
            );

            exit;
        }

        $items =
            $model->getItems($id);

        if (empty($items)) {

            FlashHelper::error(
                'Please add at least one item before accepting the quotation.'
            );

            header(
                'Location: ' .
                    URLROOT .
                    '/supplierquotations/details/' .
                    $id
            );

            exit;
        }

        $model->accept($id);

        FlashHelper::success(
            'Supplier quotation accepted successfully.'
        );

        header(
            'Location: ' .
                URLROOT .
                '/supplierquotations/details/' .
                $id
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | CANCEL
    |--------------------------------------------------------------------------
    */

    public function cancel($id)
    {
        AuthHelper::can('purchase_orders.edit');

        $model =
            $this->model('SupplierQuotation');

        $quotation =
            $model->getById($id);

        if (!$quotation) {

            FlashHelper::error(
                'Quotation not found.'
            );

            header(
                'Location: ' .
                    URLROOT .
                    '/supplierquotations'
            );

            exit;
        }

        $model->cancel($id);

        FlashHelper::success(
            'Quotation cancelled.'
        );

        header(
            'Location: ' .
                URLROOT .
                '/supplierquotations'
        );

        exit;
    }

    /*
|--------------------------------------------------------------------------
| COMPARE QUOTATIONS
|--------------------------------------------------------------------------
*/

    public function compare($reference)
    {
        AuthHelper::can('purchase_orders.view');

        $model =
            $this->model('SupplierQuotation');

        $reference =
            trim($reference);

        if ($reference === '') {

            header(
                'Location: ' .
                    URLROOT .
                    '/supplierquotations'
            );

            exit;
        }

        $quotations =
            $model->getByProcurementReference(
                $reference
            );

        if (empty($quotations)) {

            FlashHelper::error(
                'No quotations found for this procurement reference.'
            );

            header(
                'Location: ' .
                    URLROOT .
                    '/supplierquotations'
            );

            exit;
        }

        $comparison = [];

        foreach ($quotations as $quotation) {

            $comparison[] = [

                'quotation' =>
                $quotation,

                'items' =>
                $model->getComparisonItems(
                    $quotation->id
                )
            ];
        }

        $data['procurement_reference'] =
            $reference;

        $data['comparison'] =
            $comparison;

        $this->view(
            'supplier-quotations/compare',
            $data
        );
    }

    /*
|--------------------------------------------------------------------------
| CREATE PURCHASE ORDER FROM ACCEPTED QUOTATION
|--------------------------------------------------------------------------
*/

    public function createPO($id)
    {
        AuthHelper::can('purchase_orders.create');

        $quotationModel =
            $this->model('SupplierQuotation');

        $poModel =
            $this->model('PurchaseOrder');

        $itemModel =
            $this->model('PurchaseOrderItem');

        $quotation =
            $quotationModel->getById($id);

        if (!$quotation) {

            FlashHelper::error(
                'Quotation not found.'
            );

            header(
                'Location: ' .
                    URLROOT .
                    '/supplierquotations'
            );

            exit;
        }

        /*
    |--------------------------------------------------------------------------
    | MUST BE ACCEPTED
    |--------------------------------------------------------------------------
    */

        if ($quotation->status !== 'ACCEPTED') {

            FlashHelper::error(
                'Only accepted quotations can be converted to a Purchase Order.'
            );

            header(
                'Location: ' .
                    URLROOT .
                    '/supplierquotations/details/' .
                    $id
            );

            exit;
        }


        /*
    |--------------------------------------------------------------------------
    | CHECK IF PO ALREADY EXISTS
    |--------------------------------------------------------------------------
    */

        if (!empty($quotation->purchase_order_id)) {

            FlashHelper::error(
                'A Purchase Order has already been created from this quotation.'
            );

            header(
                'Location: ' .
                    URLROOT .
                    '/supplierquotations/details/' .
                    $id
            );

            exit;
        }


        /*
    |--------------------------------------------------------------------------
    | GET QUOTATION ITEMS
    |--------------------------------------------------------------------------
    */

        $items =
            $quotationModel->getItems($id);

        if (empty($items)) {

            FlashHelper::error(
                'Quotation contains no items.'
            );

            header(
                'Location: ' .
                    URLROOT .
                    '/supplierquotations/details/' .
                    $id
            );

            exit;
        }


        /*
    |--------------------------------------------------------------------------
    | EVERY ITEM MUST EXIST IN INVENTORY
    |--------------------------------------------------------------------------
    |
    | Existing PO items require inventory_id.
    | Therefore a quotation item marked as "New Item"
    | cannot yet be copied to a PO.
    |
    */

        foreach ($items as $item) {

            if (empty($item->inventory_id)) {

                FlashHelper::error(
                    'Quotation contains an item that is not yet linked to Inventory. Please add it to Inventory first.'
                );

                header(
                    'Location: ' .
                        URLROOT .
                        '/supplierquotations/details/' .
                        $id
                );

                exit;
            }
        }


        /*
    |--------------------------------------------------------------------------
    | CREATE PO HEADER
    |--------------------------------------------------------------------------
    */

        try {

            $poNumber =
                'PO-' . date('ymdHis');

            $poId =
                $poModel->create([

                    'po_number' =>
                    $poNumber,

                    'supplier_id' =>
                    $quotation->supplier_id,

                    'order_date' =>
                    date('Y-m-d'),

                    'expected_date' =>
                    !empty($quotation->promised_delivery_date)
                        ? $quotation->promised_delivery_date
                        : $quotation->required_delivery_date,

                    'notes' =>
                    'Created from Supplier Quotation ' .
                        $quotation->quotation_number

                ]);


            /*
        |--------------------------------------------------------------------------
        | COPY ITEMS
        |--------------------------------------------------------------------------
        */

            foreach ($items as $item) {

                $quantity =
                    (float)$item->quantity;

                $unitPrice =
                    (float)$item->unit_price;

                $total =
                    $quantity * $unitPrice;

                $itemModel->create([

                    'purchase_order_id' =>
                    $poId,

                    'inventory_id' =>
                    $item->inventory_id,

                    'quantity' =>
                    $quantity,

                    'unit_cost' =>
                    $unitPrice,

                    'total_cost' =>
                    $total

                ]);
            }


            /*
        |--------------------------------------------------------------------------
        | UPDATE PO TOTAL
        |--------------------------------------------------------------------------
        */

            $poModel->updateTotals($poId);


            /*
        |--------------------------------------------------------------------------
        | LINK QUOTATION TO CREATED PO
        |--------------------------------------------------------------------------
        */

            $quotationModel->setPurchaseOrderId(
                $id,
                $poId
            );


            FlashHelper::success(
                'Purchase Order created successfully from quotation.'
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
                    '/supplierquotations/details/' .
                    $id
            );

            exit;
        }
    }
}
