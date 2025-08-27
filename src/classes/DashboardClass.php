<?php
class Dashboard {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getData() {
      $patients = $this->pdo->query("
            SELECT id, full_name, dob, gender, phone, email 
            FROM patients
        ")->fetchAll(PDO::FETCH_ASSOC);

         $appointments = $this->pdo->query("
            SELECT id, patient_id, doctor_id, scheduled_at, status, notes 
            FROM appointments
        ")->fetchAll(PDO::FETCH_ASSOC);

        $notes = $this->pdo->query("
            SELECT id, patient_id, note_text, created_by, created_at 
            FROM medical_notes
        ")->fetchAll(PDO::FETCH_ASSOC);

        return [
            'patients' => $patients,
            'appointments' => $appointments,
            'notes' => $notes
        ];
    }
}
