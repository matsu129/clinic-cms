<?php
declare(strict_types=1);

namespace App\Models;

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../App/Models/ModelInterface.php';

use Core\Database;
use PDO;
use PDOException;
use App\Models\ModelInterface;

class Doctor implements ModelInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Create a new doctor record
     *
     * @param array $data
     * @return bool
     */
    public function create(array $data): bool
    {
        $sql = "INSERT INTO doctors (user_id, name, specialty, phone, email, created_at, updated_at)
                VALUES (:user_id, :name, :specialty, :phone, :email, NOW(), NOW())";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $data['user_id'] ?? null,
            ':name' => $data['name'],
            ':specialty' => $data['specialty'],
            ':phone' => $data['phone'],
            ':email' => $data['email']
        ]);
    }

    /**
     * Find a doctor by ID
     *
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM doctors WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Get all doctors
     *
     * @return array
     */
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM doctors ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update a doctor record by ID
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE doctors
                SET user_id = :user_id, name = :name, specialty = :specialty, phone = :phone, email = :email, updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $data['user_id'] ?? null,
            ':name' => $data['name'],
            ':specialty' => $data['specialty'],
            ':phone' => $data['phone'],
            ':email' => $data['email'],
            ':id' => $id
        ]);
    }

    /**
     * Delete a doctor record by ID
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM doctors WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
