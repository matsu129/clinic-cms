<?php
declare(strict_types=1);

namespace App\Controllers;

require_once __DIR__ . '/../core/AuditLogger.php';
require_once __DIR__ . '/../models/MedicalNote.php';

use App\Models\MedicalNote;
use App\Core\AuditLogger;

class MedicalNoteController
{
    private MedicalNote $noteModel;

    public function __construct()
    {
        $this->noteModel = new MedicalNote();
    }

    // Get current user ID from session
    private function getCurrentUserId(): ?int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user']['id'] ?? null;
    }

    // Display a list of all medical notes
    public function index()
    {
        $notes = $this->noteModel->getAll();
        $userId = $this->getCurrentUserId();
        AuditLogger::logAction("Fetched all medical notes", $userId);
        return $notes;
    }

    // Retrieve a single medical note by ID
    public function show(int $id): ?array
    {
        $note = $this->noteModel->findById($id);
        $userId = $this->getCurrentUserId();
        AuditLogger::logAction("Fetched medical note ID: $id", $userId);
        return $note;
    }

    // Create a new medical note
    public function create(array $data): bool
    {
        $result = $this->noteModel->create($data);
        $userId = $this->getCurrentUserId();
        if ($result) {
            AuditLogger::logAction(
                "Created new medical note for patient ID: " . ($data['patient_id'] ?? 'Unknown'),
                $userId
            );
        }
        return $result;
    }

    // Update an existing medical note by ID
    public function update(int $id, array $data): bool
    {
        $result = $this->noteModel->update($id, $data);
        $userId = $this->getCurrentUserId();
        if ($result) {
            AuditLogger::logAction("Updated medical note ID: $id", $userId);
        }
        return $result;
    }

    // Delete a medical note by ID
    public function delete(int $id): bool
    {
        $result = $this->noteModel->delete($id);
        $userId = $this->getCurrentUserId();
        if ($result) {
            AuditLogger::logAction("Deleted medical note ID: $id", $userId);
        }
        return $result;
    }

    // Get all medical notes for a specific patient
    public function getByPatient(int $patientId): array
    {
        $notes = $this->noteModel->findByPatientId($patientId);
        $userId = $this->getCurrentUserId();
        AuditLogger::logAction("Fetched medical notes for patient ID: $patientId", $userId);
        return $notes;
    }
}
