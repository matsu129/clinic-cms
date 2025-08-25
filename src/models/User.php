<?php
require_once __DIR__ . '/../core/Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // find by id
    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }

    // find by Email
    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->query($sql, [$email]);
        return $stmt->fetch();
    }

    // create new account
    public function create($email, $password, $fullName, $roleId = 1) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (email, password_hash, full_name, role_id, is_active, created_at, updated_at)
                VALUES (?, ?, ?, ?, 1, NOW(), NOW())";
        return $this->db->query($sql, [$email, $passwordHash, $fullName, $roleId]);
    }
}
?>
