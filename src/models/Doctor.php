<?php
declare(strict_types=1);

namespace Models;

require_once __DIR__ . '/../core/Database.php';

use Core\Database;
use PDO;
use PDOException;

class Doctor
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Create new doctor
    public function create(array $data): bool
    {
        $sql = "INSERT INTO doctors (user_id, name, specialty, phone, email, created_at, updated_at)
                VALUES (:user_id, :name, :specialty, :phone, :email, NOW(), NOW())";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $data['user_id'] ?? null,
            ':name' => $data['name'],
            ':specialty' => $data['specialty'],
            ':phone' => $data['phone'],
            ':email' => $data['email']
        ]);
    }

    // Get doctor by ID
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM doctors WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    // Get all doctors
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM doctors ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update doctor
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE doctors
                SET user_id = :user_id, name = :name, specialty = :specialty, phone = :phone, email = :email, updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $data['user_id'] ?? null,
            ':name' => $data['name'],
            ':specialty' => $data['specialty'],
            ':phone' => $data['phone'],
            ':email' => $data['email'],
            ':id' => $id
        ]);
    }

    // Delete doctor
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM doctors WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
