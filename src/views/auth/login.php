<?php include __DIR__ . '/../../../public/includes/header.php';  if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post" class="login-table">
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" class="login-wrapper" required><br>

    <label for="password">Password:</label>
    <input type="password" name="password" id="password" class="login-wrapper" required><br>

    <button type="submit">Login</button>
</form>
<?php 
include __DIR__ . '/../../../public/includes/footer.php'; ?>
