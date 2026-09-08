<?php
class CountryModel extends Model
{
    public function getAll()
    {
        return $this->db->query("
            SELECT *
            FROM countries
            ORDER BY country_name
        ")->fetchAll();
    }
}