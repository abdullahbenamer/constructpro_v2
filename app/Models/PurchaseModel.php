<?php

require_once '../app/Core/Model.php';

class PurchaseModel extends Model
{
    
    public function countBySupplier($supplier_id)
{
    return $this->db->query(
        "SELECT COUNT(*) AS total FROM purchases WHERE supplier_id = ?",
        [$supplier_id]
    )->fetch()->total ?? 0;
}

public function sumBySupplier($supplier_id)
{
    return $this->db->query(
        "
        SELECT COALESCE(SUM(total_amount), 0) AS total 
        FROM purchase_orders 
        WHERE supplier_id = ?
        ",
        [$supplier_id]
    )->fetch()->total ?? 0;
}

public function lastPurchaseDate($supplier_id)
{
    return $this->db->query(
        "SELECT created_at FROM purchases 
         WHERE supplier_id = ? 
         ORDER BY created_at DESC LIMIT 1",
        [$supplier_id]
    )->fetch()->created_at ?? null;
}

public function getBySupplier($supplier_id)
{
    return $this->db->query(
        "SELECT * FROM purchases WHERE supplier_id = ? ORDER BY created_at DESC",
        [$supplier_id]
    )->fetchAll();
}

    }