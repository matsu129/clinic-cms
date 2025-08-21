<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 3) {
    die('Access denied. Admin or Reception only.');
}

$patient_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM patients WHERE id=?");
$stmt->execute([$patient_id]);
$patient = $stmt->fetch();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("UPDATE patients SET first_name=?, last_name=?, birth_date=?, gender=?, address=?, phone=?, email=? WHERE id=?");
    $stmt->execute([
        $_POST['first_name'], $_POST['last_name'], $_POST['birth_date'], $_POST['gender'], $_POST['address'], $_POST['phone'], $_POST['email'], $patient_id
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
    <title>Document</title>
</head>
<body>
    <form method="POST" action="edit_patient.php?id=<?= $patient_id ?>">
    <input type="text" name="first_name" value="<?= htmlspecialchars($patient['first_name']) ?>" required>
    <input type="text" name="last_name" value="<?= htmlspecialchars($patient['last_name']) ?>" required>
    <input type="date" name="birth_date" value="<?= $patient['birth_date'] ?>" required>
    <select name="gender">
        <option value="Male" <?= $patient['gender']=='Male'?'selected':'' ?>>Male</option>
        <option value="Female" <?= $patient['gender']=='Female'?'selected':'' ?>>Female</option>
        <option value="Other" <?= $patient['gender']=='Other'?'selected':'' ?>>Other</option>
    </select>
    <input type="text" name="address" value="<?= htmlspecialchars($patient['address']) ?>">
    <input type="text" name="phone" value="<?= htmlspecialchars($patient['phone']) ?>">
    <input type="email" name="email" value="<?= htmlspecialchars($patient['email']) ?>">
    <button type="submit">Update Patient</button>
</form>

</body>
</html>