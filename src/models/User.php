<?php
declare(strict_types=1);

namespace Models;

require_once __DIR__ . '/../core/Database.php';

use Core\Database;
use PDO;
use PDOException;

class User
{
    private PDO $db;

    public function __construct()
    {
        // Get Database instance
        $dbInstance = Database::getInstance();

        // Get PDO connection from instance
        $this->db = $dbInstance->getConnection();
    }

    // Get all users
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM users");
        return $stmt->fetchAll();
    }

    // Find user by ID
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result !== false ? $result : null;
    }

    // Find user by email
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $result = $stmt->fetch();
        return $result !== false ? $result : null;
    }

    // Create new user
    public function create(array $data): bool
    {
        try {
            $sql = "INSERT INTO users (email, password_hash, full_name, role_id, is_active, created_at, updated_at)
                    VALUES (:email, :password_hash, :full_name, :role_id, :is_active, NOW(), NOW())";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':email' => $data['email'],
                ':password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
                ':full_name' => $data['full_name'],
                ':role_id' => $data['role_id'] ?? 2,
                ':is_active' => $data['is_active'] ?? 1
            ]);
        } catch (PDOException $e) {
            error_log("User create error: " . $e->getMessage());
            return false;
        }
    }

    // Update user
    public function update(int $id, array $data): bool
    {
        try {
            $sql = "UPDATE users 
                    SET email = :email, full_name = :full_name, role_id = :role_id, is_active = :is_active, updated_at = NOW()
                    WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':email' => $data['email'],
                ':full_name' => $data['full_name'],
                ':role_id' => $data['role_id'],
                ':is_active' => $data['is_active'],
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            error_log("User update error: " . $e->getMessage());
            return false;
        }
    }

    // Delete user
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("User delete error: " . $e->getMessage());
            return false;
        }
    }
}
