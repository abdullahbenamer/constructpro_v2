<?php

class SupplierPayments extends Controller
{
    public function create($supplier_id)
    {
        AuthHelper::can('suppliers.pay');

        $supplierModel = $this->model('Supplier');
        $paymentModel  = $this->model('SupplierPayment');

        $data['supplier'] = $supplierModel->getById($supplier_id);

        if (!$data['supplier']) {
            header("Location: " . URLROOT . "/suppliers");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    require_once '../app/Services/SupplierPaymentService.php';

$paymentService = new SupplierPaymentService(
    $this->model('SupplierPayment'),
    $this->model('SupplierLedger')
);

$paymentService->create([
    'supplier_id'  => $supplier_id,
    'payment_date' => $_POST['payment_date'],
    'amount'       => $_POST['amount'],
    'method'       => $_POST['method'],
    'reference'    => $_POST['reference'],
    'notes'        => $_POST['notes']
]);


            header("Location: " . URLROOT . "/suppliers/info/$supplier_id");
            exit;
        }

        $this->view('supplier-payments/create', $data);
    }

    public function list($supplier_id)
    {
        $paymentModel = $this->model('SupplierPayment');

        echo json_encode(
            $paymentModel->getBySupplier($supplier_id)
        );
    }
}