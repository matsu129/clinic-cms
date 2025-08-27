<?php
declare(strict_types=1);

namespace App\Controllers;

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../core/AuditLogger.php';

use App\Core\Database;
use Exception;

class AuditLogController {

    /**
     * Get all audit logs
     *
     * @return array
     * @throws Exception
     */
    public static function getAllLogs(): array {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT * FROM audit_logs ORDER BY created_at DESC");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw new Exception("Failed to fetch logs: " . $e->getMessage());
        }
    }

    /**
     * Get logs by user ID
     *
     * @param int $userId
     * @return array
     * @throws Exception
     */
    public static function getLogsByUser(int $userId): array {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM audit_logs WHERE user_id = :user_id ORDER BY created_at DESC");
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw new Exception("Failed to fetch logs for user {$userId}: " . $e->getMessage());
        }
    }

    /**
     * Get a single log by its ID
     *
     * @param int $logId
     * @return array|null
     * @throws Exception
     */
    public static function getLogById(int $logId): ?array {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM audit_logs WHERE id = :id");
            $stmt->execute(['id' => $logId]);
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (Exception $e) {
            throw new Exception("Failed to fetch log {$logId}: " . $e->getMessage());
        }
    }
}
