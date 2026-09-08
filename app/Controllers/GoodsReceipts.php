<?php

class GoodsReceipts extends Controller
{
    public function create()
    {
        AuthHelper::can('inventory.edit');

        $purchaseOrderModel = $this->model('PurchaseOrder');
        $supplierModel      = $this->model('Supplier');
        $locationModel      = $this->model('InventoryLocation');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            require_once '../app/Services/GoodsReceiptService.php';

            $db = $this->model('Supplier')->db; // any model has DB reference

            $service = new GoodsReceiptService(
                $this->model('PurchaseOrder'),
                $this->model('GoodsReceipt'),
                $this->model('GoodsReceiptItem'),
                $this->model('SupplierLedger'),
                $this->model('InventoryService')
            );
            $service->receive($_POST);

            header('Location: ' . URLROOT . '/goodsreceipts');
            exit;
        }

        $data['purchaseOrders'] = $purchaseOrderModel->getOpenPurchaseOrders();
        $data['locations']       = $locationModel->getAll();
        $data['suppliers']       = $supplierModel->getAll();

        $this->view('goodsreceipts/create', $data);
    }
}
