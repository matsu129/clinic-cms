<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2) {
    die('Access denied. Admin or Doctor only.');
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['medical_doc'])) {
    $uploadDir = 'uploads/';
    $fileName = basename($_FILES['medical_doc']['name']);
    $targetFile = $uploadDir . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $fileName);

    if (move_uploaded_file($_FILES['medical_doc']['tmp_name'], $targetFile)) {
        // Save $targetFile path to DB for the patient
        echo 'File uploaded successfully';
    } else {
        echo 'File upload failed';
    }
}
?>