<?php

require_once '../app/Core/Model.php';

class GoodsReceiptModel extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Create GRN Header
    |--------------------------------------------------------------------------
    */
public function create($data)
{
    $this->db->query("
        INSERT INTO goods_receipts
        (
            grn_number,
            purchase_order_id,
            supplier_id,
            receipt_date,
            subtotal,
            total_amount,
            remarks,
            created_by
        )
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?)
    ", [

        $data['grn_number'],
        $data['purchase_order_id'],
        $data['supplier_id'],
        $data['receipt_date'],
        $data['subtotal'],
        $data['total_amount'],
        $data['remarks'],
        $_SESSION['user_id']

    ]);

    return $this->db->lastInsertId();
}

    /*
    |--------------------------------------------------------------------------
    | Get By ID
    |--------------------------------------------------------------------------
    */
    public function getById($id)
    {
        return $this->db->query("
            SELECT
                gr.*,
                po.po_number,
                s.company_name

            FROM goods_receipts gr

            INNER JOIN purchase_orders po
                ON po.id = gr.purchase_order_id

            INNER JOIN suppliers s
                ON s.id = gr.supplier_id

            WHERE gr.id=?
        ",[$id])->fetch();
    }

    /*
    |--------------------------------------------------------------------------
    | Get by Supplier
    |--------------------------------------------------------------------------
    */
    public function getBySupplier($supplier_id)
    {
        return $this->db->query("
            SELECT *
            FROM goods_receipts
            WHERE supplier_id=?
            ORDER BY receipt_date DESC,id DESC
        ",[$supplier_id])->fetchAll();
    }

    /*
    |--------------------------------------------------------------------------
    | Get by Purchase Order
    |--------------------------------------------------------------------------
    */
    public function getByPurchaseOrder($po_id)
    {
        return $this->db->query("
            SELECT *
            FROM goods_receipts
            WHERE purchase_order_id=?
            ORDER BY receipt_date,id
        ",[$po_id])->fetchAll();
    }

    /*
    |--------------------------------------------------------------------------
    | Sum Received
    |--------------------------------------------------------------------------
    */
    public function totalBySupplier($supplier_id)
    {
        return $this->db->query("
            SELECT
            COALESCE(SUM(total_amount),0) total

            FROM goods_receipts

            WHERE supplier_id=?
        ",[$supplier_id])->fetch()->total;
    }

    /*
    |--------------------------------------------------------------------------
    | Next GRN Number
    |--------------------------------------------------------------------------
    */
    public function nextNumber()
    {
        return 'GRN-'.date('YmdHis');
    }

    public function countBySupplier($supplier_id)
{
    return $this->db->query("
        SELECT COUNT(*) total
        FROM goods_receipts
        WHERE supplier_id=?
    ",[$supplier_id])->fetch()->total;
}

public function getAll()
{
    return $this->db->query("
        SELECT
            gr.*,
            po.po_number,
           s.company_name AS company_name

        FROM goods_receipts gr

        INNER JOIN purchase_orders po
            ON po.id = gr.purchase_order_id

        INNER JOIN suppliers s
            ON s.id = gr.supplier_id

        ORDER BY gr.created_at DESC, gr.id DESC
    ")->fetchAll();
}

}