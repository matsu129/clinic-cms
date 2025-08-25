<?php
require_once __DIR__ . '/Database.php';

class AuditLogger {
    public static function logAction($action, $user_id = null) {
        $db = Database::getInstance();
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';

        $sql = "INSERT INTO audit_logs (user_id, action, ip_address, user_agent)
                VALUES (:user_id, :action, :ip, :ua)";
        $db->query($sql, [
            ':user_id' => $user_id,
            ':action' => $action,
            ':ip' => $ip,
            ':ua' => $ua
        ]);
    }
}
?>
