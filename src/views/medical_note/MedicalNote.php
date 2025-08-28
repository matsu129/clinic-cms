<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if($_SESSION['role_id'] != 2) {
    die('Access denied. Doctors only.');
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("INSERT INTO medical_notes (patient_id, note_text, created_by, created_at) VALUES (?,?,?,NOW())");
    $stmt->execute([$_POST['patient_id'], $_POST['note_text'], $_SESSION['user_id']]);

    logAction('Added new medical note for patient ID: '.$_POST['patient_id'], $_SESSION['user_id']);
    header('Location: medicalNote.php');
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
    <form method="POST" action="medicalNote.php">
    <select name="patient_id">
        <!-- Populate from patients table dynamically -->
    </select>
    <textarea name="note_text" placeholder="Enter medical note" required></textarea>
    <button type="submit">Save Note</button>
</form>
</body>
</html>