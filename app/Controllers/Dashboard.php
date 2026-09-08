<?php

class Dashboard extends Controller
{
    public function index()
    {
        // ==================================================
        // MODELS
        // ==================================================

        $projectModel   = $this->model('Project');
        $inventoryModel = $this->model('Inventory');
        $serviceModel   = $this->model('Service');
        $customerModel  = $this->model('Customer');
        $costModel      = $this->model('ProjectCost');
        $financeModel   = $this->model('ProjectAdvance');
        $reportModel    = $this->model('Report');
        $supplierLedgerModel = $this->model('SupplierLedger');
        $resourceRequisitionModel = $this->model('ResourceRequisition');
        $portfolio      = $reportModel->getPortfolioDashboard();
        $data['portfolio'] = $portfolio;

        $data['total_portfolio_budget'] =
            (float)$portfolio->total_budget;

        $data['total_project_costs'] =
            (float)$portfolio->total_costs;

        $data['remaining_budget'] =
            $data['total_portfolio_budget']
            - $data['total_project_costs'];

        // ==================================================
        // PROJECTS
        // ==================================================

        $all_projects = $projectModel->getProjects();

        $data['total_projects'] = count($all_projects);

        // Active Projects
        $active_projects = array_filter($all_projects, function ($p) {
            return in_array($p->status, [
                'in_progress',
                'testing',
                'planning'
            ]);
        });

        $data['active_projects'] =
            array_values($active_projects);

        // Remaining Portfolio Budget

        $data['remaining_budget'] =
            $data['total_portfolio_budget'] -
            $data['total_project_costs'];

        // ==================================================
        // INVENTORY / CUSTOMERS / SERVICES
        // ==================================================

        $data['low_stock'] =
            $inventoryModel->getLowStockAlerts();

        $data['customers'] =
            $customerModel->getCustomers('active');

        $data['resource_requisitions'] =
            $resourceRequisitionModel->getAll();
        // ==================================================
        // GLOBAL FINANCE
        // ==================================================

        $data['global_advances'] =
            $financeModel->getTotalAdvances();

        $data['global_costs'] =
            $costModel->getTotalCosts();

        $data['global_balance'] =
            $data['global_advances'] -
            $data['global_costs'];

        $data['total_supplier_outstanding'] =
            $supplierLedgerModel->getTotalOutstanding();

        // ==================================================
        // ERP FINANCIAL KPIs
        // ==================================================

        $profitLoss =
            $reportModel->getProfitLossSummary();

        $data['total_revenue'] =
            $profitLoss['revenue'];

        $data['total_costs_all'] =
            $profitLoss['costs'];

        $data['net_profit'] =
            $data['total_revenue'] -
            $data['total_costs_all'];

        // ==================================================
        // DEBUG
        // ==================================================

        error_log(
            "Dashboard Debug - " .
                "Projects: {$data['total_projects']}, " .
                "Active: " . count($data['active_projects']) . ", " .
                "Low Stock: " . count($data['low_stock']) . ", " .
                "Customers: " . count($data['customers']) . ", " .
                // "Resource Requisitions: " . count($data['resource_requisitions']) . ", " .
                "Project Costs: {$data['total_project_costs']}"
        );

        // ==================================================
        // VIEW
        // ==================================================

        $data['title'] = 'Dashboard';

        $this->view('dashboard/index', $data);
    }

    public function financeDashboard($project_id)
    {
        $model = $this->model('ProjectCostsModel');

        $data['ledger'] = $model->getProjectLedger($project_id);

        $data['total_advances'] = $model->getTotalAdvances($project_id);

        $data['total_costs'] = $model->getTotalCosts($project_id);

        $data['balance'] =
            $data['total_advances'] -
            $data['total_costs'];

        $this->view('project-costs/finance_dashboard', $data);
    }
}
