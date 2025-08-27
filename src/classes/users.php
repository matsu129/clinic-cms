<?php
session_start();

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if($_SESSION['role_id'] != 1) { // Admin only
    die('Access denied. Admins only.');
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role_id, email) VALUES (?,?,?,?)");
    $stmt->execute([$_POST['username'], $hashedPassword, $_POST['role_id'], $_POST['email']]);

    logAction('Added new user: '.$_POST['username'], $_SESSION['user_id']);
    header('Location: users.php');
    exit;
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <h2>Login</h2>
    <?php if (isset($error_message)) { echo "<p style='color:red;'>$error_message</p>"; } ?>
    <form method="post" action="users.php">
        <label for="username">Username:</label>
        <input type="text" id="username" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <input type="email" name="email" placeholder="Email" required>
        <select name="role_id">
            <option value="1">Admin</option>
            <option value="2">Doctor</option>
            <option value="3">Reception</option>
         </select>
      <button type="submit">Create user</button>
         <p class="register-link">Don't have an account? <a href="register.php" class="form-link">Register</a></p>  
    </form>
  <?php include 'includes/footer.php'; ?>  
</body>
</html>

