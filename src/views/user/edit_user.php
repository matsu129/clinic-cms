<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../config/config.php";

use App\Controllers\UserController;

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userController = new UserController();

$id = $_GET['id'] ?? null;

if ($id) {
    $user = $userController->show((int)$id);
    if (!$user) {
        echo "User not found.";
        exit;
    }
} else {
    // 新規作成用の空データ
    $user = [
        'email' => '',
        'full_name' => '',
        'role_id' => 3,
        'is_active' => 1,
        'profile_pic' => ''
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'email' => $_POST['email'] ?? $user['email'],
        'full_name' => $_POST['full_name'] ?? $user['full_name'],
        'role_id' => (int)($_POST['role_id'] ?? $user['role_id']),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
        'profile_pic' => $_POST['profile_pic'] ?? $user['profile_pic'],
    ];

    if (!empty($_POST['password'])) {
        $formData['password'] = $_POST['password'];
    }

    if ($id) {
        if ($userController->updateUser($id, $formData)) {
            header("Location: /dashboard?section=users");
            exit;
        } else {
            $error = "Failed to update user.";
        }
    } else {
        if ($userController->register($formData)) {
            header("Location: /dashboard?section=users");
            exit;
        } else {
            $error = "Failed to create User.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= $id ? 'Edit User' : 'Add New User' ?></title>
</head>
<body>
<h1><?= $id ? 'Edit User' : 'Add New User' ?></h1>
<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST">
    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br>

    <label>Password <?= $id ? '(leave blank to keep current)' : '' ?></label>
    <input type="password" name="password"><br>

    <label>Full Name</label>
    <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required><br>

    <label>Role</label>
    <select name="role_id">
        <option value="1" <?= $user['role_id']==1?'selected':'' ?>>Admin</option>
        <option value="2" <?= $user['role_id']==2?'selected':'' ?>>Doctor</option>
        <option value="3" <?= $user['role_id']==3?'selected':'' ?>>Reception</option>
    </select><br>

    <label>Active</label>
    <input type="checkbox" name="is_active" <?= $user['is_active'] ? 'checked' : '' ?>><br>

    <label>Profile Picture URL</label>
    <input type="text" name="profile_pic" value="<?= htmlspecialchars($user['profile_pic']) ?>"><br>

    <button type="submit"><?= $id ? 'Save' : 'Create' ?></button>
    <a href="/dashboard?section=users">Cancel</a>
</form>
</body>
</html>
