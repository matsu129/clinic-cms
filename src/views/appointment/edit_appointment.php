<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../controllers/AppointmentController.php";
require_once __DIR__ . "/../../controllers/DoctorController.php";
require_once __DIR__ . "/../../controllers/PatientController.php";

use App\Controllers\AppointmentController;
use App\Controllers\DoctorController;
use App\Controllers\PatientController;

if (!isset($_SESSION['user_id'])) {
    header("Location: /auth/login.php");
    exit;
}

$appointmentController = new AppointmentController();
$doctorController = new DoctorController();
$patientController = new PatientController();

// Get ID
$id = $_GET['id'] ?? null;

// Fetch doctors and patients for dropdowns
$doctors = $doctorController->index(); 
$patients = $patientController->index();

if ($id) {
    // Editing
    $appointment = $appointmentController->show($id);
    if (!$appointment) {
        echo "Appointment not found.";
        exit;
    }
} else {
    // New appointment
    $appointment = [
        'doctor_id' => '',
        'patient_id' => '',
        'scheduled_at' => '',
        'status' => 'Scheduled',
        'notes' => ''
    ];
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'doctor_id' => $_POST['doctor_id'],
        'patient_id' => $_POST['patient_id'],
        'scheduled_at' => $_POST['scheduled_at'],
        'status' => $_POST['status'],
        'notes' => $_POST['notes'],
        'created_by' => $_SESSION['user_id']  // logged-in user
    ];

    if ($id) {
        // Update
        if ($appointmentController->update($id, $formData)) {
            header("Location: /dashboard?section=appointments");
            exit;
        } else {
            $error = "Failed to update appointment.";
        }
    } else {
        // Create
        if ($appointmentController->store($formData)) {
            header("Location: /dashboard?section=appointments");
            exit;
        } else {
            $error = "Failed to create appointment.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= $id ? 'Edit Appointment' : 'Add New Appointment' ?></title>
</head>
<body>
<h1><?= $id ? 'Edit Appointment' : 'Add New Appointment' ?></h1>
<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="POST">
    <label>Doctor</label>
    <select name="doctor_id" required>
        <option value="">Select Doctor</option>
        <?php foreach ($doctors as $doctor): ?>
            <option value="<?= $doctor['id'] ?>" <?= $appointment['doctor_id']==$doctor['id']?'selected':'' ?>>
                <?= htmlspecialchars($doctor['name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <label>Patient</label>
    <select name="patient_id" required>
        <option value="">Select Patient</option>
        <?php foreach ($patients as $patient): ?>
            <option value="<?= $patient['id'] ?>" <?= $appointment['patient_id']==$patient['id']?'selected':'' ?>>
                <?= htmlspecialchars($patient['name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <label>Scheduled At</label>
    <input type="datetime-local" name="scheduled_at" value="<?= htmlspecialchars($appointment['scheduled_at']) ?>" required><br>

    <label>Status</label>
    <select name="status">
        <option value="Scheduled" <?= $appointment['status']=='Scheduled'?'selected':'' ?>>Scheduled</option>
        <option value="Completed" <?= $appointment['status']=='Completed'?'selected':'' ?>>Completed</option>
        <option value="Cancelled" <?= $appointment['status']=='Cancelled'?'selected':'' ?>>Cancelled</option>
    </select><br>

    <label>Notes</label>
    <textarea name="notes"><?= htmlspecialchars($appointment['notes']) ?></textarea><br>

    <button type="submit"><?= $id ? 'Save' : 'Create' ?></button>
    <a href="/dashboard?section=appointments">Cancel</a>
</form>
</body>
</html>
