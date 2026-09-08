<?php
require_once '../app/Core/Model.php';

class UserModel extends Model
{

    public function login($email, $password)
    {
        $user = $this->db->query(
            "SELECT * FROM users WHERE email = ?",
            [$email]
        )->fetch();

        if (!$user || !password_verify($password, $user->password)) {
            return false;
        }

        // BASIC SESSION
        $_SESSION['user_id']   = $user->id;
        $_SESSION['user_name'] = $user->name; // use to display name (short) in the navbar
        $_SESSION['full_name'] = $user->full_name; // use to display (full name)
        $_SESSION['role_id']   = $user->role_id;

        // role name
        $role = $this->db->query(
            "SELECT name FROM roles WHERE id = ?",
            [$user->role_id]
        )->fetch();

        $_SESSION['role_name'] = $role->name ?? '';

        // =========================
        // LOCATION SYSTEM (FIXED)
        // =========================

        $locations = $this->db->query(
            "SELECT location_id FROM user_locations WHERE user_id = ?",
            [$user->id]
        )->fetchAll();

        $allowed = array_map(fn($l) => $l->location_id, $locations);

        $_SESSION['allowed_locations'] = $allowed;

        $default = $user->default_location_id;
        $last    = $user->last_location_id;

        $active = null;

        if ($last && in_array($last, $allowed)) {
            $active = $last;
        } elseif ($default && in_array($default, $allowed)) {
            $active = $default;
        } elseif (!empty($allowed)) {
            $active = $allowed[0];
        }

        $_SESSION['active_location_id'] = $active;

        return true;
    }

    // Logout
    public function logout()
    {
        session_unset();
        session_destroy();
    }

    public function getById($id)
    {
        return $this->db->query(
            "SELECT * FROM users WHERE id = ?",
            [$id]
        )->fetch();
    }

    public function getAllUsers()
    {
        return $this->db->query(
            "SELECT u.*, r.name as role_name
         FROM users u
         JOIN roles r ON u.role_id = r.id
         ORDER BY u.created_at DESC"
        )->fetchAll();
    }

    public function createUser($data)
    {
        $this->db->query(
            "
        INSERT INTO users
        (
            full_name,
            name,
            email,
            password,
            role_id,
            default_location_id
        )
        VALUES
        (
            ?, ?, ?, ?, ?, ?
        )
        ",
            [
                $data['full_name'],
                $data['name'],
                $data['email'],
                password_hash(
                    $data['password'],
                    PASSWORD_DEFAULT
                ),
                $data['role_id'],
                $data['default_location_id'] ?? null
            ]
        );

        return $this->db->lastInsertId();
    }


    public function update($id, $data)
    {
        // Get existing user
        $existing = $this->getById($id);

        return $this->db->query(
            "
        UPDATE users
        SET
            name = ?,
            email = ?,
            role_id = ?,
            password = ?,
            default_location_id = ?
        WHERE id = ?
        ",
            [
                $data['name'] ?? $existing->name,
                $data['email'] ?? $existing->email,
                $data['role_id'] ?? $existing->role_id,
                $data['password'] ?? $existing->password,
                $data['default_location_id'] ?? $existing->default_location_id,
                $id
            ]
        );
    }

    public function delete($id)
    {
        return $this->db->query("DELETE FROM users WHERE id = ?", [$id]);
    }

    public function countAdmins()
    {
        $result = $this->db->query(
            "
        SELECT COUNT(*) as total

        FROM users u

        JOIN roles r
            ON r.id = u.role_id

        WHERE UPPER(r.name) = 'ADMIN'
        "
        )->fetch();

        return (int)$result->total;
    }

    public function getRoleName($role_id)
    {
        $role = $this->db->query(
            "
        SELECT name
        FROM roles
        WHERE id = ?
        ",
            [$role_id]
        )->fetch();

        return strtoupper($role->name ?? '');
    }

    public function getUserLocations($user_id)
    {
        return $this->db->query(
            "
        SELECT l.*
        FROM inventory_locations l
        JOIN user_locations ul
            ON ul.location_id = l.id
        WHERE ul.user_id = ?
        ORDER BY l.name
        ",
            [$user_id]
        )->fetchAll();
    }

    public function setLastLocation($user_id, $location_id)
    {
        return $this->db->query(
            "UPDATE users 
         SET last_location_id = ? 
         WHERE id = ?",
            [$location_id, $user_id]
        );
    }

    public function getLocationIds($user_id)
    {
        $rows = $this->db->query(
            "
        SELECT location_id
        FROM user_locations
        WHERE user_id = ?
        ",
            [$user_id]
        )->fetchAll();

        return array_map(
            fn($r) => $r->location_id,
            $rows
        );
    }

    public function saveLocations($user_id, $locations)
    {
        $this->db->query(
            "DELETE FROM user_locations WHERE user_id = ?",
            [$user_id]
        );

        foreach ($locations as $location_id) {

            $result = $this->db->query(
                "INSERT INTO user_locations (user_id, location_id) VALUES (?, ?)",
                [$user_id, $location_id]
            );

            if (!$result) {
                die("Insert failed for location: $location_id");
            }
        }
    }

   public function getUserById($id)
{
    $id = (int)$id;

    $this->db->query("
        SELECT u.*, r.name AS role_name
        FROM users u
        LEFT JOIN roles r ON u.role_id = r.id
        WHERE u.id = $id
    ");

    return $this->db->single();
}

public function getProjectManagers()
{
    return $this->db->query("
        SELECT
            u.id,
            u.full_name,
            r.name AS role_name
        FROM users u
        INNER JOIN roles r
            ON u.role_id = r.id
        WHERE r.name IN ('ADMIN', 'MANAGER')
        ORDER BY u.full_name
    ")->fetchAll(PDO::FETCH_OBJ);
}

    }
