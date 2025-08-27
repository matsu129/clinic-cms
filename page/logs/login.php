<?php
session_start();
require '../../config/config.php'; // Adjust path 

$cookieParams = session_get_cookie_params();
session_set_cookie_params([
    'lifetime' => $cookieParams['lifetime'],
    'path' => $cookieParams['path'],
    'domain' => $cookieParams['domain'],
    'secure' => isset($_SERVER['HTTPS']), // true if is HTTPS
    'httponly' => true,
    'samesite' => 'Strict'
]);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } else {

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        var_dump($password);
        var_dump($user['password_hash']);
        var_dump(password_verify($password, $user['password_hash']));

        if ($user) {
            // Verify password
            if (password_verify($password, $user['password_hash'])) {
                // Succesfull login
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role_id'] = $user['role_id'];
                $_SESSION['username'] = $user['full_name'];

                // cookie for session
                setcookie(session_name(), session_id(), [
                    'expires' => 0,
                    'path' => '/',
                    'domain' => $_SERVER['HTTP_HOST'],
                    'secure' => isset($_SERVER['HTTPS']),
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]);

                header('Location: /clinic-cms/page/logs/dashboard.php');
                exit;
            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "Email not found!";
        }
    }
}
?>
<?php include '../../includes/header.php'; ?>
<form method="post" class="login-table">
    Email: <input type="email" name="email" required class="login-wrapper"><br>
    Password: <input type="password" name="password" required class="login-wrapper"><br>
    <button type="submit">Login</button>
</form>
<?php if (isset($error)) echo $error; ?>
<?php include '../../includes/footer.php'; ?>