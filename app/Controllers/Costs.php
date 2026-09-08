<?php
class Costs extends Controller {
    public function index() {
        $costModel = $this->model('ProjectCost');
        
        // Use MODEL methods instead of $this->db
        $data['recent_costs'] = $costModel->getRecentCosts();
        $data['total_costs'] = $costModel->getTotalCosts();
        $data['title'] = 'All Project Costs';
        $this->view('costs/index', $data);
    }
}