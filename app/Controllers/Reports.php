<?php
class Reports extends Controller {

    public function index()
    {
        AuthHelper::can('reports.view');

        $reportModel = $this->model('Report');
        $advanceModel = $this->model('ProjectAdvance');
        $costModel    = $this->model('ProjectCost');

        $data['summary'] = $reportModel->getProfitLossSummary();
        $data['cost_vs_budget'] = $reportModel->getCostVsBudget();
        $data['monthly_costs'] = $reportModel->getMonthlyCosts();

        // FIX: ADD THESE
        $data['global_advances'] = $advanceModel->getTotalAdvances();
        $data['global_costs']    = $costModel->getTotalCosts();
        $data['global_balance']  = $data['global_advances'] - $data['global_costs'];

        $data['title'] = 'Financial Reports';
        $data['dashboard'] = $reportModel->getPortfolioDashboard();

        $this->view('reports/index', $data);
    }
}
