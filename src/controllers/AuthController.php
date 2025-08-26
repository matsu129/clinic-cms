<?php
namespace Controllers;

use App\Contracts\AuthInterface;
use Core\Auth;
use PDO;
use PDOException;

class AuthController implements AuthInterface {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function login(string $email, string $password): bool {
        $query = "SELECT id, password FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            Auth::login($user['id']);
            return true;
        }
        return false;
    }

    public function logout(): void {
        Auth::logout();
    }

    public function check(): bool {
        return Auth::checkLogin();
    }

    public function user(): ?array {
        if (!$this->check()) {
            return null;
        }

        $query = "SELECT id, email, name FROM users WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
