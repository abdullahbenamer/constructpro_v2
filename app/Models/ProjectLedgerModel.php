<?php
require_once '../app/Core/Model.php';
class ProjectLedgerModel extends Model
{

public function addEntry(array $data): bool
{
    $lastBalance = $this->getLastBalance(
        (int)$data['project_id']
    );

    $debit  = (float)($data['debit'] ?? 0);
    $credit = (float)($data['credit'] ?? 0);

    $newBalance =
        $lastBalance
        + $credit
        - $debit;

    $this->db->query(
        "
        INSERT INTO project_ledger
        (
            project_id,
            entry_type,
            ref_table,
            ref_id,
            description,
            debit,
            credit,
            balance_after
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ",
        [
            $data['project_id'],
            $data['entry_type'],
            $data['ref_table'],
            $data['ref_id'],
            $data['description'],
            $debit,
            $credit,
            $newBalance
        ]
    );

    return true;
}

    public function getLastBalance(int $project_id): float
    {
        $row = $this->db->query(
            "
        SELECT balance_after
        FROM project_ledger
        WHERE project_id = ?
        ORDER BY id DESC
        LIMIT 1
        ",
            [$project_id]
        )->fetch();

        return $row
            ? (float)$row->balance_after
            : 0.0;
    }

    public function getLedger($project_id)
    {
        return $this->db->query("
        SELECT
            l.*,
            pc.quantity,
            pc.cost_type,
            i.name AS item_name
        FROM project_ledger l

        LEFT JOIN project_costs pc
            ON l.ref_table = 'project_costs'
           AND l.ref_id = pc.id

        LEFT JOIN inventory i
            ON i.id = pc.inventory_id

        WHERE l.project_id = ?

        ORDER BY l.id ASC
    ", [$project_id])->fetchAll();
    }

  public function reverseCost(int $costId): void
{
    $entry = $this->getCostEntry($costId);

    if (!$entry) {
        throw new Exception(
            'Original project cost ledger entry not found.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Prevent duplicate reversal
    |--------------------------------------------------------------------------
    |
    | The project_ledger table only allows:
    |
    |     advance
    |     cost
    |
    | Therefore a reversal is recorded as another "cost" entry,
    | but with CREDIT instead of DEBIT.
    |
    */

    $existing = $this->db->query(
        "
        SELECT id
        FROM project_ledger
        WHERE ref_table = 'project_costs'
          AND ref_id = ?
          AND entry_type = 'cost'
          AND debit = 0
          AND credit = ?
        LIMIT 1
        ",
        [
            $costId,
            (float)$entry->debit
        ]
    )->fetch();

    if ($existing) {
        throw new Exception(
            'This project cost has already been reversed.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create Reversal
    |--------------------------------------------------------------------------
    */

    $this->addEntry([
        'project_id'  => $entry->project_id,

        // Must match the actual database ENUM
        'entry_type'  => 'cost',

        'ref_table'   => 'project_costs',

        'ref_id'      => $costId,

        'description' =>
            'Reversal: ' . $entry->description,

        // Reverse the original debit with a credit
        'debit'       => 0,

        'credit'      => (float)$entry->debit
    ]);
}

    public function getCostEntry(int $costId)
    {
        return $this->db->query(
            "
        SELECT *
        FROM project_ledger
        WHERE ref_table = 'project_costs'
          AND ref_id = ?
        ORDER BY id DESC
        LIMIT 1
        ",
            [$costId]
        )->fetch();
    }


    public function getProjectSummary($project_id)
    {
        return $this->db->query("
        SELECT
            COALESCE(SUM(credit),0) AS total_advances,
            COALESCE(SUM(debit),0) AS total_costs,
            COALESCE(SUM(credit - debit),0) AS balance
        FROM project_ledger
        WHERE project_id = ?
    ", [$project_id])->fetch();
    }

    public function updateCostEntry(
        int $costId,
        string $description,
        float $total
    ): bool {
        /*
    |--------------------------------------------------------------------------
    | Find the ledger entry belonging to this project cost
    |--------------------------------------------------------------------------
    */

        $entry = $this->db->query(
            "
        SELECT id, project_id
        FROM project_ledger
        WHERE ref_table = 'project_costs'
          AND ref_id = ?
        LIMIT 1
        ",
            [$costId]
        )->fetch();

        if (!$entry) {

            throw new Exception(
                'Project ledger entry for this cost was not found.'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Update ledger entry
    |--------------------------------------------------------------------------
    */

        $this->db->query(
            "
        UPDATE project_ledger
        SET
            description = ?,
            debit = ?
        WHERE id = ?
        ",
            [
                $description,
                $total,
                $entry->id
            ]
        );

        /*
    |--------------------------------------------------------------------------
    | Recalculate stored balances
    |--------------------------------------------------------------------------
    */

        $this->recalculateBalances(
            $entry->project_id
        );

        return true;
    }

    public function recalculateBalances(int $project_id): void
    {
        $rows = $this->db->query(
            "
        SELECT
            id,
            debit,
            credit
        FROM project_ledger
        WHERE project_id = ?
        ORDER BY id ASC
        ",
            [$project_id]
        )->fetchAll();

        $balance = 0;

        foreach ($rows as $row) {

            $balance += (float)$row->credit;
            $balance -= (float)$row->debit;

            $this->db->query(
                "
            UPDATE project_ledger
            SET balance_after = ?
            WHERE id = ?
            ",
                [
                    $balance,
                    $row->id
                ]
            );
        }
    }
}
