<?php
require_once __DIR__ . '/../core/AuditLogger.php';

AuditLogger::logAction("Test log from development script", 1);

echo "✅ Log inserted successfully!";
?>
