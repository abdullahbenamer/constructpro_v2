<?php

class Suppliers extends Controller
{
    public function index()
    {
        $model = $this->model('Supplier');

        $data['suppliers'] = $model->getAll();

        $this->view('suppliers/index', $data);
    }

    public function create()
    {
        AuthHelper::can('suppliers.create');

        $model = $this->model('Supplier');

        // HANDLE FORM
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $data = [

                'company_name'   => trim($_POST['company_name']),
                'contact_person' => trim($_POST['contact_person']),
                'phone'          => trim($_POST['phone']),
                'email'          => trim($_POST['email']),
                'address'        => trim($_POST['address']),
                'notes'          => trim($_POST['notes'])

            ];

            // =========================
            // VALIDATION
            // =========================

            if (empty($data['company_name'])) {

                die("Company name is required");
            }

            // =========================
            // SAVE
            // =========================

            $model->create($data);

            header('Location: ' . URLROOT . '/suppliers');
            exit;
        }

        // =========================
        // LOAD VIEW
        // =========================

        $this->view('suppliers/create');
    }

    public function edit($id)
    {
        AuthHelper::can('suppliers.create');

        $model = $this->model('Supplier');

        $supplier = $model->getById($id);

        if (!$supplier) {

            header('Location: ' . URLROOT . '/suppliers');
            exit;
        }

        // =========================
        // HANDLE POST
        // =========================

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $data = [

                'company_name'   => trim($_POST['company_name']),
                'contact_person' => trim($_POST['contact_person']),
                'phone'          => trim($_POST['phone']),
                'email'          => trim($_POST['email']),
                'address'        => trim($_POST['address']),
                'notes'          => trim($_POST['notes'])

            ];

            $model->update($id, $data);

            header('Location: ' . URLROOT . '/suppliers');
            exit;
        }

        $data['supplier'] = $supplier;

        $this->view('suppliers/edit', $data);
    }

    public function delete($id)
    {
        AuthHelper::can('suppliers.create');

        $model = $this->model('Supplier');

        $model->delete($id);

        header('Location: ' . URLROOT . '/suppliers');
        exit;
    }
    public function details($id)
    {
        AuthHelper::can('suppliers.view');

        $model = $this->model('Supplier');

        $supplier = $model->getById($id);

        if (!$supplier) {

            header('Location: ' . URLROOT . '/suppliers');
            exit;
        }

        $purchase_orders =
            $model->getPurchaseOrders($id);

        $total_purchases = $this->model('PurchaseOrder')
            ->sumBySupplier($supplier_id);

        $data['supplier'] = $supplier;
        $data['purchase_orders'] = $purchase_orders;
        $data['total_purchases'] = $total_purchases;

        $this->view('suppliers/details', $data);
    }

public function info($supplier_id)
{
    AuthHelper::can('suppliers.view');

    $supplierModel = $this->model('Supplier');
    $paymentModel  = $this->model('SupplierPaymentModel');
    $poModel       = $this->model('PurchaseOrder');
    $ledgerModel   = $this->model('SupplierLedger');

    $data['supplier'] = $supplierModel->getById($supplier_id);

    if (!$data['supplier']) {
        header("Location: " . URLROOT . "/suppliers");
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | DATA SOURCES
    |--------------------------------------------------------------------------
    */
    $purchase_orders = $poModel->getBySupplier($supplier_id);
    $ledger          = $ledgerModel->getStatement($supplier_id);

    /*
    |--------------------------------------------------------------------------
    | SUMMARY (VIEW COMPATIBLE + ERP SAFE)
    |--------------------------------------------------------------------------
    */
    $ordered_value = 0;
    foreach ($purchase_orders as $po) {
        $ordered_value += (float)$po->total_amount;
    }

    $received_value = 0;
    foreach ($ledger as $row) {
        if ($row->type === 'GRN') {
            $received_value += (float)$row->debit;
        }
    }

    $paid_amount = 0;
    foreach ($ledger as $row) {
        if ($row->type === 'PAYMENT') {
            $paid_amount += (float)$row->credit;
        }
    }

    $balance = 0;
    foreach ($ledger as $row) {
        $balance += ((float)$row->debit - (float)$row->credit);
    }

    $data['summary'] = [
        'po_count'       => count($purchase_orders),
        'ordered_value'  => $ordered_value,
        'received_value' => $received_value,
        'paid_amount'    => $paid_amount,
        'balance'        => $balance
    ];

    /*
    |--------------------------------------------------------------------------
    | UI DATA
    |--------------------------------------------------------------------------
    */
    $data['ledger'] = $ledger;
    $data['payments'] = $paymentModel->getBySupplier($supplier_id);
    $data['purchase_orders'] = $purchase_orders;

    $this->view('suppliers/supplier_info', $data);
}

public function ledger($supplier_id)
{
    AuthHelper::can('suppliers.view');

    $supplierModel = $this->model('Supplier');
    $ledgerModel   = $this->model('SupplierLedger');

    $supplier =
        $supplierModel->getById($supplier_id);

    if (!$supplier) {

        header(
            'Location: ' .
            URLROOT .
            '/suppliers'
        );

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | GET SUPPLIER LEDGER
    |--------------------------------------------------------------------------
    */

    $ledger =
        $ledgerModel->getStatement(
            $supplier_id
        );

    /*
    |--------------------------------------------------------------------------
    | SUMMARY
    |--------------------------------------------------------------------------
    */

    $totalDebit  = 0;
    $totalCredit = 0;

    foreach ($ledger as $row) {

        $totalDebit +=
            (float)$row->debit;

        $totalCredit +=
            (float)$row->credit;
    }

    /*
    |--------------------------------------------------------------------------
    | FINAL BALANCE
    |--------------------------------------------------------------------------
    */

    $balance =
        $totalDebit -
        $totalCredit;

    /*
    |--------------------------------------------------------------------------
    | VIEW DATA
    |--------------------------------------------------------------------------
    */

    $data = [

        'supplier'     => $supplier,

        'ledger'       => $ledger,

        'total_debit'  => $totalDebit,

        'total_credit' => $totalCredit,

        'balance'      => $balance

    ];

    /*
    |--------------------------------------------------------------------------
    | PRINTABLE REPORT
    |--------------------------------------------------------------------------
    */

    $this->view(
        'suppliers/ledger_report',
        $data,
        false
    );
}
}
