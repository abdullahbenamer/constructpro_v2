<?php
require_once '../app/Core/Model.php';

class CustomerModel extends Model {
    public function getCustomers($status = null) {
        $sql = "SELECT * FROM customers";
        $params = [];
        
        if ($status) {
            $sql .= " WHERE status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY name ASC";
        
        return $this->db->query($sql, $params)->fetchAll();
    }
    
    // FIXED: No params!
    public function getAll() {
        return $this->db->query("SELECT * FROM customers ORDER BY name ASC")->fetchAll();
    }
    

    public function getById($id) {
        $result = $this->db->query("SELECT * FROM customers WHERE id = ?", [$id])->fetch();
        return $result ?: null;  
    }
    
    public function create($data) {
        return $this->db->query(
            "INSERT INTO customers (name, company, email, phone, address, status) VALUES (?, ?, ?, ?, ?, ?)",
            [$data['name'], $data['company'], $data['email'], $data['phone'], $data['address'], $data['status']]
        )->rowCount() > 0;
    }
    
    public function update($id, $data) {
        return $this->db->query(
            "UPDATE customers SET name=?, company=?, email=?, phone=?, address=?, status=? WHERE id=?",
            [$data['name'], $data['company'], $data['email'], $data['phone'], $data['address'], $data['status'], $id]
        )->rowCount() > 0;
    }
    
    public function delete($id) {
        return $this->db->query("DELETE FROM customers WHERE id = ?", [$id])->rowCount() > 0;
    }

public function getCustomerById($id)
{
    $this->db->query("
        SELECT c.*, u.full_name AS account_manager
        FROM customers c
        LEFT JOIN users u ON c.account_manager_id = u.id
        WHERE c.id = $id
    ");

    return $this->db->single();
}

public function getCustomerProjects($customer_id)
{
    $this->db->query("
        SELECT p.*, 
               u.id AS project_manager_id,
               u.full_name AS project_manager
        FROM projects p
        LEFT JOIN users u ON p.project_manager_id = u.id
        WHERE p.customer_id = $customer_id
        ORDER BY p.id DESC
    ");

    return $this->db->resultSet();
}
}