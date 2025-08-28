<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../config/config.php";

use App\Controllers\PatientController;

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$patientController = new PatientController();

// IDを取得
$id = $_GET['id'] ?? null;

if ($id) {
    // 編集
    $patient = $patientController->show($id);
} else {
    // 新規作成用の空データ
    $patient = [
        'name' => '',
        'dob' => '',
        'gender' => 'Unspecified',
        'address' => '',
        'phone' => '',
        'email' => ''
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'name' => $_POST['name'],
        'dob' => $_POST['dob'],
        'gender' => $_POST['gender'],
        'address' => $_POST['address'],
        'phone' => $_POST['phone'],
        'email' => $_POST['email']
    ];

    if ($id) {
        // 編集
        if ($patientController->update($id, $formData)) {
            header("Location: /dashboard?section=patients");
            exit;
        } else {
            $error = "Failed to update patient.";
        }
    } else {
        // 新規作成
        if ($patientController->create($formData)) {
            header("Location: /dashboard?section=patients");
            exit;
        } else {
            $error = "Failed to create patient.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= $id ? 'Edit Patient' : 'Add New Patient' ?></title>
</head>
<body>
<h1><?= $id ? 'Edit Patient' : 'Add New Patient' ?></h1>
<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST">
    <label>Full Name</label>
    <input type="text" name="name" value="<?= htmlspecialchars($patient['name']) ?>" required><br>

    <label>DOB</label>
    <input type="date" name="dob" value="<?= htmlspecialchars($patient['dob']) ?>" required><br>

    <label>Gender</label>
    <select name="gender">
        <option value="Male" <?= $patient['gender']=='Male'?'selected':'' ?>>Male</option>
        <option value="Female" <?= $patient['gender']=='Female'?'selected':'' ?>>Female</option>
        <option value="Other" <?= $patient['gender']=='Other'?'selected':'' ?>>Other</option>
        <option value="Unspecified" <?= $patient['gender']=='Unspecified'?'selected':'' ?>>Unspecified</option>
    </select><br>

    <label>Address</label>
    <textarea name="address"><?= htmlspecialchars($patient['address']) ?></textarea><br>

    <label>Phone</label>
    <input type="text" name="phone" value="<?= htmlspecialchars($patient['phone']) ?>"><br>

    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($patient['email']) ?>"><br>

    <button type="submit"><?= $id ? 'Save' : 'Create' ?></button>
    <a href="/dashboard?section=patients">Cancel</a>
</form>
</body>
</html>
