<?php
namespace App\Core;

class Auth {
    public static function startSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login(int $user_id): void {
        self::startSession();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['token'] = bin2hex(random_bytes(32));
        $_SESSION['last_activity'] = time();
    }

    public static function checkLogin(): bool {
        self::startSession();
        return isset($_SESSION['user_id']);
    }

    public static function getToken(): ?string {
        self::startSession();
        return $_SESSION['token'] ?? null;
    }

    public static function validateToken(string $token): bool {
        self::startSession();
        return hash_equals($_SESSION['token'] ?? '', $token);
    }

    public static function logout(): void {
        self::startSession();
        session_unset();
        session_destroy();
    }

    public static function checkTimeout(int $timeout = 1800): bool {
        self::startSession();
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
            self::logout();
            return false;
        }
        $_SESSION['last_activity'] = time();
        return true;
    }
}
