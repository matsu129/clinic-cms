<?php
require_once __DIR__ . '/AuditLogger.php';

class Patient {
    private $pdo;
    private $logger;

    public function __construct($pdo, $logger) {
        $this->pdo = $pdo;
        $this->logger = $logger;
    }

    public function create($full_name, $email, $phone, $user_id) {
        $stmt = $this->pdo->prepare("INSERT INTO patients (full_name, email, phone, created_at) VALUES (?, ?, ?, NOW())");
        if($stmt->execute([$full_name, $email, $phone])) {
            $this->logger->log("Created patient $full_name", $user_id);
            return true;
        }
        return false;
    }

    public function update($id, $full_name, $email, $phone, $user_id) {
        $stmt = $this->pdo->prepare("UPDATE patients SET full_name=?, email=?, phone=? WHERE id=?");
        if($stmt->execute([$full_name, $email, $phone, $id])) {
            $this->logger->log("Updated patient ID $id", $user_id);
            return true;
        }
        return false;
    }

    public function delete($id, $user_id) {
        $stmt = $this->pdo->prepare("DELETE FROM patients WHERE id=?");
        if($stmt->execute([$id])) {
            $this->logger->log("Deleted patient ID $id", $user_id);
            return true;
        }
        return false;
    }

    public function getAll() {
        return $this->pdo->query("SELECT * FROM patients ORDER BY full_name")->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM patients WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function render() {
        $successMsg = "";

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'];
            $full_name = $_POST['full_name'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];

            if(!empty($_POST['patient_id'])) {
                $this->update($_POST['patient_id'], $full_name, $email, $phone, $user_id);
                $successMsg = "Patient updated!";
            } else {
                $this->create($full_name, $email, $phone, $user_id);
                $successMsg = "Patient created!";
            }
        }

        if(isset($_GET['delete_id'])) {
            $this->delete($_GET['delete_id'], $_SESSION['user_id']);
            header("Location: ?section=patients");
            exit;
        }

        $editPatient = isset($_GET['edit_id']) ? $this->getById($_GET['edit_id']) : null;
        ?>
        <h2><?= $editPatient ? "Edit" : "Add" ?> Patient</h2>
        <?php if($successMsg) echo "<p style='color:green;'>$successMsg</p>"; ?>

        <form method="POST">
            <?php if($editPatient): ?>
                <input type="hidden" name="patient_id" value="<?= $editPatient['id'] ?>">
            <?php endif; ?>

            <label>Full Name:</label>
            <input type="text" name="full_name" value="<?= $editPatient['full_name'] ?? '' ?>" required><br>

            <label>Email:</label>
            <input type="email" name="email" value="<?= $editPatient['email'] ?? '' ?>" required><br>

            <label>Phone:</label>
            <input type="text" name="phone" value="<?= $editPatient['phone'] ?? '' ?>" required><br>

            <button type="submit"><?= $editPatient ? "Update" : "Add" ?> Patient</button>
            <?php if($editPatient): ?>
                <a href="?section=patients">Cancel</a>
            <?php endif; ?>
        </form>

        <h2>All Patients</h2>
        <table border="1" cellpadding="5">
        <tr><th>ID</th><th>Full Name</th><th>Email</th><th>Phone</th><th>Actions</th></tr>
        <?php foreach($this->getAll() as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['full_name']) ?></td>
            <td><?= htmlspecialchars($p['email']) ?></td>
            <td><?= htmlspecialchars($p['phone']) ?></td>
            <td>
                <a href="?section=patients&edit_id=<?= $p['id'] ?>">Edit</a> |
                <a href="?section=patients&delete_id=<?= $p['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
        </table>
        <?php
    }
}
