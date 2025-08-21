<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if($_SESSION['role_id'] != 1) {
    die('Access denied. Admins only.');
}

$stmt = $pdo->query("SELECT * FROM audit_logs ORDER BY created_at DESC");
$logs = $stmt->fetchAll();
?>

<table>
    <thead>
        <tr><th>ID</th><th>Action</th><th>User ID</th><th>Timestamp</th></tr>
    </thead>
    <tbody>
    <?php foreach($logs as $log): ?>
        <tr>
            <td><?= $log['id'] ?></td>
            <td><?= htmlspecialchars($log['action']) ?></td>
            <td><?= $log['user_id'] ?></td>
            <td><?= $log['created_at'] ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>