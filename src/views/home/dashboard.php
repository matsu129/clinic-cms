<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../config/config.php";

use App\Controllers\PatientController;
use App\Controllers\AppointmentController;
use App\Controllers\MedicalNoteController;

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
            <?php $baseUrl = '/dashboard'; ?>
            <li><a href="<?= $baseUrl ?>?section=patients" class="<?= $section=='patients'?'active':'' ?>">Patients</a></li>
            <li><a href="<?= $baseUrl ?>?section=appointments" class="<?= $section=='appointments'?'active':'' ?>">Appointments</a></li>
            <li><a href="<?= $baseUrl ?>?section=medical_notes" class="<?= $section=='medical_notes'?'active':'' ?>">Medical Notes</a></li>
        </ul>
        <a href="/auth/logout">Logout</a>
    </div>

    <div class="main-content">
        <?php
        switch($section) {
            case 'patients':
                $patientController = new PatientController();
                $patients = $patientController->index();
                echo "<h2>Patients List</h2>";
                if (!empty($patients)) {
                    echo "<table border='1' cellpadding='8' cellspacing='0'>";
                    echo "<thead>";
                    echo "<tr>";
                    echo "<th>ID</th>";
                    echo "<th>Name</th>";
                    echo "<th>DOB</th>";
                    echo "<th>Gender</th>";
                    echo "<th>Phone</th>";
                    echo "<th>Email</th>";
                    echo "<th>Address</th>";
                    echo "<th>Created At</th>";
                    echo "<th>Updated At</th>";
                    echo "<th>Actions</th>"; // 編集・削除用
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";
                    foreach ($patients as $patient) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($patient['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($patient['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($patient['dob']) . "</td>";
                        echo "<td>" . htmlspecialchars($patient['gender']) . "</td>";
                        echo "<td>" . htmlspecialchars($patient['phone']) . "</td>";
                        echo "<td>" . htmlspecialchars($patient['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($patient['address']) . "</td>";
                        echo "<td>" . htmlspecialchars($patient['created_at']) . "</td>";
                        echo "<td>" . htmlspecialchars($patient['updated_at']) . "</td>";
                        echo "<td>";
                        echo "<a href='?section=patients&edit=" . $patient['id'] . "'>Edit</a> | ";
                        echo "<a href='?section=patients&delete=" . $patient['id'] . "' onclick='return confirm(\"Are you sure?\")'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
                } else {
                    echo "<p>No patients found.</p>";
                }
                break;

            case 'appointments':
                $appointmentController = new AppointmentController();
                $appointments = $appointmentController->index();
                echo "<h2>Appointments List</h2>";
                if (!empty($appointments)) {
                    echo "<table border='1' cellpadding='8' cellspacing='0'>";
                    echo "<thead>";
                    echo "<tr>";
                    echo "<th>ID</th>";
                    echo "<th>Doctor ID</th>";
                    echo "<th>Patient ID</th>";
                    echo "<th>Scheduled At</th>";
                    echo "<th>Status</th>";
                    echo "<th>Notes</th>";
                    echo "<th>Created By</th>";
                    echo "<th>Created At</th>";
                    echo "<th>Updated At</th>";
                    echo "<th>Actions</th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";

                    foreach ($appointments as $appt) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($appt['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($appt['doctor_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($appt['patient_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($appt['scheduled_at']) . "</td>";
                        echo "<td>" . htmlspecialchars($appt['status']) . "</td>";
                        echo "<td>" . htmlspecialchars($appt['notes']) . "</td>";
                        echo "<td>" . htmlspecialchars($appt['created_by']) . "</td>";
                        echo "<td>" . htmlspecialchars($appt['created_at']) . "</td>";
                        echo "<td>" . htmlspecialchars($appt['updated_at']) . "</td>";
                        echo "<td>";
                        echo "<a href='?section=appointments&edit=" . $appt['id'] . "'>Edit</a> | ";
                        echo "<a href='?section=appointments&delete=" . $appt['id'] . "' onclick='return confirm(\"Are you sure?\")'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }

                    echo "</tbody>";
                    echo "</table>";
                } else {
                    echo "<p>No appointments found.</p>";
                }
                break;

            case 'medical_notes':
                $medicalNoteController = new MedicalNoteController();
                $notes = $medicalNoteController->index();
                if (!empty($notes)) {
                    echo "<table border='1' cellpadding='8' cellspacing='0'>";
                    echo "<thead>";
                    echo "<tr>";
                    echo "<th>ID</th>";
                    echo "<th>Doctor ID</th>";
                    echo "<th>Patient Name</th>";
                    echo "<th>Note</th>";
                    echo "<th>Created At</th>";
                    echo "<th>Updated At</th>";
                    echo "<th>Actions</th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";

                    foreach ($notes as $note) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($note['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($note['doctor_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($note['patient_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($note['note']) . "</td>";
                        echo "<td>" . htmlspecialchars($note['created_at']) . "</td>";
                        echo "<td>" . htmlspecialchars($note['updated_at']) . "</td>";
                        echo "<td>";
                        echo "<a href='?section=medical_notes&edit=" . $note['id'] . "'>Edit</a> | ";
                        echo "<a href='?section=medical_notes&delete=" . $note['id'] . "' onclick='return confirm(\"Are you sure?\")'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }

                    echo "</tbody>";
                    echo "</table>";
                } else {
                    echo "<p>No medical notes found.</p>";
                }
                break;

            case 'logout':
                session_destroy();
                header('Location: /auth/logout');
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
