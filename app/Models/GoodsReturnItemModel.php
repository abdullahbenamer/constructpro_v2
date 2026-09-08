<?php

require_once '../app/Core/Model.php';

class GoodsReturnItemModel extends Model
{

public function create(array $data)
{
    return $this->db->query("
        INSERT INTO goods_return_items
        (
            goods_return_id,
            goods_receipt_item_id,
            inventory_id,
            location_id,
            quantity,
            unit_cost,
            total_cost
        )
        VALUES
        (?, ?, ?, ?, ?, ?, ?)
    ", [
        $data['goods_return_id'],
        $data['goods_receipt_item_id'],
        $data['inventory_id'],
        $data['location_id'],
        $data['quantity'],
        $data['unit_cost'],
        $data['total_cost']
    ]);
}

 public function getByReturn($returnId)
{
    return $this->db->query("
        SELECT

            gri.*,

            i.name,
            i.sku,
            i.base_unit,

            /*
            |----------------------------------------------------------
            | Original GRN receiving location
            |----------------------------------------------------------
            */
            grl.code AS original_location_code,
            grl.name AS original_location_name,

            /*
            |----------------------------------------------------------
            | Actual warehouse from which goods were returned
            |----------------------------------------------------------
            */
            rl.code AS return_location_code,
            rl.name AS return_location_name

        FROM goods_return_items gri

        INNER JOIN inventory i
            ON i.id = gri.inventory_id

        INNER JOIN goods_receipt_items grri
            ON grri.id = gri.goods_receipt_item_id

        LEFT JOIN inventory_locations grl
            ON grl.id = grri.location_id

        INNER JOIN inventory_locations rl
            ON rl.id = gri.location_id

        WHERE gri.goods_return_id = ?

        ORDER BY gri.id

    ", [$returnId])->fetchAll();
}

    /*
    |--------------------------------------------------------------------------
    | TOTAL ALREADY RETURNED FROM A GRN ITEM
    |--------------------------------------------------------------------------
    */

    public function getReturnedQuantity($goodsReceiptItemId)
    {
        $row = $this->db->query("
            SELECT
                COALESCE(SUM(quantity), 0) AS returned_quantity

            FROM goods_return_items

            WHERE goods_receipt_item_id = ?
        ", [$goodsReceiptItemId])->fetch();

        return (float)($row->returned_quantity ?? 0);
    }
}