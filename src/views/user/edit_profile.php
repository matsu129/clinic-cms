<?php
session_start();
require_once "../../db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = $_POST['name'];
    $phone = $_POST['phone'];

    $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ? WHERE id = ?");
    $stmt->execute([$name, $phone, $user_id]);

    header("Location: view_profile.php");
    exit;
}

$stmt = $pdo->prepare("SELECT name, phone FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
</head>
<body>
    <h2>Edit Profile</h2>
    <form action="upload_profile_pic.php" method="POST" enctype="multipart/form-data">
    <label for="username">Meno:</label>
    <input type="text" name="username" value="<?= $user['username'] ?>">

    <label for="email">Email:</label>
    <input type="email" name="email" value="<?= $user['email'] ?>">

    <label for="profile_pic">Profile picture:</label>
    <input type="file" name="profile_pic">

    <button type="submit" name="save">Save</button>
</form>

</body>
</html>
