<?php
require_once '../app/Core/Model.php';

class PurchaseOrderItemModel extends Model
{
    public function getByPO($po_id)
    {
        return $this->db->query(
            "
        SELECT

            poi.*,

            i.name,
            i.sku,
            i.base_unit,

            (poi.quantity * poi.unit_cost) as total,

            COALESCE(poi.received_quantity, 0) as received_qty

        FROM purchase_order_items poi

        JOIN inventory i
            ON i.id = poi.inventory_id

        WHERE poi.purchase_order_id = ?

        ORDER BY poi.id DESC
        ",
            [$po_id]
        )->fetchAll();
    }
    public function create($data)
    {
        return $this->db->query(
            "
            INSERT INTO purchase_order_items
            (
                purchase_order_id,
                inventory_id,
                quantity,
                unit_cost
            )
            VALUES (?, ?, ?, ?)
            ",
            [
                $data['purchase_order_id'],
                $data['inventory_id'],
                $data['quantity'],
                $data['unit_cost']
            ]
        );
    }

    public function delete($id)
    {
        return $this->db->query(
            "DELETE FROM purchase_order_items WHERE id = ?",
            [$id]
        );
    }

    public function getById($id)
    {
        return $this->db->query(
            "
            SELECT *
            FROM purchase_order_items
            WHERE id = ?
            ",
            [$id]
        )->fetch();
    }
}
