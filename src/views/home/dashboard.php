<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../models/Patient.php";
require_once __DIR__ . "/../../models/Appointment.php";
require_once __DIR__ . "/../../models/MedicalNote.php";

use App\Models\Patient;
use App\Models\Appointment;
use App\Models\MedicalNote;

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$section = $_GET['section'] ?? 'patients';

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<link rel="stylesheet" href="../../assets/css/dashboard.css">
<style>
.dashboard-container { display:flex; width:100%; }
.sidebar { width:220px; background:#2c3e50; color:white; padding:20px; }
.sidebar h2 { margin-top:0; }
.sidebar ul { list-style:none; padding:0; }
.sidebar ul li { margin-bottom:10px; }
.sidebar ul li a { color:white; text-decoration:none; display:block; padding:8px; border-radius:4px; }
.sidebar ul li a.active, .sidebar ul li a:hover { background:#34495e; }
.sidebar p, .sidebar a { color:#ecf0f1; }
.main-content { flex-grow:1; padding:20px; overflow-y:auto; background:#ecf0f1; }
</style>
</head>
<body>
<?php include __DIR__ . '/../../../public/includes/header.php'; ?>

<div class="dashboard-container">
    <div class="sidebar">
        <h2>Dashboard</h2>
        <p>Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></p>
        <p>Role: <?= $_SESSION['role_id'] == 1 ? 'Admin' : ($_SESSION['role_id'] == 2 ? 'Doctor' : 'Reception') ?></p>
        <ul>
            <li><a href="?section=patients" class="<?= $section=='patients'?'active':'' ?>">Patients</a></li>
            <li><a href="?section=appointments" class="<?= $section=='appointments'?'active':'' ?>">Appointments</a></li>
            <li><a href="?section=medical_notes" class="<?= $section=='medical_notes'?'active':'' ?>">Medical Notes</a></li>
        </ul>
        <a href="?section=logout">Logout</a>
    </div>

    <div class="main-content">
        <?php
        switch($section) {
            case 'patients':
                $patientModel = new Patient();
                $patients = $patientModel->getAll();
                echo "<h2>Patients List</h2>";
                echo "<pre>" . print_r($patients, true) . "</pre>";
                break;

            case 'appointments':
                $appointmentModel = new Appointment();
                $appointments = $appointmentModel->getAll();
                echo "<h2>Appointments List</h2>";
                echo "<pre>" . print_r($appointments, true) . "</pre>";
                break;

            case 'medical_notes':
                $medicalNoteModel = new MedicalNote();
                $notes = $medicalNoteModel->getAll();
                echo "<h2>Medical Notes</h2>";
                echo "<pre>" . print_r($notes, true) . "</pre>";
                break;

            case 'logout':
                session_destroy();
                header('Location: login.php');
                exit;

            default:
                echo "<p>Welcome! Please select a section from the sidebar.</p>";
        }
        ?>
    </div>
</div>

<?php include __DIR__ . '/../../../public/includes/footer.php'; ?>
</body>
</html>
