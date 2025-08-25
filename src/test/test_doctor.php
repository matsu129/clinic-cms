<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once __DIR__ . '/../controllers/DoctorController.php';

use Controllers\DoctorController;

$doctorController = new DoctorController();



function printTestResult($title, $result) {
    echo "<h3>$title</h3>";
    echo "<pre>";
    print_r($result);
    echo "</pre><hr>";
}

// ===== Test Create Doctor =====
$createResult = $doctorController->create([
    'user_id' => 2,   // もしユーザーIDがあれば
    'name' => 'John Smith',
    'specialty' => 'Cardiology',
    'phone' => '123-456-7890',
    'email' => 'doctor.john@example.com'
]);
printTestResult("Create Doctor Test", $createResult);

// ===== Test Get All Doctors =====
$allDoctors = $doctorController->index();
printTestResult("Get All Doctors", $allDoctors);

// ===== Test Get Doctor by ID =====
if (!empty($allDoctors)) {
    $doctorId = $allDoctors[0]['id'];
    $doctorData = $doctorController->show($doctorId);
    printTestResult("Get Doctor by ID", $doctorData);

    // ===== Test Update Doctor =====
    $updateResult = $doctorController->update($doctorId, [
        'user_id' => 2,
        'name' => 'John Smith',
        'specialty' => 'Neurology',
        'phone' => '987-654-3210',
        'email' => 'alice.updated@example.com'
    ]);
    printTestResult("Update Doctor Test", $updateResult);

    // // ===== Test Delete Doctor =====
    // $deleteResult = $doctorController->delete($doctorId);
    // printTestResult("Delete Doctor Test", $deleteResult);
}
