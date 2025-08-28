<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Appointment;
use Exception;

class AppointmentController
{
    private Appointment $appointment;

    public function __construct()
    {
        $this->appointment = new Appointment();
    }

    /**
     * Retrieve all appointments
     */
    public function index(): array
    {
        return $this->appointment->getAll();
    }

    /**
     * Retrieve details of a specific appointment
     */
    public function show(int $id): array
    {
        $data = $this->appointment->findById($id);
        if (!$data) {
            throw new Exception("Appointment ID {$id} not found");
        }
        return $data;
    }

    /**
     * Create a new appointment
     */
    public function store(array $data): bool
    {
        return $this->appointment->create($data);
    }

    /**
     * Update an existing appointment
     */
    public function update(int $id, array $data): bool
    {
        if (!$this->appointment->findById($id)) {
            throw new Exception("Cannot update. Appointment ID {$id} does not exist.");
        }
        return $this->appointment->update($id, $data);
    }

    /**
     * Delete an appointment
     */
    public function destroy(int $id): bool
    {
        if (!$this->appointment->findById($id)) {
            throw new Exception("Cannot delete. Appointment ID {$id} does not exist.");
        }
        return $this->appointment->delete($id);
    }

    /**
     * Retrieve all appointments for a specific doctor
     */
    public function getByDoctor(int $doctorId): array
    {
        return $this->appointment->findByDoctorId($doctorId);
    }

    /**
     * Retrieve all appointments for a specific patient
     */
    public function getByPatient(int $patientId): array
    {
        return $this->appointment->findByPatientId($patientId);
    }
}
