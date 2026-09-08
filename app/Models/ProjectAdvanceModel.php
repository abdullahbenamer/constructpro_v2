<?php

require_once '../app/Core/Model.php';

class ProjectAdvanceModel extends Model
{
    public function addAdvance($data)
    {
        // 1. Save advance
        $this->db->query(
            "INSERT INTO project_advances
            (project_id, amount, payment_method, reference, notes, received_by, advance_date)
            VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $data['project_id'],
                $data['amount'],
                $data['payment_method'],
                $data['reference'],
                $data['notes'],
                $_SESSION['user_id'],
                $data['advance_date']
            ]
        );

        return $this->db->lastInsertId();
    }

    public function getAdvancesByProject($project_id)
    {
        return $this->db->query(
            "SELECT *
             FROM project_advances
             WHERE project_id = ?
             ORDER BY advance_date DESC",
            [$project_id]
        )->fetchAll();
    }

    public function getProjectBalance($project_id)
{
    return $this->db->query("
        SELECT 
            SUM(credit) AS total_in,
            SUM(debit) AS total_out,
            (SUM(credit) - SUM(debit)) AS balance
        FROM project_ledger
        WHERE project_id = ?
    ", [$project_id])->fetch();
}

    private function insertSettlement($project_id, $advance_id, $cost_id, $amount)
    {
        $this->db->query(
            "INSERT INTO project_settlements
        (project_id, advance_id, cost_id, amount, settlement_type)
        VALUES (?, ?, ?, ?, 'advance_to_cost')",
            [$project_id, $advance_id, $cost_id, $amount]
        );
    }

    public function getTotalAdvances()
    {
        return $this->db->query("
        SELECT COALESCE(SUM(amount),0) AS total
        FROM project_advances
        WHERE status = 'received'
    ")->fetch()->total;
    }
}
