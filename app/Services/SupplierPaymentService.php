<?php

class SupplierPaymentService extends Model
{
    private SupplierPaymentModel $paymentModel;
    private SupplierLedgerModel $ledgerModel;

    public function __construct(
        SupplierPaymentModel $paymentModel,
        SupplierLedgerModel $ledgerModel
    ) {
        parent::__construct();

        $this->paymentModel = $paymentModel;
        $this->ledgerModel  = $ledgerModel;
    }

    public function create(array $data)
    {
        try {

            $this->db->beginTransaction();

            /*
            |--------------------------------------------------------------------------
            | 1. Create Payment
            |--------------------------------------------------------------------------
            */
            $paymentId = $this->paymentModel->create($data);

            /*
            |--------------------------------------------------------------------------
            | 2. Post to Supplier Ledger (ERP CORE)
            |--------------------------------------------------------------------------
            */
            $this->ledgerModel->add([
                'supplier_id'    => $data['supplier_id'],
                'type'           => 'PAYMENT',
                'reference_type' => 'SupplierPayment',
                'reference_id'   => $paymentId,
                'amount'         => $data['amount'],
                'direction'      => 'CREDIT'
            ]);

            $this->db->commit();

            return $paymentId;

        } catch (Exception $e) {

            $this->db->rollBack();
            throw $e;
        }
    }
}