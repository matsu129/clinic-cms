<?php
require_once __DIR__ . '/../core/Auth.php';
use Core\Auth;

Auth::login(1);

echo Auth::checkLogin() ? "✅ Logged In\n" : "❌ Not Logged In\n";
echo "Token: " . Auth::getToken() . "\n";

sleep(2);

echo Auth::checkTimeout(1) ? "⏳ Active\n" : "❌ Timeout\n";

Auth::logout();
echo Auth::checkLogin() ? "Still logged in\n" : "✅ Logged Out\n";
?>
