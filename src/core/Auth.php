<?php
class Auth {
    public static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login($user_id) {
        self::startSession();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['token'] = bin2hex(random_bytes(32));
        $_SESSION['last_activity'] = time();
    }

    public static function checkLogin() {
        self::startSession();
        return isset($_SESSION['user_id']);
    }

    public static function getToken() {
        self::startSession();
        return $_SESSION['token'] ?? null;
    }

    public static function validateToken($token) {
        self::startSession();
        return hash_equals($_SESSION['token'] ?? '', $token);
    }

    public static function logout() {
        self::startSession();
        session_unset();
        session_destroy();
    }

    public static function checkTimeout($timeout = 1800) { // 30分
        self::startSession();
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
            self::logout();
            return false;
        }
        $_SESSION['last_activity'] = time();
        return true;
    }
}
?>
