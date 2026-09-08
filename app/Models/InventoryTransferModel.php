<?php
require_once '../app/Core/Model.php';

class InventoryTransferModel extends Model
{
    
       public function create(array $data): int
{
    $this->db->query(
        "
        INSERT INTO inventory_transfers
        (
            inventory_id,
            from_location_id,
            to_location_id,
            quantity,
            reference,
            notes,
            created_by
        )
        VALUES
        (?, ?, ?, ?, ?, ?, ?)
        ",
        [
            $data['inventory_id'],
            $data['from_location_id'],
            $data['to_location_id'],
            $data['quantity'],
            $data['reference'] ?? null,
            $data['notes'] ?? null,
            $data['created_by'] ?? null
        ]
    );

    return (int)$this->db->lastInsertId();
}

    public function getAll()
    {
        return $this->db->query(
            "
            SELECT
                t.*,

                i.name AS item_name,
                i.sku AS item_sku,

                fl.code AS from_code,
                fl.name AS from_name,

                tl.code AS to_code,
                tl.name AS to_name

            FROM inventory_transfers t

            JOIN inventory i
                ON i.id = t.inventory_id

            JOIN inventory_locations fl
                ON fl.id = t.from_location_id

            JOIN inventory_locations tl
                ON tl.id = t.to_location_id

            ORDER BY t.created_at DESC
            "
        )->fetchAll();
    }

   public function findBySku($value)
{
    return $this->db->query("
        SELECT id, name, sku
        FROM inventory
        WHERE sku = :v
        LIMIT 1
    ", [
        'v' => trim($value)
    ])->fetch();
}
public function getLocationsByItem($inventory_id)
{
    return $this->db->query("
        SELECT l.id, l.name, l.code, s.quantity
        FROM inventory_location_stock s
        JOIN inventory_locations l ON l.id = s.location_id
        WHERE s.inventory_id = :id
          AND s.quantity > 0
    ", [
        'id' => $inventory_id
    ])->fetchAll();
}

public function getById(int $id)
{
    return $this->db->query(
        "
        SELECT *
        FROM inventory_transfers
        WHERE id = ?
        ",
        [$id]
    )->fetch();
}
}
