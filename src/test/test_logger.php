<?php
require_once __DIR__ . '/../core/AuditLogger.php';

use \Core\AuditLogger;

try {
    AuditLogger::logAction("Test log from development script", 1);
    echo "✅ Log inserted successfully!";
} catch (Exception $e) {
    echo "❌ Failed to insert log: " . $e->getMessage();
}
