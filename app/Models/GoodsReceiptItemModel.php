<?php

require_once '../app/Core/Model.php';

class GoodsReceiptItemModel extends Model
{
    /*
    |--------------------------------------------------------------------------
    | CREATE GRN ITEM
    |--------------------------------------------------------------------------
    */

    public function create(array $data)
    {
        return $this->db->query("
            INSERT INTO goods_receipt_items
            (
                goods_receipt_id,
                purchase_order_item_id,
                inventory_id,
                location_id,
                quantity,
                unit_cost,
                total_cost
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ", [
            $data['goods_receipt_id'],
            $data['purchase_order_item_id'],
            $data['inventory_id'],
            $data['location_id'],
            $data['quantity'],
            $data['unit_cost'],
            $data['total_cost']
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | GET GRN ITEMS
    |--------------------------------------------------------------------------
    */

    public function getItems($grn_id)
    {
        return $this->db->query("
            SELECT

                gri.*,

                i.name,
                i.sku,
                i.base_unit,

                l.code AS location_code,
                l.name AS location_name

            FROM goods_receipt_items gri

            INNER JOIN inventory i
                ON i.id = gri.inventory_id

            LEFT JOIN inventory_locations l
                ON l.id = gri.location_id

            WHERE gri.goods_receipt_id = ?

            ORDER BY gri.id

        ", [$grn_id])->fetchAll();
    }


    /*
    |--------------------------------------------------------------------------
    | GET GRN ITEM BY ID
    |--------------------------------------------------------------------------
    */

    public function getById($id)
    {
        return $this->db->query("
            SELECT

                gri.*,

                gr.supplier_id,
                gr.purchase_order_id,
                gr.id AS goods_receipt_id,
                gr.grn_number,

                i.name,
                i.sku,
                i.base_unit,

                l.code AS location_code,
                l.name AS location_name

            FROM goods_receipt_items gri

            INNER JOIN goods_receipts gr
                ON gr.id = gri.goods_receipt_id

            INNER JOIN inventory i
                ON i.id = gri.inventory_id

            LEFT JOIN inventory_locations l
                ON l.id = gri.location_id

            WHERE gri.id = ?

        ", [$id])->fetch();
    }
}