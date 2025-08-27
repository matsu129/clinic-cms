<?php
declare(strict_types=1);

namespace App\Controllers;


require_once __DIR__ . '/../core/AuditLogger.php';
require_once __DIR__ . '/../models/Patient.php';
use App\Models\Patient;
use App\Core\AuditLogger;

class PatientController
{
    private Patient $patientModel;

    public function __construct()
    {
        $this->patientModel = new Patient();
    }
    
    // get CurrentUserID
    private function getCurrentUserId(): ?int {
      if (session_status() === PHP_SESSION_NONE) {
          session_start();
      }
      return $_SESSION['user']['id'] ?? null;
    }

    // Get all patients and show view
    public function index(): void
    {
        $patients = $this->patientModel->getAll();
        $userId = $this->getCurrentUserId();
        AuditLogger::logAction("Fetched all patients", $userId);

        include __DIR__ . '/../views/patient/index.php';
    }

    // Get patient by ID
    public function show(int $id): ?array
    {
        $patient = $this->patientModel->findById($id);
        $userId = $this->getCurrentUserId();
        AuditLogger::logAction("Fetched patient ID: $id" , $userId);
        return $patient;
    }

    // Create new patient
    public function create(array $data): bool
    {
        $result = $this->patientModel->create($data);
        $userId = $this->getCurrentUserId();
        if ($result) {
            AuditLogger::logAction("Created new patient: " . ($data['name'] ?? 'Unknown'), $userId);
        }
        return $result;
    }

    // Update patient
    public function update(int $id, array $data): bool
    {
        $result = $this->patientModel->update($id, $data);
        $userId = $this->getCurrentUserId();
        if ($result) {
            AuditLogger::logAction("Updated patient ID: $id", $userId);
        }
        return $result;
    }

    // Delete patient
    public function delete(int $id): bool
    {
        $result = $this->patientModel->delete($id);
        $userId = $this->getCurrentUserId();
        if ($result) {
            AuditLogger::logAction("Deleted patient ID: $id", $userId);
        }
        return $result;
    }
}
