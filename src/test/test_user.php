<?php
session_start();
require_once __DIR__ . '/../controllers/UserController.php';

$userController = new UserController();

// register
echo "<h3>Register Test</h3>";
print_r($userController->register("testuser@example.com", "test1234", "Test User"));

// login
echo "<h3>Login Test</h3>";
print_r($userController->login("testuser@example.com", "test1234"));
