<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$role_id = $_SESSION['role_id'];

if($role_id == 1) {
    echo "<a href='users.php'>Manage Users</a>";
}

echo "<a href='../patient.php'>Patients</a>";
echo "<a href='../appointment.php'>Appointments</a>";
echo "<a href='../medicalNote.php'>Medical Notes</a>";
echo "<a href='../auditLogger.php'>Audit Logs</a>";
echo "<a href='logout.php'>Logout</a>";
?>