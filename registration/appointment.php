<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . '/AuditLogger.php';
require_once __DIR__ . '/Patient.php';

class Appointment {
    private $pdo;
    private $logger;

    public function __construct($pdo, $logger) {
        $this->pdo = $pdo;
        $this->logger = $logger;
    }

    public function create($patient_id, $scheduled_at, $status, $notes, $user_id) {
        $stmt = $this->pdo->prepare("
            INSERT INTO appointments (patient_id, scheduled_at, status, notes, created_by, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        if($stmt->execute([$patient_id, $scheduled_at, $status, $notes, $user_id])) {
            $this->logger->log("Created appointment for patient $patient_id", $user_id);
            return true;
        }
        return false;
    }

    public function update($id, $patient_id, $scheduled_at, $status, $notes, $user_id) {
        $stmt = $this->pdo->prepare("
            UPDATE appointments 
            SET patient_id=?, scheduled_at=?, status=?, notes=?, updated_at=NOW() 
            WHERE id=?
        ");
        if($stmt->execute([$patient_id, $scheduled_at, $status, $notes, $id])) {
            $this->logger->log("Updated appointment ID $id", $user_id);
            return true;
        }
        return false;
    }

    public function delete($id, $user_id) {
        $stmt = $this->pdo->prepare("DELETE FROM appointments WHERE id=?");
        if($stmt->execute([$id])) {
            $this->logger->log("Deleted appointment ID $id", $user_id);
            return true;
        }
        return false;
    }

    public function getAll() {
        return $this->pdo->query("
            SELECT a.id, a.patient_id, a.scheduled_at, a.status, a.notes, p.full_name AS patient_name
            FROM appointments a
            JOIN patients p ON a.patient_id = p.id
            ORDER BY a.scheduled_at DESC
        ")->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM appointments WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function render() {
        $successMsg = "";

        // Handle POST (Create/Update)
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'];
            $patient_id = $_POST['patient_id'];
            $scheduled_at = $_POST['scheduled_at'];
            $status = $_POST['status'];
            $notes = $_POST['notes'];

            if(!empty($_POST['appointment_id'])) {
                $this->update($_POST['appointment_id'], $patient_id, $scheduled_at, $status, $notes, $user_id);
                $successMsg = "Appointment updated!";
            } else {
                $this->create($patient_id, $scheduled_at, $status, $notes, $user_id);
                $successMsg = "Appointment created!";
            }
        }

        // Handle Delete
        if(isset($_GET['delete_id'])) {
            $this->delete($_GET['delete_id'], $_SESSION['user_id']);
            header("Location: ?section=appointments");
            exit;
        }

        // Handle Edit
        $editAppointment = isset($_GET['edit_id']) ? $this->getById($_GET['edit_id']) : null;

        // Patients list
        $patients = $this->pdo->query("SELECT id, full_name FROM patients ORDER BY full_name")->fetchAll();
        ?>

        <h2><?= $editAppointment ? "Edit" : "Add" ?> Appointment</h2>
        <?php if($successMsg) echo "<p style='color:green;'>$successMsg</p>"; ?>
        <form method="POST">
            <?php if($editAppointment): ?>
                <input type="hidden" name="appointment_id" value="<?= $editAppointment['id'] ?>">
            <?php endif; ?>

            <label>Patient:</label>
            <select name="patient_id" required>
                <?php foreach($patients as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= ($editAppointment && $editAppointment['patient_id']==$p['id']) ? "selected" : "" ?>>
                        <?= htmlspecialchars($p['full_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

            <label>Scheduled At:</label>
            <input type="datetime-local" name="scheduled_at" value="<?= $editAppointment['scheduled_at'] ?? '' ?>" required><br>

            <label>Status:</label>
            <input type="text" name="status" value="<?= $editAppointment['status'] ?? '' ?>"><br>

            <label>Notes:</label>
            <textarea name="notes"><?= $editAppointment['notes'] ?? '' ?></textarea><br>

            <button type="submit"><?= $editAppointment ? "Update" : "Add" ?> Appointment</button>
            <?php if($editAppointment): ?>
                <a href="?section=appointments">Cancel</a>
            <?php endif; ?>
        </form>

        <h2>All Appointments</h2>
        <table border="1" cellpadding="5">
        <tr>
            <th>ID</th><th>Patient</th><th>Scheduled At</th><th>Status</th><th>Notes</th><th>Actions</th>
        </tr>
        <?php foreach($this->getAll() as $a): ?>
        <tr>
            <td><?= $a['id'] ?></td>
            <td><?= htmlspecialchars($a['patient_name']) ?></td>
            <td><?= $a['scheduled_at'] ?></td>
            <td><?= htmlspecialchars($a['status']) ?></td>
            <td><?= htmlspecialchars($a['notes']) ?></td>
            <td>
                <a href="?section=appointments&edit_id=<?= $a['id'] ?>">Edit</a> |
                <a href="?section=appointments&delete_id=<?= $a['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
        </table>

        <?php
    }
}
?>
