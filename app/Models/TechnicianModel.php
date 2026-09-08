<?php

require_once '../app/Core/Model.php';

class TechnicianModel extends Model
{
    public function getAll()
    {
        return $this->db->query(
            "SELECT *
             FROM technicians
             ORDER BY name ASC"
        )->fetchAll();
    }

    public function getById($id)
    {
        return $this->db->query(
            "SELECT *
             FROM technicians
             WHERE id = ?",
            [$id]
        )->fetch();
    }
}