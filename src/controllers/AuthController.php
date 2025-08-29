<?php
namespace App\Controllers;

use App\Core\AuthInterface;
use App\Core\Auth;
use PDO;
use PDOException;

require_once __DIR__ . '/../config/config.php';

class AuthController implements AuthInterface {
    private ?PDO $db;

    public function __construct(?PDO $db = null) {
        $this->db = $db;
    }


    public function login(string $email, string $password): bool {
        $email = filter_var(trim($email), FILTER_VALIDATE_EMAIL);
        $stmt = $this->db->prepare("SELECT id, full_name, role_id, password_hash FROM users WHERE email = :email LIMIT 1");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role_id'] = $user['role_id'];
            return true;
        }
        return false;
    }

    public function logout(): void {
        session_destroy();
        header('Location: /auth/login');
        exit;
    }

    public function check(): bool {
        return isset($_SESSION['user_id']);
    }

    public function user(): ?array {
        if (!$this->check()) return null;

        $stmt = $this->db->prepare("SELECT id, full_name, email, role_id FROM users WHERE id = :id LIMIT 1");
        $stmt->bindValue(':id', $_SESSION['user_id']);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function showLogin(): void {
        include BASE_PATH.'/src/views/auth/login.php';
    }
}