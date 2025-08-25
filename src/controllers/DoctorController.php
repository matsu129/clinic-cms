<?php
declare(strict_types=1);

namespace Controllers;

require_once __DIR__ . '/../models/Doctor.php';
require_once __DIR__ . '/../core/AuditLogger.php';

use Models\Doctor;
use Core\AuditLogger;

class DoctorController
{
    private Doctor $doctorModel;

    public function __construct()
    {
        $this->doctorModel = new Doctor();
    }

    // get CurrentUserID
    private function getCurrentUserId(): ?int {
      if (session_status() === PHP_SESSION_NONE) {
          session_start();
      }
      return $_SESSION['user']['id'] ?? null;
    }

    // Get all doctors
    public function index(): array
    {
        $doctors = $this->doctorModel->getAll();
        $userId = $this->getCurrentUserId();
        AuditLogger::logAction("Fetched all doctors", $userId);
        return $doctors;
    }

    // Get doctor by ID
    public function show(int $id): ?array
    {
        $doctor = $this->doctorModel->findById($id);
        $userId = $this->getCurrentUserId();
        AuditLogger::logAction("Fetched doctor ID: $id", $userId);
        return $doctor;
    }

    // Create new doctor
    public function create(array $data): bool
    {
        $result = $this->doctorModel->create($data);
        $userId = $this->getCurrentUserId();
        if ($result) {
            AuditLogger::logAction("Created new doctor: " . ($data['name'] ?? 'Unknown'), $userId);
        }
        return $result;
    }

    // Update doctor
    public function update(int $id, array $data): bool
    {
        $result = $this->doctorModel->update($id, $data);
        $userId = $this->getCurrentUserId();
        if ($result) {
            AuditLogger::logAction("Updated doctor ID: $id", $userId);
        }
        return $result;
    }

    // Delete doctor
    public function delete(int $id): bool
    {
        $result = $this->doctorModel->delete($id);
        $userId = $this->getCurrentUserId();
        if ($result) {
            AuditLogger::logAction("Deleted doctor ID: $id", $userId);
        }
        return $result;
    }
}
