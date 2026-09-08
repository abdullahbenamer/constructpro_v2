<?php
require_once '../app/Core/Model.php';

class ProjectModel extends Model
{

    public function getProjects($filters = [])
    {
        $sql = "
    SELECT 
        p.*,
        c.company AS customer_name,
        COALESCE(SUM(pc.total_cost), 0) AS total_cost,
        COALESCE(COUNT(DISTINCT d.id), 0) AS document_count
    FROM projects p
    LEFT JOIN customers c ON c.id = p.customer_id
    LEFT JOIN project_costs pc ON pc.project_id = p.id
    LEFT JOIN project_documents d ON d.project_id = p.id
    WHERE p.is_archived = 0
    GROUP BY p.id
    ORDER BY p.id DESC
    ";

        return $this->db->query($sql)->fetchAll();
    }

    public function getArchivedProjects()
    {
        return $this->db->query(
            "SELECT p.*, c.company as customer_name
         FROM projects p
         LEFT JOIN customers c
            ON c.id = p.customer_id
         WHERE p.is_archived = 1
         ORDER BY p.created_at DESC"
        )->fetchAll();
    }
    // CRUD Methods
    public function getAll()
    {
        return $this->getProjects();
    }

  public function getById($id)
{
    $stmt = $this->db->query(
        "SELECT
            p.*,
            c.company AS customer_name,
            u.full_name AS project_manager_name
         FROM projects p
         LEFT JOIN customers c
            ON p.customer_id = c.id
         LEFT JOIN users u
            ON p.project_manager_id = u.id
         WHERE p.id = ?",
        [$id]
    );

    $result = $stmt->fetch();

    // SAFE: Return null object or redirect
    if (!$result) {
        header('Location: ' . URLROOT . '/projects');
        exit;
    }

    return $result;
}

    public function create($data)
    {
        $this->db->beginTransaction();

        try {

            /*
        |--------------------------------------------------------------------------
        | 1. CREATE PROJECT
        |--------------------------------------------------------------------------
        */

            $this->db->query(
                "INSERT INTO projects
(
    customer_id,
    title,
    project_type,
    description,
    site_location,
    start_date,
    deadline,
    project_manager_id,
    contract_number,
    priority,
    status,
    budget
)
            VALUES
           (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $data['customer_id'],
                    $data['title'],
                    $data['project_type'],
                    $data['description'],
                    $data['site_location'],
                    $data['start_date'],
                    $data['deadline'],
                    $data['project_manager_id'],
                    $data['contract_number'],
                    $data['priority'],
                    $data['status'],
                    $data['budget']
                ]
            );

            $projectId = (int)$this->db->lastInsertId();

            if ($projectId <= 0) {
                throw new Exception('Unable to create project.');
            }
            // Create Project Code
            $projectCode = 'PRJ-' . date('Y') . '-' . str_pad(
                $projectId,
                4,
                '0',
                STR_PAD_LEFT
            );

            $this->db->query(
                "UPDATE projects
                SET project_code = ?
                WHERE id = ?",
                [
                    $projectCode,
                    $projectId
                ]
            );

            /*
|--------------------------------------------------------------------------
| 2. SAVE PROJECT SCOPES
|--------------------------------------------------------------------------
*/

            if (!empty($data['scopes']) && is_array($data['scopes'])) {

                foreach ($data['scopes'] as $scope) {

                    $scope = trim($scope);

                    if ($scope === '') {
                        continue;
                    }

                    $this->db->query(
                        "INSERT INTO project_scopes
             (project_id, scope)
             VALUES (?, ?)",
                        [
                            $projectId,
                            $scope
                        ]
                    );
                }
            }

            /*
        |--------------------------------------------------------------------------
        | 2. CREATE PROJECT INVENTORY LOCATION
        |--------------------------------------------------------------------------
        */

            $locationCode = $projectCode;

            $locationName =
                'PROJECT - ' .
                $projectCode .
                ' # ' .
                trim($data['title']);

            $this->db->query(
                "INSERT INTO inventory_locations
            (
                code,
                name,
                address,
                notes
            )
            VALUES (?, ?, ?, ?)",
                [
                    $locationCode,
                    $locationName,
                    trim($data['site_location'] ?? ''),
                    'Project inventory location'
                ]
            );

            $locationId = (int)$this->db->lastInsertId();

            if ($locationId <= 0) {
                throw new Exception(
                    'Unable to create project inventory location.'
                );
            }

            /*
        |--------------------------------------------------------------------------
        | 3. LINK PROJECT TO ITS LOCATION
        |--------------------------------------------------------------------------
        */

            $this->db->query(
                "UPDATE projects
             SET location_id = ?
             WHERE id = ?",
                [
                    $locationId,
                    $projectId
                ]
            );

            $this->db->commit();

            return $projectId;
        } catch (Throwable $e) {

            $this->db->rollBack();

            throw $e;
        }
    }

    public function getProjectScopes($projectId)
    {
        return $this->db->query(
            "SELECT scope
         FROM project_scopes
         WHERE project_id = ?
         ORDER BY id",
            [$projectId]
        )->fetchAll();
    }

    public function update($id, $data)
    {
        $this->db->beginTransaction();

        try {

            // UPDATE PROJECT
            $stmt = $this->db->query(
                "UPDATE projects SET
            customer_id       = ?,
            title             = ?,
            project_type      = ?,
            description       = ?,
            site_location     = ?,
            start_date        = ?,
            deadline          = ?,
            project_manager_id = ?,
            contract_number   = ?,
            priority          = ?,
            status            = ?,
            budget            = ?
         WHERE id = ?",
                [
                    $data['customer_id'],
                    $data['title'],
                    $data['project_type'],
                    $data['description'],
                    $data['site_location'],
                    $data['start_date'],
                    $data['deadline'],
                    $data['project_manager_id'],
                    $data['contract_number'],
                    $data['priority'],
                    $data['status'],
                    $data['budget'],
                    $id
                ]
            );

            // DELETE OLD SCOPES
            $this->db->query(
                "DELETE FROM project_scopes
         WHERE project_id = ?",
                [$id]
            );

            // INSERT NEW SCOPES
            if (!empty($data['scopes']) && is_array($data['scopes'])) {

                foreach ($data['scopes'] as $scope) {

                    $scope = trim($scope);

                    if ($scope === '') {
                        continue;
                    }

                    $this->db->query(
                        "INSERT INTO project_scopes
                 (project_id, scope)
                 VALUES (?, ?)",
                        [$id, $scope]
                    );
                }
            }

            $this->db->commit();

            return true;
        } catch (Throwable $e) {

            $this->db->rollBack();

            throw $e;
        }
    }

    // delete Project
    public function delete($id)
    {
        return $this->db->query("DELETE FROM projects WHERE id = ?", [$id])->rowCount() > 0;
    }

    // ARCHIVE Project
    public function archive($id)
    {
        return $this->db->query(
            "UPDATE projects
         SET is_archived = 1
         WHERE id = ?",
            [$id]
        )->rowCount() > 0;
    }

    public function restore($id)
    {
        return $this->db->query(
            "UPDATE projects
         SET is_archived = 0
         WHERE id = ?",
            [$id]
        );
    }
    public function getProjectWithCosts($id)
    {
        $project = $this->getById($id);
        if ($project) {
            require_once '../app/Models/ProjectCostModel.php';
            $costModel = new ProjectCostModel();
            $project->total_cost = $costModel->getTotalCost($id);
            $project->costs = $costModel->getProjectCosts($id);
        }
        return $project;
    }

    public function getTotalBudget()
    {
        $this->db->query("
        SELECT SUM(budget) AS total_budget
        FROM projects
    ");

        return $this->db->single()->total_budget ?? 0;
    }
}
