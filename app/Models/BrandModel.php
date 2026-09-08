<?php

class BrandModel
{
    protected $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getAll()
    {
        $this->db->query("SELECT * FROM brands ORDER BY brand_name ASC");
        return $this->db->resultSet();  
    }
}