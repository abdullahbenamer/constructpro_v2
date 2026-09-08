<?php
require_once '../app/Core/Model.php';

class ProjectCostModel extends Model {
    public function getTotalCost($project_id) {
        $result = $this->db->query(
            "SELECT COALESCE(SUM(quantity * unit_price), 0) as total FROM project_costs WHERE project_id = ?", 
            [$project_id]
        )->fetch();
        return (float)($result->total ?? 0);
    }

public function getProjectCosts($project_id)
{
    return $this->db->query(
        "
        SELECT 
            pc.*,
            i.name AS item_name,
            i.sku,
            l.code AS location_code,
            l.name AS location_name

        FROM project_costs pc

        LEFT JOIN inventory i 
            ON i.id = pc.inventory_id

        LEFT JOIN inventory_locations l 
            ON l.id = pc.location_id

        WHERE pc.project_id = ?

        ORDER BY pc.created_at DESC
        ",
        [$project_id]
    )->fetchAll();
}
    // NEW: For Costs page
    public function getRecentCosts() {
        return $this->db->query("
            SELECT pc.*, p.title as project_title, i.name as item_name
            FROM project_costs pc
            JOIN projects p ON pc.project_id = p.id
            LEFT JOIN inventory i ON pc.inventory_id = i.id
            ORDER BY pc.created_at DESC
            LIMIT 20
        ")->fetchAll();
    }
    
           public function getTotalCosts()
{
    return $this->db->query("
        SELECT COALESCE(SUM(total_cost),0) AS total
        FROM project_costs
    ")->fetch()->total;
}
    
  public function create($data)
{
    $this->db->query(
        "INSERT INTO project_costs 
        (project_id, cost_type, description, quantity, unit_price, inventory_id, location_id)
        VALUES (?, ?, ?, ?, ?, ?, ?)",
        [
            $data['project_id'],
            $data['cost_type'],
            $data['description'],
            $data['quantity'],
            $data['unit_price'],
            $data['inventory_id'],
            $data['location_id'] ?? null
        ]
    );

    return $this->db->lastInsertId();
}

     // Add to existing ProjectCostModel
 public function getById($id) {
    $stmt = $this->db->query("SELECT * FROM project_costs  WHERE id = ?", [$id]);
    $result = $stmt->fetch();
    return $result ?: null;  // Return null if false
}


//get the Item Locations

public function getInventoryLocations(int $inventory_id)
{
    return $this->db->query("
        SELECT
            ils.location_id,
            ils.quantity,
            l.code,
            l.name

        FROM inventory_location_stock ils

        INNER JOIN inventory_locations l
            ON l.id = ils.location_id

        WHERE ils.inventory_id = ?
        AND ils.quantity > 0
    ", [$inventory_id])->fetchAll();
}

public function update($id, $data) {

    $this->db->query(
        "UPDATE project_costs 
         SET 
         cost_type=?, 
         description=?, 
         quantity=?, 
         unit_price=?, 
         inventory_id=?, 
         location_id=?
         WHERE id=?",
       [
        $data['cost_type'],
        $data['description'],
        $data['quantity'],
        $data['unit_price'],
        $data['inventory_id'] ?? null,
        $data['location_id'] ?? null,
        $id
    ]
    );

      return true; 
}

public function delete($id) {
    return $this->db->query("DELETE FROM project_costs WHERE id = ?", [$id])->rowCount() > 0;
}

public function getInventoryUsage($inventory_id)
{
    return $this->db->query(
        "
        SELECT
            pc.*,
            p.title AS project_title

        FROM project_costs pc

        JOIN projects p
            ON p.id = pc.project_id

        WHERE pc.inventory_id = ?

        ORDER BY pc.created_at DESC
        ",
        [$inventory_id]
    )->fetchAll();
}

public function getProjectLedger($project_id)
{
    return $this->db->query("
        SELECT 
            id,
            'advance' AS entry_type,
            notes AS description,
            0 AS debit,
            amount AS credit,
            created_at
        FROM project_advances
        WHERE project_id = ?

        UNION ALL

        SELECT 
            id,
            'cost' AS entry_type,
            description,
            total_cost AS debit,
            0 AS credit,
            created_at
        FROM project_costs
        WHERE project_id = ?

        ORDER BY created_at ASC
    ", [$project_id, $project_id])->fetchAll();
}


public function getFinanceSummary($project_id)
{
    $advances = $this->db->query("
        SELECT COALESCE(SUM(amount),0) AS total
        FROM project_advances
        WHERE project_id = ?
        AND status = 'received'
    ", [$project_id])->fetch()->total;

    $costs = $this->db->query("
        SELECT COALESCE(SUM(total_cost),0) AS total
        FROM project_costs
        WHERE project_id = ?
    ", [$project_id])->fetch()->total;

    return [
        'advances' => (float)$advances,
        'costs'    => (float)$costs,
        'balance'  => (float)$advances - (float)$costs
    ];
}



}