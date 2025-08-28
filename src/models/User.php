<?php
declare(strict_types=1);

namespace App\Models;


use App\Core\Database;
use PDO;
use PDOException;

class User implements ModelInterface
{
    private PDO $db;
    private string $table = 'users';

    public function __construct()
    {
        $dbInstance = Database::getInstance();
        $this->db = $dbInstance->getConnection();
    }

    /**
     * 全ユーザー取得
     */
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * IDでユーザーを取得
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result !== false ? $result : null;
    }

    /**
     * Emailでユーザーを検索（独自メソッド）
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result !== false ? $result : null;
    }

    /**
     * 新規ユーザー作成
     */
    public function create(array $data): bool
    {
        try {
            $sql = "INSERT INTO {$this->table} 
                (email, password_hash, full_name, role_id, is_active, created_at, updated_at)
                VALUES (:email, :password_hash, :full_name, :role_id, :is_active, NOW(), NOW())";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':email' => $data['email'],
                ':password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
                ':full_name' => $data['full_name'],
                ':role_id' => $data['role_id'] ?? 2,
                ':is_active' => $data['is_active'] ?? 1
            ]);
        } catch (PDOException $e) {
            error_log("User create error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ユーザー更新
     */
    public function update(int $id, array $data): bool
    {
        try {
            $sql = "UPDATE {$this->table} 
                SET email = :email, full_name = :full_name, role_id = :role_id, 
                    is_active = :is_active, updated_at = NOW(), profile_pic = :profile_pic
                WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':email' => $data['email'],
                ':full_name' => $data['full_name'],
                ':role_id' => $data['role_id'],
                ':is_active' => $data['is_active'],
                ':id' => $id,
                ':profile_pic' => $data['profile_pic']
            ]);
        } catch (PDOException $e) {
            error_log("User update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ユーザー削除
     */
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("User delete error: " . $e->getMessage());
            return false;
        }
    }
}
