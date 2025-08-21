<?php
include '../includes/db.php';

if (!isset($_GET['id'])) {
    die("Appointment ID not provided.");
}
$id = (int) $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];
    $notes = $_POST['notes'];

    $sql = "UPDATE appointments 
            SET patient_id=?, doctor_id=?, appointment_date=?, notes=? 
            WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissi", $patient_id, $doctor_id, $appointment_date, $notes, $id);

    if ($stmt->execute()) {
        echo "✅ Appointment updated successfully!";
        header("Location: list_appointments.php");
        exit();
    } else {
        echo "❌ Error updating appointment: " . $conn->error;
    }
}
$sql = "SELECT * FROM appointments WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Appointment not found.");
}
$appointment = $result->fetch_assoc();
$patients = $conn->query("SELECT id, name FROM patients");
$doctors = $conn->query("SELECT id, name FROM doctors");
?>
   <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Appointment</title>
</head>
<body>
    <h2>Edit Appointment</h2>

    <form method="POST">
        <label>Patient:</label>
        <select name="patient_id" required>
            <?php while ($p = $patients->fetch_assoc()): ?>
                <option value="<?= $p['id'] ?>" <?= ($p['id'] == $appointment['patient_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <br><br>

        <label>Doctor:</label>
        <select name="doctor_id" required>
            <?php while ($d = $doctors->fetch_assoc()): ?>
                <option value="<?= $d['id'] ?>" <?= ($d['id'] == $appointment['doctor_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($d['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <br><br>

        <label>Appointment Date:</label>
        <input type="datetime-local" name="appointment_date" value="<?= date('Y-m-d\TH:i', strtotime($appointment['appointment_date'])) ?>" required>
        <br><br>

        <label>Notes:</label>
        <textarea name="notes"><?= htmlspecialchars($appointment['notes']) ?></textarea>
        <br><br>

        <button type="submit">Save Changes</button>
    </form>

</body>
</html>