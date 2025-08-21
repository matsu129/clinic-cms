<?php 
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if($_SESSION['role_id'] != 2 && $_SESSION['role_id'] != 3) {
    die('Access denied. Doctor or Reception only.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("INSERT INTO appointments (patient_id, doctor_id, scheduled_at, notes, created_by) VALUES (?,?,?,?,?)");
    $stmt->execute([$_POST['patient_id'], $_POST['doctor_id'], $_POST['scheduled_at'], $_POST['notes'], $_SESSION['user_id']]);

    logAction('Added new appointment for patient ID: '.$_POST['patient_id'], $_SESSION['user_id']);
    header('Location: appointment.php');
    exit;
}

if(isset($_GET['delete_id'])) {
    $del_id = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM appointments WHERE appointment_id=?");
    $stmt->execute([$del_id]);
    logAction('Deleted appointment ID: '.$del_id, $_SESSION['user_id']);
    header('Location: appointment.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
   <form method="POST" action="appointment.php">
    <select name="patient_id">
        <?php
$stmt = $pdo->query("SELECT id, first_name, last_name FROM patients ORDER BY last_name");
while($row = $stmt->fetch()) {
    echo "<option value=\"{$row['id']}\">{$row['first_name']} {$row['last_name']}</option>";
}
?>
    </select>
    <select name="doctor_id">
       <?php
$stmt = $pdo->query("SELECT doctor_id, first_name, last_name FROM doctors ORDER BY last_name");
while($row = $stmt->fetch()) {
    echo "<option value=\"{$row['doctor_id']}\">{$row['first_name']} {$row['last_name']}</option>";
}
?>
    </select>
    <input type="datetime-local" name="appointment_date" required>
    <textarea name="notes" placeholder="Notes"></textarea>
    <button type="submit">Schedule Appointment</button>
</form> 
</body>
</html>