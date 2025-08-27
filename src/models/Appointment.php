<?php
declare(strict_types=1);

namespace App\Models;

require_once __DIR__ . '/../Core/Database.php';

use Core\Database;
use PDO;
use PDOException;

class Appointment implements ModelInterface
{
    private PDO $db;
    private string $table = 'appointments';

    public function __construct()
    {
        $dbInstance = Database::getInstance();
        $this->db = $dbInstance->getConnection();
    }

    /**
     * Create a new appointment
     */
    public function create(array $data): bool
    {
        try {
            $sql = "INSERT INTO {$this->table} 
                (doctor_id, patient_id, scheduled_at, status, notes, created_by, created_at, updated_at)
                VALUES (:doctor_id, :patient_id, :scheduled_at, :status, :notes, :created_by, NOW(), NOW())";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':doctor_id'    => $data['doctor_id'],
                ':patient_id'   => $data['patient_id'],
                ':scheduled_at'=> $data['scheduled_at'],
                ':status'       => $data['status'],
                ':notes'        => $data['notes'] ?? null,
                ':created_by'   => $data['created_by']
            ]);
        } catch (PDOException $e) {
            error_log("Appointment create error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find an appointment by ID
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Update an appointment by ID
     */
    public function update(int $id, array $data): bool
    {
        try {
            $sql = "UPDATE {$this->table} 
                    SET doctor_id = :doctor_id, 
                        patient_id = :patient_id,
                        scheduled_at = :scheduled_at, 
                        status = :status, 
                        notes = :notes,
                        updated_at = NOW()
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':doctor_id'    => $data['doctor_id'],
                ':patient_id'   => $data['patient_id'],
                ':scheduled_at'=> $data['scheduled_at'],
                ':status'       => $data['status'],
                ':notes'        => $data['notes'] ?? null,
                ':id'           => $id
            ]);
        } catch (PDOException $e) {
            error_log("Appointment update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete an appointment by ID
     */
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Appointment delete error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all appointments with joined patient and doctor names
     */
    public function getAll(): array
    {
        $sql = "SELECT a.*, 
                       p.full_name AS patient_name, 
                       d.name AS doctor_name
                FROM {$this->table} a
                JOIN patients p ON a.patient_id = p.id
                JOIN doctors d ON a.doctor_id = d.id
                ORDER BY a.scheduled_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get appointments by doctor ID
     */
    public function findByDoctorId(int $doctorId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} 
             WHERE doctor_id = :doctor_id 
             ORDER BY scheduled_at DESC"
        );
        $stmt->execute([':doctor_id' => $doctorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get appointments by patient ID
     */
    public function findByPatientId(int $patientId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} 
             WHERE patient_id = :patient_id 
             ORDER BY scheduled_at DESC"
        );
        $stmt->execute([':patient_id' => $patientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
