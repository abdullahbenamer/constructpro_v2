<?php

require_once '../app/Core/Model.php';

class SupplierPaymentAllocationModel extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Allocate part of a payment to one GRN
    |--------------------------------------------------------------------------
    */
    public function create($data)
    {
        return $this->db->query("
            INSERT INTO supplier_payment_allocations
            (
                payment_id,
                goods_receipt_id,
                amount
            )
            VALUES
            (?,?,?)
        ",[
            $data['payment_id'],
            $data['goods_receipt_id'],
            $data['amount']
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete allocations of one payment
    |--------------------------------------------------------------------------
    */
    public function deleteByPayment($payment_id)
    {
        return $this->db->query("
            DELETE
            FROM supplier_payment_allocations
            WHERE payment_id=?
        ",[$payment_id]);
    }

    /*
    |--------------------------------------------------------------------------
    | Payment allocations
    |--------------------------------------------------------------------------
    */
    public function getByPayment($payment_id)
    {
        return $this->db->query("
            SELECT

                spa.*,

                gr.grn_number,
                gr.receipt_date

            FROM supplier_payment_allocations spa

            INNER JOIN goods_receipts gr
                ON gr.id=spa.goods_receipt_id

            WHERE spa.payment_id=?

            ORDER BY gr.receipt_date
        ",[$payment_id])->fetchAll();
    }

    /*
    |--------------------------------------------------------------------------
    | Amount already paid against one GRN
    |--------------------------------------------------------------------------
    */
    public function totalAllocatedToGRN($grn_id)
    {
        $row = $this->db->query("
            SELECT
                COALESCE(SUM(amount),0) total
            FROM supplier_payment_allocations
            WHERE goods_receipt_id=?
        ",[$grn_id])->fetch();

        return (float)$row->total;
    }

    /*
    |--------------------------------------------------------------------------
    | Outstanding balance of one GRN
    |--------------------------------------------------------------------------
    */
    public function outstandingGRN($grn_id)
    {
        $grn = $this->db->query("
            SELECT total_amount
            FROM goods_receipts
            WHERE id=?
        ",[$grn_id])->fetch();

        if(!$grn){
            return 0;
        }

        return
            (float)$grn->total_amount
            -
            $this->totalAllocatedToGRN($grn_id);
    }

    /*
    |--------------------------------------------------------------------------
    | Outstanding GRNs of one supplier
    |--------------------------------------------------------------------------
    */
    public function getOutstandingGRNs($supplier_id)
    {
        return $this->db->query("
            SELECT

                gr.id,

                gr.grn_number,

                gr.receipt_date,

                gr.total_amount,

                (
                    SELECT
                    COALESCE(SUM(amount),0)

                    FROM supplier_payment_allocations spa

                    WHERE spa.goods_receipt_id=gr.id

                ) paid

            FROM goods_receipts gr

            WHERE gr.supplier_id=?

            ORDER BY gr.receipt_date
        ",[$supplier_id])->fetchAll();
    }
}