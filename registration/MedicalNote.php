<?php
require_once __DIR__ . '/AuditLogger.php';

class MedicalNote {
    private $pdo;
    private $logger;

    public function __construct($pdo, $logger) {
        $this->pdo = $pdo;
        $this->logger = $logger;
    }

    public function create($patient_id, $note_text, $user_id) {
        $stmt = $this->pdo->prepare("
            INSERT INTO medical_notes (patient_id, note_text, created_by, created_at, updated_at) 
            VALUES (?, ?, ?, NOW(), NOW())
        ");
        if($stmt->execute([$patient_id, $note_text, $user_id])) {
            $this->logger->log("Created medical note for patient ID $patient_id", $user_id);
            return true;
        }
        return false;
    }

    public function update($id, $patient_id, $note_text, $user_id) {
        $stmt = $this->pdo->prepare("
            UPDATE medical_notes SET patient_id=?, note_text=?, updated_at=NOW() WHERE id=?
        ");
        if($stmt->execute([$patient_id, $note_text, $id])) {
            $this->logger->log("Updated medical note ID $id", $user_id);
            return true;
        }
        return false;
    }

    public function delete($id, $user_id) {
        $stmt = $this->pdo->prepare("DELETE FROM medical_notes WHERE id=?");
        if($stmt->execute([$id])) {
            $this->logger->log("Deleted medical note ID $id", $user_id);
            return true;
        }
        return false;
    }

    public function getAll() {
        return $this->pdo->query("
            SELECT mn.id, mn.patient_id, mn.note_text, mn.created_by, mn.created_at, mn.updated_at, p.full_name AS patient_name
            FROM medical_notes mn
            LEFT JOIN patients p ON mn.patient_id = p.id
            ORDER BY mn.created_at DESC
        ")->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM medical_notes WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function render() {
        $successMsg = "";

        // POST Create/Update
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'];
            $patient_id = $_POST['patient_id'];
            $note_text = $_POST['note_text'];

            if(!empty($_POST['note_id'])) {
                $this->update($_POST['note_id'], $patient_id, $note_text, $user_id);
                $successMsg = "Medical note updated!";
            } else {
                $this->create($patient_id, $note_text, $user_id);
                $successMsg = "Medical note created!";
            }
        }

        // Delete
        if(isset($_GET['delete_id'])) {
            $this->delete($_GET['delete_id'], $_SESSION['user_id']);
            header("Location: ?section=medical_notes");
            exit;
        }

        // Edit
        $editNote = isset($_GET['edit_id']) ? $this->getById($_GET['edit_id']) : null;

        // Patients list
        $patients = $this->pdo->query("SELECT id, full_name FROM patients ORDER BY full_name")->fetchAll();

        ?>
        <h2><?= $editNote ? "Edit" : "Add" ?> Medical Note</h2>
        <?php if($successMsg) echo "<p style='color:green;'>$successMsg</p>"; ?>

        <form method="POST">
            <?php if($editNote): ?>
                <input type="hidden" name="note_id" value="<?= $editNote['id'] ?>">
            <?php endif; ?>

            <label>Patient:</label>
            <select name="patient_id" required>
                <?php foreach($patients as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= ($editNote && $editNote['patient_id']==$p['id']) ? "selected" : "" ?>>
                        <?= htmlspecialchars($p['full_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

            <label>Note:</label>
            <textarea name="note_text" required><?= $editNote['note_text'] ?? '' ?></textarea><br>

            <button type="submit"><?= $editNote ? "Update" : "Add" ?> Note</button>
            <?php if($editNote): ?>
                <a href="?section=medical_notes">Cancel</a>
            <?php endif; ?>
        </form>

        <h2>All Medical Notes</h2>
        <table border="1" cellpadding="5">
            <tr>
                <th>ID</th><th>Patient</th><th>Note</th><th>Created At</th><th>Actions</th>
            </tr>
            <?php foreach($this->getAll() as $n): ?>
            <tr>
                <td><?= $n['id'] ?></td>
                <td><?= htmlspecialchars($n['patient_name']) ?></td>
                <td><?= htmlspecialchars($n['note_text']) ?></td>
                <td><?= $n['created_at'] ?></td>
                <td>
                    <a href="?section=medical_notes&edit_id=<?= $n['id'] ?>">Edit</a> |
                    <a href="?section=medical_notes&delete_id=<?= $n['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php
    }
}
?>
