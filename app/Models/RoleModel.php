<?php
require_once '../app/Core/Model.php';
class RoleModel extends Model {

    public function getAll()
    {
        return $this->db->query("SELECT * FROM roles")->fetchAll();
    }

    public function getPermissions($role_id)
{
    return $this->db->query(
        "SELECT permission_id FROM role_permissions WHERE role_id = ?",
        [$role_id]
    )->fetchAll();
}

    public function getPermissionsNames($role_id)
{
    return $this->db->query(
        "SELECT p.name
         FROM permissions p
         JOIN role_permissions rp ON p.id = rp.permission_id
         WHERE rp.role_id = ?",
        [$role_id]
    )->fetchAll();
}

public function assignPermission($role_id, $permission_id)
{
    return $this->db->query(
        "INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (?, ?)",
        [$role_id, $permission_id]
    );
}

    public function clearPermissions($role_id)
{
    return $this->db->query(
        "DELETE FROM role_permissions WHERE role_id = ?",
        [$role_id]
    );
}

    public function create($name)
    {
        return $this->db->query(
            "INSERT INTO roles (name) VALUES (?)",
            [$name]
        );
    }

    // Roles

    public function getById($id)
{
    return $this->db->query(
        "SELECT * FROM roles WHERE id = ?",
        [$id]
    )->fetch();
}

public function update($id, $name)
{
    return $this->db->query(
        "UPDATE roles SET name = ? WHERE id = ?",
        [$name, $id]
    );
}

public function delete($id)
{
    return $this->db->query(
        "DELETE FROM roles WHERE id = ?",
        [$id]
    );

        // ❌ Don’t delete role if used
$count = $this->db->query(
    "SELECT COUNT(*) as total FROM users WHERE role_id = ?",
    [$id]
)->fetch();

if ($count->total > 0) {
    die("❌ Cannot delete role in use");
}

        }
}