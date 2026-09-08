<?php
require_once '../app/Core/Model.php';

class ReportModel extends Model
{
   public function getProfitLossSummary()
{
    $revenue = $this->db->query(
        "
        SELECT COALESCE(SUM(budget), 0) AS total
        FROM projects
        WHERE status != 'cancelled'
        "
    )->fetch()->total;

    $costs = $this->db->query(
        "
        SELECT COALESCE(SUM(total_cost), 0) AS total
        FROM project_costs
        "
    )->fetch()->total;

    return [
        'revenue' => (float)$revenue,
        'costs' => (float)$costs,
        'profit' => (float)$revenue - (float)$costs,
        'profit_margin' =>
            $revenue > 0
            ? (
                ((float)$revenue - (float)$costs)
                / (float)$revenue
            ) * 100
            : 0
    ];
}

   public function getCostVsBudget()
{
    return $this->db->query("
        SELECT 
            p.id,
            p.title,
            p.budget,

            COALESCE(SUM(pc.total_cost), 0) AS actual_cost,

            (
                p.budget -
                COALESCE(SUM(pc.total_cost), 0)
            ) AS variance

        FROM projects p

        LEFT JOIN project_costs pc
            ON p.id = pc.project_id

        WHERE p.status != 'cancelled'

        GROUP BY p.id

        ORDER BY variance ASC
    ")->fetchAll();
}

   public function getMonthlyCosts()
{
    return $this->db->query("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') AS month,

            COALESCE(SUM(total_cost), 0) AS total_cost

        FROM project_costs

        GROUP BY DATE_FORMAT(created_at, '%Y-%m')

        ORDER BY month DESC

        LIMIT 12
    ")->fetchAll();
}

    public function getPortfolioSummary()
    {
        return $this->db->query("
        SELECT
            COUNT(*) AS total_projects,

            SUM(CASE WHEN is_archived = 0 THEN 1 ELSE 0 END)
                AS active_projects,

            SUM(CASE WHEN is_archived = 1 THEN 1 ELSE 0 END)
                AS archived_projects,

            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END)
                AS completed_projects,

            COALESCE(SUM(budget),0)
                AS total_budget

        FROM projects
    ")->fetch();
    }

    public function getDeadlineSummary()
    {
        return $this->db->query("
        SELECT

            SUM(
                CASE
                    WHEN deadline < CURDATE()
                    AND status <> 'completed'
                    THEN 1 ELSE 0
                END
            ) AS overdue,

            SUM(
                CASE
                    WHEN deadline BETWEEN CURDATE()
                    AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                    AND status <> 'completed'
                    THEN 1 ELSE 0
                END
            ) AS due_soon

        FROM projects
        WHERE is_archived = 0
    ")->fetch();
    }

    public function getAdvanceSummary()
    {
        return $this->db->query("
        SELECT
            COALESCE(SUM(amount),0) total_advances
        FROM project_advances
        WHERE status='received'
    ")->fetch();
    }

public function getPortfolioDashboard()
{
    return $this->db->query("
        SELECT

            /* ==================================================
               PROJECTS
               ================================================== */

            COUNT(*) AS total_projects,

            SUM(
                CASE
                    WHEN is_archived = 0
                    THEN 1
                    ELSE 0
                END
            ) AS active_projects,

            SUM(
                CASE
                    WHEN is_archived = 1
                    THEN 1
                    ELSE 0
                END
            ) AS archived_projects,

            SUM(
                CASE
                    WHEN status = 'completed'
                    THEN 1
                    ELSE 0
                END
            ) AS completed_projects,


            /* ==================================================
               FINANCE
               ================================================== */

            COALESCE(
                SUM(budget),
                0
            ) AS total_budget,


            COALESCE(
                (
                    SELECT SUM(total_cost)
                    FROM project_costs
                ),
                0
            ) AS total_costs,


            COALESCE(
                (
                    SELECT SUM(amount)
                    FROM project_advances
                    WHERE status = 'received'
                ),
                0
            ) AS total_advances,


            /* ==================================================
               DEADLINES
               ================================================== */

            SUM(
                CASE
                    WHEN deadline < CURDATE()
                    AND status <> 'completed'
                    AND is_archived = 0
                    THEN 1
                    ELSE 0
                END
            ) AS overdue_projects,


            SUM(
                CASE
                    WHEN deadline BETWEEN
                        CURDATE()
                        AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)

                    AND status <> 'completed'
                    AND is_archived = 0

                    THEN 1
                    ELSE 0
                END
            ) AS due_soon_projects


        FROM projects
    ")->fetch();
}
}
