<?php

class ProjectCosts extends Controller
{
    public function index($project_id = null)
    {
        $costModel = $this->model('ProjectCost');
        $projectModel = $this->model('Project');

        // =========================
        // 1. GLOBAL COSTS PAGE
        // =========================
        if (!$project_id) {

            $data['recent_costs'] = $costModel->getRecentCosts();
            $data['total_costs'] = $costModel->getTotalCosts();

            // ❌ DO NOT use project_id here
            $this->view('costs/index', $data);
            return;
        }

        // =========================
        // 2. PROJECT COSTS PAGE
        // =========================
        $project = $projectModel->getById($project_id);

        if (!$project) {
            header('Location: ' . URLROOT . '/projects');
            exit;
        }
        $data['project'] = $project;
        $data['project_id'] = $project_id;

        // PROJECT SCOPES
        $data['project_scopes'] = $projectModel->getProjectScopes($project_id);

        $data['costs'] = $costModel->getProjectCosts($project_id);
        $data['total_cost'] = $costModel->getTotalCost($project_id);

        $this->view('project-costs/index', $data);
    }

    public function create($project_id = null)
    {
        if (!$project_id) {
            header('Location: ' . URLROOT . '/projects');
            exit;
        }

        // ============================================
        // HANDLE FORM SUBMISSION
        // ============================================

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            try {

                $this->service('ProjectCost')->create([

                    'project_id'   => $project_id,

                    'cost_type'    => $_POST['cost_type'] ?? null,

                    'description'  => trim($_POST['description'] ?? ''),

                    'quantity'     => (float)($_POST['quantity'] ?? 0),

                    'unit_price'   => (float)($_POST['unit_price'] ?? 0),

                    'inventory_id' => !empty($_POST['inventory_id'])
                        ? (int)$_POST['inventory_id']
                        : null,

                    'location_id'  => !empty($_POST['location_id'])
                        ? (int)$_POST['location_id']
                        : null

                ]);

                FlashHelper::success(
                    'Project cost added successfully.'
                );

                header(
                    'Location: ' .
                        URLROOT .
                        '/project-costs/' .
                        $project_id
                );

                exit;
            } catch (Exception $e) {

                FlashHelper::error(
                    $e->getMessage()
                );

                header(
                    'Location: ' .
                        URLROOT .
                        '/project-costs/create/' .
                        $project_id
                );

                exit;
            }
        }

        // ============================================
        // DISPLAY FORM (GET)
        // ============================================

        $projectModel = $this->model('Project');

        $project = $projectModel->getById($project_id);

        if (!$project) {
            header('Location: ' . URLROOT . '/projects');
            exit;
        }

        $inventoryModel = $this->model('Inventory');
        $locationModel  = $this->model('InventoryLocation');

        $data['project']    = $project;
        $data['project_id'] = $project_id;
        $data['inventory']  = $inventoryModel->getAll();
        $data['locations']  = $locationModel->getAll();

        $this->view(
            'project-costs/create',
            $data
        );
    }

    // EDIT Project Costs
    public function edit($id)
    {
        $costModel = $this->model('ProjectCost');
        $inventoryModel = $this->model('Inventory');
        $locationModel = $this->model('InventoryLocation');

        // ==================================================
        // LOAD EXISTING COST
        // ==================================================

        $cost = $costModel->getById($id);

        if (!$cost) {
            header('Location: ' . URLROOT . '/project-costs');
            exit;
        }

        // ==================================================
        // HANDLE POST
        // ==================================================

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            try {

                $this->service('ProjectCost')->update(
                    (int)$id,
                    [
                        'description' => trim(
                            $_POST['description'] ?? ''
                        ),

                        'quantity' => (float)(
                            $_POST['quantity'] ?? 0
                        ),

                        'unit_price' => (float)(
                            $_POST['unit_price'] ?? 0
                        )
                    ]
                );

                FlashHelper::success(
                    'Project cost updated successfully.'
                );
            } catch (Throwable $e) {

                FlashHelper::error(
                    $e->getMessage()
                );
            }

            header(
                'Location: ' .
                    URLROOT .
                    '/project-costs/' .
                    $cost->project_id
            );

            exit;
        }

        // ==================================================
        // LOAD EDIT FORM
        // ==================================================

        $data['cost'] = $cost;

        $data['project_id'] =
            $cost->project_id;

        $data['inventory'] =
            $inventoryModel->getAll();

        $data['locations'] =
            $locationModel->getAll();

        $this->view(
            'project-costs/edit',
            $data
        );
    }

    public function delete($id)
    {
        try {

            $cost = $this->model('ProjectCost')->getById($id);

            if (!$cost) {
                FlashHelper::error('Project cost not found.');

                header(
                    'Location: ' .
                        URLROOT .
                        '/project-costs'
                );

                exit;
            }

            $project_id = $cost->project_id;

            /*
        |--------------------------------------------------------------------------
        | Project Cost Service
        |--------------------------------------------------------------------------
        */

            $this->service('ProjectCost')->delete((int)$id);

            FlashHelper::success(
                'Project cost deleted successfully.'
            );

            header(
                'Location: ' .
                    URLROOT .
                    '/project-costs/' .
                    $project_id
            );

            exit;
        } catch (Throwable $e) {

            FlashHelper::error(
                $e->getMessage()
            );

            header(
                'Location: ' .
                    URLROOT .
                    '/project-costs'
            );

            exit;
        }
    }

    public function getInventoryLocations(int $inventory_id)
    {
        header('Content-Type: application/json');

        $stockModel =
            $this->model('InventoryLocationStock');

        $locations =
            $stockModel->getItemLocations($inventory_id);

        echo json_encode($locations);

        exit;
    }

    public function ledger($project_id)
    {
        $ledgerModel = $this->model('ProjectLedger');
        $projectModel = $this->model('Project');

        $data['ledger'] = $ledgerModel->getLedger($project_id);

        $data['project'] = $projectModel->getById($project_id);

        $this->view('project-costs/ledger', $data);
    }

    public function financeDashboard($project_id)
    {
        AuthHelper::can('projects.view');

        $model = $this->model('ProjectCost');
        $projectModel = $this->model('Project');

        $project = $projectModel->getById($project_id);

        if (!$project) {
            FlashHelper::error("Project not found");
            header('Location: ' . URLROOT . '/projects');
            exit;
        }

        // show on the Dashboard
        $summary = $model->getFinanceSummary($project_id);

        $summary['budget_used'] =
            $project->budget > 0
            ? ($summary['costs'] / $project->budget) * 100
            : 0;

        $summary['budget_remaining'] =
            $project->budget - $summary['costs'];

        $startDate = strtotime($project->created_at);
        $endDate   = strtotime($project->deadline);
        $today     = time();

        $totalDays = max(1, ($endDate - $startDate) / 86400);
        $elapsedDays = ($today - $startDate) / 86400;

        $summary['timeline_used'] =
            max(0, min(100, ($elapsedDays / $totalDays) * 100));

        $summary['days_remaining'] =
            max(0, ceil(($endDate - $today) / 86400));

        /*
|--------------------------------------------------------------------------
| NEW KPI
|--------------------------------------------------------------------------
*/

        $summary['advance_funding'] =
            $project->budget > 0
            ? ($summary['advances'] / $project->budget) * 100
            : 0;

        $data = [
            'project' => $project,
            'summary' => $summary
        ];

        $this->view('project-costs/finance_dashboard', $data);
    }


    public function finance($project_id)
    {
        AuthHelper::can('project.finance.view');

        $projectModel = $this->model('Project');
        $ledgerModel = $this->model('ProjectLedger');
        $advanceModel = $this->model('ProjectAdvance');

        $data['project'] = $projectModel->getById($project_id);

        $data['balance'] =
            $ledgerModel->getProjectSummary($project_id);

        $data['advances'] =
            $advanceModel->getAdvancesByProject($project_id);

        $this->view('project-costs/advance_list', $data);
    }

    public function ledgerReport($project_id)
    {
        $projectModel = $this->model('Project');
        $ledgerModel = $this->model('ProjectLedger');

        $data['project'] = $projectModel->getById($project_id);

        $data['ledger'] = $ledgerModel->getLedger($project_id);

        $data['summary'] = $ledgerModel->getProjectSummary($project_id);

        $this->view(
            'project-costs/ledger_report',
            $data,
            false
        );
    }
}
