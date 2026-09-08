<?php

class PurchaseOrders extends Controller
{
// list all purchase orders
    public function index()
    {
        AuthHelper::can('purchase-orders.view');

        $model = $this->model('PurchaseOrder');

        $data['orders'] = $model->getAll();

        $this->view('purchase-orders/index', $data);
    }

    public function create()
    {
        AuthHelper::can('purchase_orders.create');

        $supplierModel = $this->model('Supplier');
        $model = $this->model('PurchaseOrder');

        // =========================
        // HANDLE POST
        // =========================

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $po_number =
                'PO-' . date('ymdHis');

            $id = $model->create([

                'po_number'    => $po_number,
                'supplier_id'  => $_POST['supplier_id'],
                'order_date'   => $_POST['order_date'],
                'expected_date' => $_POST['expected_date'],
                'notes'        => $_POST['notes']

            ]);

            header(
                'Location: ' .
                    URLROOT .
                    '/purchaseorders/details/' .
                    $id
            );

            exit;
        }

        // =========================
        // LOAD FORM
        // =========================

        $data['suppliers'] =
            $supplierModel->getAll();

        $this->view('purchase-orders/create', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | DETAILS
    |--------------------------------------------------------------------------
    */

    public function details($id)
    {
        AuthHelper::can('purchase_orders.view');

        $model = $this->model('PurchaseOrder');
        $inventoryModel = $this->model('Inventory');

        $po = $model->getById($id);

        if (!$po) {

            header('Location: ' . URLROOT . '/purchaseorders');
            exit;
        }

        $data['po'] = $po;

        $data['items'] =
            $model->getItems($id);

        $data['inventory'] =
            $inventoryModel->getAll();

        $this->view('purchase-orders/details', $data);
    }

    public function items($po_id)
    {

        AuthHelper::can('inventory.edit');

        $purchaseOrderModel = $this->model('PurchaseOrder');

        header('Content-Type: application/json');

        echo json_encode(
            $purchaseOrderModel->getPOItems((int)$po_id)
        );

        exit;
    }


    // for PO items add view
    public function itemsPage($po_id)
    {
        AuthHelper::can('purchase_orders.view');

        $model = $this->model('PurchaseOrder');
        $inventoryModel = $this->model('Inventory');

        $po = $model->getById($po_id);

        if (!$po) {
            header('Location: ' . URLROOT . '/purchaseorders');
            exit;
        }

        $data['po'] = $po;
        $data['items'] = $model->getItems($po_id); // HTML view data
        $data['inventory'] = $inventoryModel->getAll();

        $this->view('purchase-orders/items', $data);
    }

    public function addItem($po_id)
    {
        AuthHelper::can('purchase-orders.create');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/purchaseorders/itemsPage/' . $po_id);
            exit;
        }

        $itemModel = $this->model('PurchaseOrderItem');

        // Lock the PO
        $poModel = $this->model('PurchaseOrder');

        if (!$poModel->isEditable($po_id)) {

            $_SESSION['error'] =
                'Purchase Order is locked and cannot be modified.';

            header(
                'Location: ' .
                    URLROOT .
                    '/purchaseorders/itemsPage/' .
                    $po_id
            );

            exit;
        }

        $total =
    $_POST['quantity'] *
    $_POST['unit_cost'];

$itemModel->create([
    'purchase_order_id'=>$po_id,
    'inventory_id'=>$_POST['inventory_id'],
    'quantity'=>$_POST['quantity'],
    'unit_cost'=>$_POST['unit_cost'],
    'total_cost'=>$total
]);

        // 🔥 ADD THIS
        $poModel = $this->model('PurchaseOrder');
        $poModel->updateTotals($po_id);

        // header('Location: ' . URLROOT . '/purchaseorders/items/' . $po_id);
        header('Location: ' . URLROOT . '/purchaseorders/itemsPage/' . $po_id);

        exit;
    }

    public function deleteItem($id)
    {
        AuthHelper::can('purchase_orders.edit');

        $itemModel = $this->model('PurchaseOrderItem');

        $item = $itemModel->getById($id);

        if (!$item) {
            header('Location: ' . URLROOT . '/purchaseorders');
            exit;
        }

        $po_id = $item->purchase_order_id;

        // lock the PO
        $poModel = $this->model('PurchaseOrder');

        if (!$poModel->isEditable($po_id)) {

            $_SESSION['error'] =
                'Purchase Order is locked and cannot be modified.';

            header(
                'Location: ' .
                    URLROOT .
                    '/purchaseorders/itemsPage/' .
                    $po_id
            );

            exit;
        }

        $itemModel->delete($id);

        // header('Location: ' . URLROOT . '/purchaseorders/items/' . $po_id);
        header('Location: ' . URLROOT . '/purchaseorders/itemsPage/' . $po_id);
        exit;
    }

public function approve($id)
{
    AuthHelper::can('purchase_orders.edit');

    $model = $this->model('PurchaseOrder');

    $po = $model->getById($id);

    if (!$po) {

        $_SESSION['error'] =
            'Purchase Order not found.';

        header(
            'Location: ' .
            URLROOT .
            '/purchaseorders'
        );

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | ONLY DRAFT PURCHASE ORDERS CAN BE APPROVED
    |--------------------------------------------------------------------------
    */

    if ($po->status !== 'draft') {

        $_SESSION['error'] =
            'Only draft purchase orders can be approved.';

        header(
            'Location: ' .
            URLROOT .
            '/purchaseorders/details/' .
            $id
        );

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | PO MUST CONTAIN AT LEAST ONE ITEM
    |--------------------------------------------------------------------------
    */

    $items = $model->getItems($id);

    if (empty($items)) {

        $_SESSION['error'] =
            'Please add at least one item before approving this Purchase Order.';

        header(
            'Location: ' .
            URLROOT .
            '/purchaseorders/details/' .
            $id
        );

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE
    |--------------------------------------------------------------------------
    */

    $model->approve(
        $id,
        $_SESSION['user_id']
    );

    $_SESSION['success'] =
        'Purchase Order approved successfully.';

    header(
        'Location: ' .
        URLROOT .
        '/purchaseorders/details/' .
        $id
    );

    exit;
}

    public function cancel($id)
{
    AuthHelper::can('purchase_orders.edit');

    try {

        $service = $this->service('PurchaseOrder');

        $service->cancel((int)$id);

        FlashHelper::success(
            'Purchase Order cancelled successfully.'
        );

    } catch (Throwable $e) {

        FlashHelper::error(
            $e->getMessage()
        );
    }

    header(
        'Location: ' .
        URLROOT .
        '/purchaseorders'
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| PRINT PURCHASE ORDER
|--------------------------------------------------------------------------
*/

public function print($id)
{
    AuthHelper::can('purchase_orders.view');

    $model = $this->model('PurchaseOrder');

    $po = $model->getById((int)$id);

    if (!$po) {

        header(
            'Location: ' .
            URLROOT .
            '/purchaseorders'
        );

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | ONLY APPROVED / RECEIVED POs SHOULD BE PRINTED AS OFFICIAL PO
    |--------------------------------------------------------------------------
    */

    if (
        !in_array(
            $po->status,
            ['approved', 'partial', 'received'],
            true
        )
    ) {

        $_SESSION['error'] =
            'Only approved Purchase Orders can be printed.';

        header(
            'Location: ' .
            URLROOT .
            '/purchaseorders/details/' .
            $id
        );

        exit;
    }

    $data['po'] =
        $po;

    $data['items'] =
        $model->getItems((int)$id);

    $this->view(
        'purchase-orders/print',
        $data,
        False // preventing from loading web page Header and footer
    );
}

}
