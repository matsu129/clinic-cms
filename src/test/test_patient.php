<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../controllers/PatientController.php';
use Controllers\PatientController;

$patientController = new PatientController();

function printTest($title, $data) {
    echo "<h3>$title</h3>";
    echo "<pre>"; print_r($data); echo "</pre><hr>";
}

// Create
$createResult = $patientController->create([
    'name' => 'Test Test',
    'dob' => '1990-01-01',
    'gender' => 'Male',
    'phone' => '123-456-7890',   // optional
    'email' => 'john@example.com', // optional
    'address' => '123 Street'     // optional
]);
printTest("Create Patient", $createResult);

// Read all
$patients = $patientController->index();
printTest("All Patients", $patients);

// Read by ID
if (!empty($patients[0]['id'])) {
    $patientId = $patients[0]['id'];
    $patientData = $patientController->show($patientId);
    printTest("Patient by ID", $patientData);

    // Update
    $updateResult = $patientController->update($patientId, [
      'name' => 'Edit Test',
      'dob' => '1990-01-02',
      'gender' => 'Female',
      'phone' => '123-456-7890',   // optional
      'email' => 'john@example.com', // optional
      'address' => '123 Street'     // optional
    ]);
    printTest("Update Patient", $updateResult);

    // // Delete
    // $deleteResult = $patientController->delete($patientId);
    // printTest("Delete Patient", $deleteResult);
}
