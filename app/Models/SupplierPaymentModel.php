<?php

require_once '../app/Core/Model.php';

class SupplierPaymentModel extends Model
{
public function create($data)
{
    $this->db->query("
        INSERT INTO supplier_payments
        (supplier_id, payment_date, amount, method, reference, notes, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ", [
        $data['supplier_id'],
        $data['payment_date'],
        $data['amount'],
        $data['method'],
        $data['reference'],
        $data['notes'],
        $_SESSION['user_id']
    ]);

    return $this->db->lastInsertId();
}

    public function getBySupplier($supplier_id)
    {
        return $this->db->query("
            SELECT *
            FROM supplier_payments
            WHERE supplier_id = ?
            ORDER BY payment_date DESC
        ", [$supplier_id])->fetchAll();
    }

    public function sumBySupplier($supplier_id)
    {
        return $this->db->query("
            SELECT COALESCE(SUM(amount),0) AS total
            FROM supplier_payments
            WHERE supplier_id = ?
        ", [$supplier_id])->fetch()->total ?? 0;
    }

    public function countBySupplier($supplier_id)
    {
        return $this->db
            ->query($sql, [$id])
            ->fetchColumn();
    }


    public function beginTransaction()
    {
        $this->db->beginTransaction();
    }

    public function commit()
    {
        $this->db->commit();
    }

    public function rollback()
    {
        $this->db->rollBack();
    }
}
