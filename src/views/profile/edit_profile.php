<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../controllers/UserController.php";

use App\Controllers\UserController;

if (!isset($_SESSION['user_id'])) {
    header("Location: /auth/login");
    exit;
}

$userController = new UserController();
$userId = $_SESSION['user_id'];

$user = $userController->show((int)$userId);
if (!$user) {
    echo "User not found.";
    exit;
}

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'email' => $_POST['email'] ?? $user['email'],
        'full_name' => $_POST['full_name'] ?? $user['full_name'],
        'role_id' => $user['role_id'],
        'is_active' => $user['is_active'],
        'profile_pic' => $user['profile_pic'],
    ];


    if (!empty($_POST['password'])) {
        $formData['password'] = $_POST['password'];
    }

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../../public/uploads/profile_pics/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileTmpPath = $_FILES['profile_pic']['tmp_name'];
        $fileName = basename($_FILES['profile_pic']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExt, $allowedExts)) {
            $newFileName = 'user_' . $userId . '.' . $fileExt;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $formData['profile_pic'] = '/uploads/profile_pics/' . $newFileName;
            } else {
                $error = "Failed to move uploaded file.";
            }
        } else {
            $error = "Invalid file type. Only JPG, PNG, GIF allowed.";
        }
    }

    if (!$error && $userController->updateUser((int)$userId, $formData)) {
        $_SESSION['user']['email'] = $formData['email'];
        $_SESSION['user']['full_name'] = $formData['full_name'];
        $_SESSION['user']['profile_pic'] = $formData['profile_pic'];

        $success = "Profile updated successfully!";
        $user = $userController->show((int)$userId);
    } elseif (!$error) {
        $error = "Failed to update profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile</title>
</head>
<body>
<h1>My Profile</h1>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<?php if ($success): ?>
    <p style="color:green;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br>

    <label>Password (leave blank to keep current)</label>
    <input type="password" name="password"><br>

    <label>Full Name</label>
    <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required><br>

    <label>Profile Picture</label><br>
    <?php
        $profileImg = !empty($user['profile_pic']) 
            ? str_replace($_SERVER['DOCUMENT_ROOT'], '', $user['profile_pic']) 
        : '/default.png';
    ?>


    <img src="<?= htmlspecialchars($profileImg) ?>" alt="Profile" style="max-width:100px;"><br>
    <input type="file" name="profile_pic"><br>

    <button type="submit">Save</button>
</form>

<a href="/dashboard">Back to Dashboard</a>
</body>
</html>
