<?php
require_once __DIR__ . '/../config/config.php';

interface LoggerInterface {
    public function log(string $message, int $userId);
}

class AuditLogger implements LoggerInterface {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function log(string $message, int $userId) {
        $stmt = $this->pdo->prepare("INSERT INTO audit_logs (user_id, action, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$userId, $message]);
    }
}
?>