<?php
session_start();
require_once __DIR__ . '/config.php'; // DB connection

// Access control
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 3) {
    die('Access denied. Admin or Reception only.');
}

// Helper: log action
function logAction($message, $userId) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$userId, $message]);
}

// Get patient ID
$patient_id = $_GET['id'] ?? null;
if (!$patient_id) {
    die('Patient ID is required.');
}

// Fetch patient
$stmt = $pdo->prepare("SELECT * FROM patients WHERE id=?");
$stmt->execute([$patient_id]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$patient) {
    die('Patient not found.');
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE patients SET name=?, dob=?, gender=?, address=?, phone=?, email=?, updated_at=NOW() WHERE id=?");
    $stmt->execute([
        $_POST['name'],
        $_POST['dob'],
        $_POST['gender'],
        $_POST['address'] ?? null,
        $_POST['phone'] ?? null,
        $_POST['email'] ?? null,
        $patient_id
    ]);

    logAction('Updated patient ID: '.$patient_id, $_SESSION['user_id']);
    header('Location: patient.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Patient</title>
</head>
<body>
    <h1>Edit Patient</h1>
    <form method="POST" action="edit_patient.php?id=<?= htmlspecialchars($patient_id) ?>">
        <input type="text" name="name" value="<?= htmlspecialchars($patient['name']) ?>" placeholder="Full Name" required>
        <input type="date" name="dob" value="<?= htmlspecialchars($patient['dob']) ?>" required>
        <select name="gender" required>
            <option value="Male" <?= $patient['gender']=='Male'?'selected':'' ?>>Male</option>
            <option value="Female" <?= $patient['gender']=='Female'?'selected':'' ?>>Female</option>
            <option value="Other" <?= $patient['gender']=='Other'?'selected':'' ?>>Other</option>
            <option value="Unspecified" <?= $patient['gender']=='Unspecified'?'selected':'' ?>>Unspecified</option>
        </select>
        <textarea name="address" placeholder="Address"><?= htmlspecialchars($patient['address']) ?></textarea>
        <input type="text" name="phone" value="<?= htmlspecialchars($patient['phone']) ?>" placeholder="Phone">
        <input type="email" name="email" value="<?= htmlspecialchars($patient['email']) ?>" placeholder="Email">
        <button type="submit">Update Patient</button>
    </form>
    <p><a href="patient.php">Back to Patient List</a></p>
</body>
</html>
