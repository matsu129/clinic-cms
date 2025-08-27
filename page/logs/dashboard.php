<?php
session_start();
require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . '/../../registration/Patient.php';
require_once __DIR__ . '/../../registration/Appointment.php';
require_once __DIR__ . '/../../registration/MedicalNote.php';
require_once __DIR__ . '/../../registration/AuditLogger.php';

//If user is login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

class Dashboard {
    private $pdo;
    private $logger;
    private $section;

    public function __construct($pdo, $section) {
        $this->pdo = $pdo;
        $this->logger = new AuditLogger($pdo);
        $this->section = $section;
    }

    private function getRoleName() {
        return match($_SESSION['role_id'] ?? 3) {
            1 => 'Admin',
            2 => 'Doctor',
            default => 'Reception'
        };
    }

    public function renderSidebar() {
        ?>
        <div class="sidebar">
            <h2>Dashboard</h2>
            <p>Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></p>
            <p>Role: <?= $this->getRoleName() ?></p>
            <ul>
                <li><a href="?section=patients" class="<?= $this->section=='patients'?'active':'' ?>">Patients</a></li>
                <li><a href="?section=appointments" class="<?= $this->section=='appointments'?'active':'' ?>">Appointments</a></li>
                <li><a href="?section=medical_notes" class="<?= $this->section=='medical_notes'?'active':'' ?>">Medical Notes</a></li>
            </ul>
            <a href="?section=logout">Logout</a>
        </div>
        <?php
    }

    public function renderContent() {
        echo '<div class="main-content">';
        switch($this->section) {
            case 'patients':
                $patient = new Patient($this->pdo, $this->logger);
                $patient->render();
                break;
            case 'appointments':
                $appointment = new Appointment($this->pdo, $this->logger);
                $appointment->render();
                break;
            case 'medical_notes':
                $medicalNote = new MedicalNote($this->pdo, $this->logger);
                $medicalNote->render();
                break;
            case 'logout':
                session_destroy();
                header('Location: login.php');
                exit;
            default:
                echo "<p>Welcome! Please select a section from the sidebar.</p>";
        }
        echo '</div>';
    }
}

// Sectiona GET param
$section = $_GET['section'] ?? 'patients';
$dashboard = new Dashboard($pdo, $section);
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
.table-patients, .table-appointments, .table-medical-notes { border-collapse:collapse; width:100%; }
.table-patients th, .table-patients td,
.table-appointments th, .table-appointments td,
.table-medical-notes th, .table-medical-notes td { border:1px solid #bdc3c7; padding:8px; text-align:left; }
.table-patients th, .table-appointments th, .table-medical-notes th { background:#34495e; color:white; }
.btn, .btn-link { padding:5px 10px; text-decoration:none; background:#3498db; color:white; border-radius:4px; margin-top:5px; display:inline-block; }
.btn:hover, .btn-link:hover { background:#2980b9; }
.success { color:green; }
.error { color:red; }
</style>
</head>
<body>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<div class="dashboard-container">
    <?php 
    $dashboard->renderSidebar();
    $dashboard->renderContent();
    ?>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>
