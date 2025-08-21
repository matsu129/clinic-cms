<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 3) {
    die('Access denied. Admin or Reception only.');
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("INSERT INTO patients (first_name,last_name,birth_date,gender,address,phone,email) VALUES (?,?,?,?,?,?,?)");
    $stmt->execute([$_POST['first_name'], $_POST['last_name'], $_POST['birth_date'], $_POST['gender'], $_POST['address'], $_POST['phone'], $_POST['email']]);

    logAction('Added new patient: '.$_POST['first_name'].' '.$_POST['last_name'], $_SESSION['user_id']);
    header('Location: patient.php');
    exit;
}
if(isset($_GET['delete_id'])) {
    $del_id = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM patients WHERE id=?");
    $stmt->execute([$del_id]);
    logAction('Deleted patient ID: '.$del_id, $_SESSION['user_id']);
    header('Location: patient.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="POST" action="patient.php">
        <input type="text" name="first_name" placeholder="First Name" required>
        <input type="text" name="last_name" placeholder="Last Name" required>
        <input type="date" name="birth_date" required>
        <select name="gender">
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>
        <input type="text" name="address" placeholder="Address">
        <input type="text" name="phone" placeholder="Phone">
        <input type="email" name="email" placeholder="Email">
        <button type="submit">Save Patient</button>
        <a href="patient.php?delete_id=<?= $patient['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
    </form>
    
</body>
</html>