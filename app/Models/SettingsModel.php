<?php
require_once '../app/Core/Model.php';
class SettingsModel extends Model
{
    public function get()
    {
        return $this->db->query("SELECT * FROM settings LIMIT 1")->fetch();
    }

  public function save($data)
{
    $exists = $this->db->query("SELECT id FROM settings LIMIT 1")->fetch();

    if ($exists) {
        $this->db->query("
            UPDATE settings SET
                company_name = ?,
                address = ?,
                contacts = ?,
                logo = ?
            WHERE id = ?
        ", [
            $data['company_name'],
            $data['address'],
            $data['contacts'],
            $data['logo'],
            $exists->id
        ]);
    } else {
        $this->db->query("
            INSERT INTO settings (company_name, address, contacts, logo)
            VALUES (?, ?, ?, ?)
        ", [
            $data['company_name'],
            $data['address'],
            $data['contacts'],
            $data['logo']
        ]);
    }
}
}