<?php
declare(strict_types=1);

namespace App\Models;

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/ModelInterface.php';

use Core\Database;
use PDO;
use PDOException;

class Patient implements ModelInterface
{
    private PDO $db;
    private string $table = 'patients';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Create a new patient
     */
    public function create(array $data): bool
    {
        try {
            $sql = "INSERT INTO {$this->table} 
                    (name, dob, gender, phone, email, address, created_at, updated_at)
                    VALUES (:name, :dob, :gender, :phone, :email, :address, NOW(), NOW())";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':name' => $data['name'],
                ':dob' => $data['dob'],
                ':gender' => $data['gender'],
                ':phone' => $data['phone'] ?? null,
                ':email' => $data['email'] ?? null,
                ':address' => $data['address'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log("Patient create error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find a patient by ID
     */
    public function findById(int $id): ?array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Patient findById error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all patients
     */
    public function getAll(): array
    {
        try {
            $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY id ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Patient getAll error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Update a patient record
     */
    public function update(int $id, array $data): bool
    {
        try {
            $sql = "UPDATE {$this->table} 
                    SET name = :name, dob = :dob, gender = :gender, phone = :phone, 
                        email = :email, address = :address, updated_at = NOW()
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
        } catch (PDOException $e) {
            error_log("Patient update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a patient record
     */
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Patient delete error: " . $e->getMessage());
            return false;
        }
    }
}
