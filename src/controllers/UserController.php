<?php
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $user;

    public function __construct() {
        $this->user = new User();
    }

    // register
    public function register($email, $password, $fullName) {
        if ($this->user->findByEmail($email)) {
            return ["success" => false, "message" => "Email already registered."];
        }
        $this->user->create($email, $password, $fullName);
        return ["success" => true, "message" => "Registration successful!"];
    }

    // login
    public function login($email, $password) {
        $user = $this->user->findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return ["success" => false, "message" => "Invalid email or password."];
        }
        $_SESSION['user_id'] = $user['id'];
        return ["success" => true, "message" => "Login successful!", "user" => $user];
    }
}
?>
