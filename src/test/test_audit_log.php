<?php
require_once __DIR__ . '/../controllers/AuditLogController.php';

use App\Controllers\AuditLogController;

try {
    echo "<h2>==== All Logs ====</h2><pre>";
    $logs = AuditLogController::getAllLogs();
    print_r($logs);
    echo "</pre>";

    echo "<h2>==== Logs for User ID 1 ====</h2><pre>";
    $userLogs = AuditLogController::getLogsByUser(1);
    print_r($userLogs);
    echo "</pre>";

    echo "<h2>==== Log Detail for ID 2 ====</h2><pre>";
    $logDetail = AuditLogController::getLogById(2);
    print_r($logDetail);
    echo "</pre>";

} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
