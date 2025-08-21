<?php
function logAction($message, $user_id) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, created_at) VALUES (?,?,NOW())");
    $stmt->execute([$user_id, $message]);
}
?>