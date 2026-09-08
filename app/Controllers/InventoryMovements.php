<?php

class InventoryMovements extends Controller
{
    public function index()
    {
        $model = $this->model('InventoryMovement');

        $data['movements'] = $model->getAllMovements();
        // $data['locations'] = $locationModel->getAll();

        $this->view('inventory/movements', $data);
    }

    public function add($inventory_id)
    {

        $model = $this->model('InventoryMovement');
        $locationModel = $this->model('InventoryLocation');

        if ($_POST) {

            $model->addMovement($_POST);

            header('Location: ' . URLROOT . '/inventory');
            exit;
        }

        $data['inventory_id'] = $inventory_id;

        $data['locations'] = $locationModel->getAll();

        $this->view('inventory/movement_add', $data);
    }

 public function receive()
{
    AuthHelper::can('inventory.edit');

    $purchaseOrderModel = $this->model('PurchaseOrder');
    $inventoryModel     = $this->model('Inventory');
    $supplierModel      = $this->model('Supplier');
    $locationModel      = $this->model('InventoryLocation');

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        $service = $this->service('GoodsReceipt');

        $service->receive($_POST);

        FlashHelper::success(
            'Goods received successfully.'
        );

    } catch (Throwable $e) {

        FlashHelper::error(
            $e->getMessage()
        );
    }

    header(
        'Location: ' .
        URLROOT .
        '/inventorymovements'
    );

    exit;
}

    $data['inventory'] =
        $inventoryModel->getAll();

    $data['suppliers'] =
        $supplierModel->getAll();

    $data['locations'] =
        $locationModel->getAll();

    $data['purchaseOrders'] =
        $purchaseOrderModel->getOpenPurchaseOrders();

    $this->view(
        'inventory/receive',
        $data
    );
}
}
