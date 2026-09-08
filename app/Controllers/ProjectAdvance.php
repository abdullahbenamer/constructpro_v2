<?php

class ProjectAdvance extends Controller
{
   public function create($project_id)
{
    AuthHelper::can('project.advance.create');

    $projectModel = $this->model('Project');
    $advanceModel = $this->model('ProjectAdvance');
    $ledger = $this->model('ProjectLedger');

    $data['project'] = $projectModel->getById($project_id);

    if (!$data['project']) {
        FlashHelper::error("Project not found");
        header("Location: " . URLROOT . "/projects");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if ($_POST['amount'] <= 0) {
            FlashHelper::error("Invalid amount");
            header("Location: " . URLROOT . "/projectadvance/create/$project_id");
            exit;
        }

        // 1. Save advance
        $advance_id = $advanceModel->addAdvance([
            'project_id' => $project_id,
            'amount' => $_POST['amount'],
            'payment_method' => $_POST['payment_method'] ?? null,
            'reference' => $_POST['reference'] ?? null,
            'notes' => $_POST['notes'] ?? null,
            'advance_date' => $_POST['advance_date'] ?? date('Y-m-d')
        ]);

        // 2. WRITE TO LEDGER (SOURCE OF TRUTH)
        $ledger->addEntry([
            'project_id'  => $project_id,
            'entry_type'  => 'advance',
            'ref_table'   => 'project_advances',
            'ref_id'      => $advance_id,
            'description' => $_POST['reference'],
            'debit'       => 0,
            'credit'      => $_POST['amount']
        ]);

        FlashHelper::success("Advance recorded successfully");

        header("Location: " . URLROOT . "/projectadvance/list/$project_id");
        exit;
    }

    $this->view('project-costs/advance_create', $data);
}

    public function list($project_id)
    {
        AuthHelper::can('project.advance.view');

        $advanceModel = $this->model('ProjectAdvance');
        $projectModel = $this->model('Project');

        $data['project'] = $projectModel->getById($project_id);
        $data['advances'] = $advanceModel->getAdvancesByProject($project_id);
        $data['balance'] = $advanceModel->getProjectBalance($project_id);

        $this->view('project-costs/advance_list', $data);
    }

    public function runAutoSettlement($project_id)
{
    $advances = $this->db->query(
        "SELECT * FROM project_advances WHERE project_id = ? ORDER BY advance_date ASC",
        [$project_id]
    )->fetchAll();

    $costs = $this->db->query(
        "SELECT id, total_cost AS amount, created_at 
         FROM project_costs 
         WHERE project_id = ? 
         ORDER BY created_at ASC",
        [$project_id]
    )->fetchAll();

    // reset previous settlements
    $this->db->query(
        "DELETE FROM project_settlements WHERE project_id = ?",
        [$project_id]
    );

    $advanceIndex = 0;
    $costIndex = 0;

    while ($advanceIndex < count($advances) && $costIndex < count($costs)) {

        $advance = $advances[$advanceIndex];
        $cost = $costs[$costIndex];

        $remainingAdvance = $advance->amount;
        $remainingCost = $cost->amount;

        while ($remainingAdvance > 0 && $costIndex < count($costs)) {

            if ($remainingAdvance >= $remainingCost) {

                // FULL COST COVERED
                $this->insertSettlement(
                    $project_id,
                    $advance->id,
                    $cost->id,
                    $remainingCost
                );

                $remainingAdvance -= $remainingCost;
                $costIndex++;
                $remainingCost = $costs[$costIndex]->amount ?? 0;

            } else {

                // PARTIAL COVER
                $this->insertSettlement(
                    $project_id,
                    $advance->id,
                    $cost->id,
                    $remainingAdvance
                );

                $costs[$costIndex]->amount -= $remainingAdvance;
                $remainingAdvance = 0;
            }
        }

        $advanceIndex++;
    }
}
}