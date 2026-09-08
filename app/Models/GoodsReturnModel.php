<?php

require_once '../app/Core/Model.php';

class GoodsReturnModel extends Model
{
    /*
    |--------------------------------------------------------------------------
    | CREATE RETURN HEADER
    |--------------------------------------------------------------------------
    */

    public function create(array $data)
    {
        $this->db->query("
            INSERT INTO goods_returns
            (
                return_number,
                supplier_id,
                goods_receipt_id,
                purchase_order_id,
                return_date,
                reason,
                notes,
                total_amount,
                created_by
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $data['return_number'],
            $data['supplier_id'],
            $data['goods_receipt_id'],
            $data['purchase_order_id'],
            $data['return_date'],
            $data['reason'] ?? null,
            $data['notes'] ?? null,
            $data['total_amount'],
            $data['created_by'] ?? null
        ]);

        return $this->db->lastInsertId();
    }

    /*
    |--------------------------------------------------------------------------
    | GET BY ID
    |--------------------------------------------------------------------------
    */

    public function getById($id)
    {
        return $this->db->query("
            SELECT
                gr.*,
                g.grn_number,
                po.po_number,
                s.company_name AS supplier_name

            FROM goods_returns gr

            INNER JOIN goods_receipts g
                ON g.id = gr.goods_receipt_id

            INNER JOIN purchase_orders po
                ON po.id = gr.purchase_order_id

            INNER JOIN suppliers s
                ON s.id = gr.supplier_id

            WHERE gr.id = ?
        ", [$id])->fetch();
    }

    /*
    |--------------------------------------------------------------------------
    | GET ALL
    |--------------------------------------------------------------------------
    */

    public function getAll()
    {
        return $this->db->query("
            SELECT
                gr.*,
                g.grn_number,
                po.po_number,
                s.company_name AS supplier_name

            FROM goods_returns gr

            INNER JOIN goods_receipts g
                ON g.id = gr.goods_receipt_id

            INNER JOIN purchase_orders po
                ON po.id = gr.purchase_order_id

            INNER JOIN suppliers s
                ON s.id = gr.supplier_id

            ORDER BY gr.created_at DESC, gr.id DESC
        ")->fetchAll();
    }

    /*
    |--------------------------------------------------------------------------
    | NEXT RETURN NUMBER
    |--------------------------------------------------------------------------
    */

    public function nextNumber()
    {
        return 'RTS-' . date('ymdHis');
    }
}