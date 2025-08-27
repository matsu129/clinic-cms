<?php
session_start();
require_once __DIR__ . '/config.php'; // Database connection settings

// Access control
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 3) {
    die('Access denied. Admin or Reception only.');
}

// Database connection
try {
    $pdo = new PDO("mysql:host=".DB_SERVERNAME.";dbname=".DB_NAME, DB_USERNAME, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Audit log function
function logAction($message, $userId) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$userId, $message]);
}

// Handle new patient creation
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO patients (name, dob, gender, phone, email, address) VALUES (?,?,?,?,?,?)");
    $stmt->execute([
        $_POST['name'],
        $_POST['dob'],
        $_POST['gender'],
        $_POST['phone'] ?? null,
        $_POST['email'] ?? null,
        $_POST['address'] ?? null
    ]);

    logAction('Added new patient: '.$_POST['name'], $_SESSION['user_id']);
    header('Location: patient.php');
    exit;
}

// Handle patient deletion
if(isset($_GET['delete_id'])) {
    $del_id = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM patients WHERE id=?");
    $stmt->execute([$del_id]);
    logAction('Deleted patient ID: '.$del_id, $_SESSION['user_id']);
    header('Location: patient.php');
    exit;
}

// Fetch patient list
$stmt = $pdo->query("SELECT * FROM patients ORDER BY id DESC");
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Management</title>
</head>
<body>
    <h1>Patient Management</h1>

    <!-- New Patient Form -->
    <form method="POST" action="patient.php">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="date" name="dob" required>
        <select name="gender">
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
            <option value="Unspecified">Unspecified</option>
        </select>
        <input type="text" name="phone" placeholder="Phone">
        <input type="email" name="email" placeholder="Email">
        <textarea name="address" placeholder="Address"></textarea>
        <button type="submit">Save Patient</button>
    </form>

    <h2>Patient List</h2>
    <table border="1" cellpadding="5">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Date of Birth</th>
            <th>Gender</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Address</th>
            <th>Actions</th>
        </tr>
        <?php foreach($patients as $patient): ?>
        <tr>
            <td><?= htmlspecialchars($patient['id']) ?></td>
            <td><?= htmlspecialchars($patient['name']) ?></td>
            <td><?= htmlspecialchars($patient['dob']) ?></td>
            <td><?= htmlspecialchars($patient['gender']) ?></td>
            <td><?= htmlspecialchars($patient['phone']) ?></td>
            <td><?= htmlspecialchars($patient['email']) ?></td>
            <td><?= htmlspecialchars($patient['address']) ?></td>
            <td>
                <a href="patient.php?delete_id=<?= $patient['id'] ?>" onclick="return confirm('Are you sure you want to delete this patient?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
