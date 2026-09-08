<?php
require_once '../app/Core/Model.php';
class ProjectFinanceModel extends Model
{
    public function getProjectSummary($project_id)
    {
        $in = $this->db->query("
        SELECT COALESCE(SUM(amount),0) AS total_in
        FROM project_advances
        WHERE project_id = ?
        AND status = 'received'
    ", [$project_id])->fetch()->total_in;

        $out = $this->db->query("
        SELECT COALESCE(SUM(total_cost),0) AS total_out
        FROM project_costs
        WHERE project_id = ?
    ", [$project_id])->fetch()->total_out;

        return (object)[
            'total_in' => $in,
            'total_out' => $out,
            'balance' => $in - $out
        ];
    }
}
