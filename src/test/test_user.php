<?php
// Enable full error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../config/config.php'; 
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Controllers\UserController; 

// Initialize controller
$userController = new UserController();

// Utility for formatted output
function printTestResult($title, $result) {
    echo "<h3>$title</h3>";
    echo "<pre>";
    print_r($result);
    echo "</pre><hr>";
}

// ===== Test Registration =====
printTestResult("Register Test", $userController->register([
    'email' => 'Admin2@example.com',
    'password' => 'test1234',
    'full_name' => 'Admin User2',
    'role_id' => 1
]));

// ===== Test Login =====
$loginResult = $userController->login('Admin2@example.com', 'test1234');
printTestResult("Login Test", $loginResult);

// ===== Test Get User by ID =====
if (!empty($loginResult['success']) && !empty($loginResult['user']['id'])) {
    $userId = $loginResult['user']['id'];
    $userData = $userController->show($userId);
    printTestResult("Get User by ID", $userData);

    // ===== Test Update User =====
    $updateResult = $userController->updateUser(
        $userId,
        ['updatedadmin@example.com',
        'Updated Admin']
    );
    printTestResult("Update User Test", $updateResult);

    // ===== Test Delete User =====
    // $deleteResult = $userController->deleteUser($userId);
    // printTestResult("Delete User Test", $deleteResult);
} else {
    echo "<p style='color:red;'>Login failed. Skipping ID, Update, and Delete tests.</p>";
}
?>
