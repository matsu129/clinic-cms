<?php
session_start();
require '../../config/config.php'; // Adjust path 


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
var_dump($password); 
var_dump($user['password_hash']); 
var_dump(password_verify($password, $user['password_hash']));

 if ($user && password_verify($password, $user['password_hash'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role_id'] = $user['role_id'];
    $_SESSION['username'] = $user['full_name'];
    header('Location: /clinic-cms/page/logs/dashboard.php');
    exit;
} else {
    $error = "Invalid email or password";
}
}
?>
<?php include '../../includes/header.php'; ?>
<form method="post"  class="login-table">
  Email: <input type="email" name="email" required class="login-wrapper"><br>
  Password: <input type="password" name="password" required class="login-wrapper"><br>
  <button type="submit">Login</button>
</form>
<?php if(isset($error)) echo $error; ?>
<?php include '../../includes/footer.php'; ?>


