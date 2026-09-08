<?php
require_once '../app/Core/Model.php';
class PermissionModel extends Model {

    public function getAll()
    {
        return $this->db->query(
            "SELECT * FROM permissions ORDER BY name")->fetchAll();
    }

     public function create($name)
    {
        return $this->db->query(
            "INSERT INTO permissions (name) VALUES (?)",
            [$name]
        );
    }

    public function getById($id)
{
    return $this->db->query(
        "SELECT * FROM permissions WHERE id = ?",
        [$id]
    )->fetch();
}

public function update($id, $name)
{
    return $this->db->query(
        "UPDATE permissions SET name = ? WHERE id = ?",
        [$name, $id]
    );
}

public function delete($id)
{
    return $this->db->query(
        "DELETE FROM permissions WHERE id = ?",
        [$id]
    );
}
}