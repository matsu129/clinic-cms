<?php
namespace App\Core;

require_once __DIR__ . '/Database.php';
use App\Core\Database;
use Exception;

class AuditLogger {

    /**
     * Log a user action to the audit_logs table
     *
     * @param string $action
     * @param int|null $userId
     * @return void
     * @throws Exception
     */
    public static function logAction(string $action, ?int $userId = null): void {
        try {
            $db = Database::getInstance()->getConnection();

            $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';

            $sql = "INSERT INTO audit_logs (user_id, action, ip_address, user_agent, created_at)
                    VALUES (:user_id, :action, :ip, :ua, NOW())";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':action' => $action,
                ':ip' => $ip,
                ':ua' => $userAgent
            ]);

        } catch (Exception $e) {
            error_log("Failed to write audit log: " . $e->getMessage(), 3, __DIR__ . '/../logs/error.log');
        }
    }
}
