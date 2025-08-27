<?php
if (!defined('BASE_PATH')) define('BASE_PATH', __DIR__ . '/../');
if (!defined('DB_SERVERNAME')) define('DB_SERVERNAME', 'localhost');
if (!defined('DB_USERNAME')) define('DB_USERNAME', 'PHPAgent');
if (!defined('DB_PASS')) define('DB_PASS', 'Jaka.2489');
if (!defined('DB_NAME')) define('DB_NAME', 'clinic_app');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_SERVERNAME . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USERNAME,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
