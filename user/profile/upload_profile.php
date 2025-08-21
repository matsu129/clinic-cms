<?php
session_start();
require '../db.php';

$user_id = $_SESSION['user_id'];

if (isset($_POST['save'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];

    // picture 
    if (!empty($_FILES['profile_pic']['name'])) {
        $target_dir = "../uploads/profile_pics/";
        $file_name = time() . "_" . basename($_FILES["profile_pic"]["name"]);
        $target_file = $target_dir . $file_name;

        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed = ["jpg", "jpeg", "png", "gif"];

        if (in_array($imageFileType, $allowed)) {
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                // update with pictures
                $sql = "UPDATE users SET username=?, email=?, profile_pic=? WHERE id=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$username, $email, $file_name, $user_id]);
            }
        }
    } else {
        // update without change pictures
        $sql = "UPDATE users SET username=?, email=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $email, $user_id]);
    }

    header("Location: view_profile.php");
    exit;
}
?>
