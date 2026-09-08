<?php
require_once '../app/Core/Model.php';

class InventoryLocationModel extends Model
{
    
public function getAll()
{
    return $this->db->query("
        SELECT
            l.*,
            u.full_name AS storekeeper,

            COALESCE(
                SUM(ils.quantity),
                0
            ) AS total_stock

        FROM inventory_locations l

        LEFT JOIN users u
            ON u.id = l.storekeeper_id

        LEFT JOIN inventory_location_stock ils
            ON ils.location_id = l.id

        GROUP BY l.id

        ORDER BY l.code
    ")->fetchAll();
}

    public function getById($id)
    {
        return $this->db->query(
            "SELECT * FROM inventory_locations WHERE id = ?",
            [$id]
        )->fetch();
    }

public function create($data)
{
    $code = strtoupper(trim($data['code']));

    $exists = $this->db->query(
        "SELECT id
         FROM inventory_locations
         WHERE UPPER(TRIM(code)) = ?",
        [$code]
    )->fetch();

    if ($exists) {
        return [
            'success' => false,
            'message' => 'Code already exists'
        ];
    }

    $stmt = $this->db->query(
        "INSERT INTO inventory_locations
        (
            code,
            name,
            address,
            storekeeper_id,
            mobile,
            notes
        )
        VALUES (?, ?, ?, ?, ?, ?)",
        [
            $code,
            trim($data['name']),
            trim($data['address'] ?? ''),
            $data['storekeeper_id'] ?: null,
            trim($data['mobile'] ?? ''),
            trim($data['notes'] ?? '')
        ]
    );

    return [
        'success' => $stmt->rowCount() > 0,
        'id'      => $this->db->lastInsertId()
    ];
}

public function getStorekeepers()
{
    return $this->db->query("
        SELECT u.id, u.full_name
        FROM users u
        INNER JOIN roles r ON u.role_id = r.id
        WHERE UPPER(r.name) = 'STOREKEEPER'
        ORDER BY u.full_name
    ")->fetchAll();
}

public function update($id, $data)
{
    $code = strtoupper(trim($data['code']));

    // prevent duplicate code
    $exists = $this->db->query(
        "SELECT id
         FROM inventory_locations
         WHERE UPPER(TRIM(code)) = ?
         AND id <> ?",
        [$code, $id]
    )->fetch();

    if ($exists) {
        return [
            'success' => false,
            'message' => 'Code already exists'
        ];
    }

    $stmt = $this->db->query(
        "UPDATE inventory_locations
         SET
            code = ?,
            name = ?,
            address = ?,
            storekeeper_id = ?,
            mobile = ?,
            notes = ?
         WHERE id = ?",
        [
            $code,
            $data['name'],
            $data['address'] ?? null,
            $data['storekeeper_id'] ?? null,
            $data['mobile'] ?? null,
            $data['notes'] ?? null,
            $id
        ]
    );

    return [
        'success' => true
    ];
}

// check if location has stock/history before allowing deletion
public function hasStock($location_id)
{
    $row = $this->db->query(
        "SELECT COUNT(*)
FROM inventory_location_stock
WHERE location_id = ?",
        [$location_id]
    )->fetch();

    return ($row->qty ?? 0) > 0;
}

public function delete($id)
{
    return $this->db->query(
        "DELETE FROM inventory_locations
         WHERE id = ?",
        [$id]
    );
}

// Mothods for allow access to specific locations
/**
 * Get all users
 */
public function getUsers()
{
    return $this->db->query("
        SELECT id, full_name
        FROM users
        ORDER BY full_name ASC
    ")->fetchAll();
}


/**
 * Get assigned users for a warehouse
 */
public function getLocationUsers($location_id)
{
    return $this->db->query("
        SELECT user_id
        FROM user_locations
        WHERE location_id = ?
    ", [$location_id])->fetchAll();
}


/**
 * Save warehouse team
 */
public function saveLocationUsers($location_id, $users = [])
{
    // Remove old assignments
    $this->db->query("
        DELETE FROM user_locations
        WHERE location_id = ?
    ", [$location_id]);

    // Insert new assignments
    foreach ($users as $user_id) {

        $this->db->query("
            INSERT INTO user_locations (user_id, location_id)
            VALUES (?, ?)
        ", [$user_id, $location_id]);
    }
}


/**
 * Check if user can access warehouse
 */
public function userHasAccess($location_id, $user_id)
{
    $result = $this->db->query("
        SELECT 1
        FROM user_locations
        WHERE location_id = ?
        AND user_id = ?
    ", [$location_id, $user_id])->fetch();

    return (bool)$result;
}

}
