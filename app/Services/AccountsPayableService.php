<?php
class AccountsPayableService
{
    private $poModel;
    private $ledgerModel;

    public function __construct(
        $poModel,
        $ledgerModel
    ){
        $this->poModel=$poModel;
        $this->ledgerModel=$ledgerModel;
    }

    public function getSupplierSummary($supplierId)
    {
        return [

            'po_count'

                => $this->poModel->countBySupplier($supplierId),

            'ordered_value'

                => $this->poModel->sumBySupplier($supplierId),

            'received_value'

                => $this->ledgerModel->getDebitTotal($supplierId),

            'paid_amount'

                => $this->ledgerModel->getCreditTotal($supplierId),

            'balance'

                => $this->ledgerModel->getBalance($supplierId)

        ];
    }

    public function getStatement($supplierId)
    {
        return $this->ledgerModel->getStatement($supplierId);
    }
}