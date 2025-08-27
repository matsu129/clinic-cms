<?php
define("DB_HOST", "127.0.0.1");
define("DB_PORT", "3306");
define("DB_NAME", "clinic_app");
define("DB_USER", "PHPAgent");
define("DB_PASS", "1111");
define('BASE_PATH', dirname(__DIR__, 2));

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/Vancouver');

try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8",
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
