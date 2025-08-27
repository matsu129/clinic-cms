<?php
declare(strict_types=1);

namespace App\Models;

require_once __DIR__ . '/../Core/Database.php';

use Core\Database;
use PDO;
use PDOException;

class MedicalNote implements ModelInterface
{
    private PDO $db;
    private string $table = 'medical_notes';

    public function __construct()
    {
        $dbInstance = Database::getInstance();
        $this->db = $dbInstance->getConnection();
    }

    /**
     * Create a new medical note
     */
    public function create(array $data): bool
    {
        try {
            $sql = "INSERT INTO {$this->table} (patient_id, doctor_id, note, created_at, updated_at)
                    VALUES (:patient_id, :doctor_id, :note, NOW(), NOW())";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':patient_id' => $data['patient_id'],
                ':doctor_id'  => $data['doctor_id'],
                ':note'       => $data['note']
            ]);
        } catch (PDOException $e) {
            error_log("MedicalNote create error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find a medical note by ID
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result !== false ? $result : null;
    }

    /**
     * Update a medical note by ID
     */
    public function update(int $id, array $data): bool
    {
        try {
            $sql = "UPDATE {$this->table} 
                    SET note = :note, updated_at = NOW()
                    WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':note' => $data['note'],
                ':id'   => $id
            ]);
        } catch (PDOException $e) {
            error_log("MedicalNote update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a medical note by ID
     */
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("MedicalNote delete error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all medical notes
     */
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get medical notes by patient ID
     */
    public function findByPatientId(int $patientId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE patient_id = :patient_id ORDER BY created_at DESC");
        $stmt->execute([':patient_id' => $patientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
