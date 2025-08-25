<?php
declare(strict_types=1);

namespace Models;

require_once __DIR__ . '/../core/Database.php';

use Core\Database;
use PDO;
use PDOException;

class Patient
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Create a new patient
    public function create(array $data): bool
    {
        $sql = "INSERT INTO patients (name, dob, gender, phone, email, address, created_at, updated_at)
                VALUES (:name, :dob, :gender, :phone, :email, :address, NOW(), NOW())";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name' => $data['name'],
            ':dob' => $data['dob'],
            ':gender' => $data['gender'],
            ':phone' => $data['phone'] ?? null,
            ':email' => $data['email'] ?? null,
            ':address' => $data['address'] ?? null,
        ]);
    }

    // Get patient by ID
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM patients WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    // Get all patients
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM patients ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update patient
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE patients 
                SET name = :name, dob = :dob, gender = :gender, phone = :phone, email = :email, address = :address, updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name' => $data['name'],
            ':dob' => $data['dob'],
            ':gender' => $data['gender'],
            ':phone' => $data['phone'] ?? null,
            ':email' => $data['email'] ?? null,
            ':address' => $data['address'] ?? null,
            ':id' => $id
        ]);
    }

    // Delete patient
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM patients WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
