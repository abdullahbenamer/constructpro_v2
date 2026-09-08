<?php

require_once '../app/Core/Model.php';

class SupplierLedgerModel extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Add Ledger Entry
    |--------------------------------------------------------------------------
    */
    public function add(array $data)
    {
        $this->db->query("
            INSERT INTO supplier_ledger
            (
                supplier_id,
                type,
                reference_type,
                reference_id,
                amount,
                direction
            )
            VALUES
            (?, ?, ?, ?, ?, ?)
        ", [

            $data['supplier_id'],
            $data['type'],
            $data['reference_type'],
            $data['reference_id'],
            $data['amount'],
            $data['direction']

        ]);

        return $this->db->lastInsertId();
    }

    /*
    |--------------------------------------------------------------------------
    | Supplier Ledger
    |--------------------------------------------------------------------------
    */
    public function getBySupplier($supplierId)
    {
        return $this->db->query("
            SELECT *
            FROM supplier_ledger
            WHERE supplier_id=?
            ORDER BY created_at,id
        ", [$supplierId])->fetchAll();
    }

    /*
    |--------------------------------------------------------------------------
    | Debit Total
    |--------------------------------------------------------------------------
    */
    public function getDebitTotal($supplierId)
    {
        return (float)$this->db->query("
            SELECT
                COALESCE(SUM(amount),0) total
            FROM supplier_ledger
            WHERE supplier_id=?
              AND direction='DEBIT'
        ", [$supplierId])->fetch()->total;
    }

    /*
    |--------------------------------------------------------------------------
    | Credit Total
    |--------------------------------------------------------------------------
    */
    public function getCreditTotal($supplierId)
    {
        return (float)$this->db->query("
            SELECT
                COALESCE(SUM(amount),0) total
            FROM supplier_ledger
            WHERE supplier_id=?
              AND direction='CREDIT'
        ", [$supplierId])->fetch()->total;
    }

    /*
    |--------------------------------------------------------------------------
    | Current Supplier Balance
    |--------------------------------------------------------------------------
    */
    public function getBalance($supplierId)
    {
        return
            $this->getDebitTotal($supplierId)
            -
            $this->getCreditTotal($supplierId);
    }

    /*
    |--------------------------------------------------------------------------
    | ERP Statement
    |--------------------------------------------------------------------------
    */
public function getStatement($supplierId, $from = null, $to = null)
{
    $params = [$supplierId];
    $whereDate = "";

    if ($from && $to) {
        $whereDate = " AND DATE(created_at) BETWEEN ? AND ? ";
        $params[] = $from;
        $params[] = $to;
    }

    $rows = $this->db->query("
        SELECT
            id,
            supplier_id,
            type,
            reference_type,
            reference_id,
            amount,
            direction,
            created_at
        FROM supplier_ledger
        WHERE supplier_id = ?
        $whereDate
        ORDER BY created_at ASC, id ASC
    ", $params)->fetchAll();

    $statement = [];
    $balance = 0;

    foreach ($rows as $row) {

        $debit  = ($row->direction === 'DEBIT') ? (float)$row->amount : 0;
        $credit = ($row->direction === 'CREDIT') ? (float)$row->amount : 0;

        $balance += ($debit - $credit);

        $statement[] = (object)[
            'date'        => $row->created_at,
            'type'        => $row->type,
            'reference'   => $row->reference_type,
            'ref_id'      => $row->reference_id,
            'debit'       => $debit,
            'credit'      => $credit,
            'balance'     => $balance
        ];
    }

    return $statement;
}

public function getTotalOutstanding()
{
    $result = $this->db->query("
        SELECT
            COALESCE(
                SUM(
                    CASE
                        WHEN balance > 0
                        THEN balance
                        ELSE 0
                    END
                ),
                0
            ) AS total_outstanding
        FROM (
            SELECT
                supplier_id,
                SUM(
                    CASE
                        WHEN direction = 'DEBIT'
                        THEN amount
                        ELSE -amount
                    END
                ) AS balance
            FROM supplier_ledger
            GROUP BY supplier_id
        ) AS supplier_balances
    ")->fetch();

    return (float)($result->total_outstanding ?? 0);
}
}