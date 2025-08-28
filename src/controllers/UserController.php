<?php
declare(strict_types=1);

namespace App\Controllers;

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../core/AuditLogger.php';

use App\Models\User;
use App\Core\AuditLogger;

class UserController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // get current userId
    private function getCurrentUserId(): ?int {
        return $_SESSION['user']['id'] ?? null;
    }

    // Get all users
    public function index(): array
    {
        $users = $this->userModel->getAll();
        AuditLogger::logAction("Fetched all users", $this->getCurrentUserId());
        return $users;
    }

    // Get user by ID
    public function show(int $id): ?array
    {
        $user = $this->userModel->findById($id);
        AuditLogger::logAction("Fetched user ID: $id", $this->getCurrentUserId());
        return $user;
    }

    // Register new user
    public function register(array $data): bool
    {
        if (!isset($data['email'], $data['password'], $data['full_name'])) {
            throw new \InvalidArgumentException('Missing required fields.');
        }

        $result = $this->userModel->create($data);
        AuditLogger::logAction(
            $result ? "Registered new user: {$data['email']}" : "Failed to register user: {$data['email']}",
            $this->getCurrentUserId()
        );

        return $result;
    }

    // Update user
    public function updateUser(int $id, string $email, string $fullName): bool
    {
        $result = $this->userModel->update($id, [
            'email' => $email,
            'full_name' => $fullName,
            'role_id' => 2,
            'is_active' => 1
        ]);

        AuditLogger::logAction(
            $result ? "Updated user ID: $id" : "Failed to update user ID: $id",
            $this->getCurrentUserId()
        );

        return $result;
    }

    // Delete user
    public function deleteUser(int $id): bool
    {
        $result = $this->userModel->delete($id);
        AuditLogger::logAction(
            $result ? "Deleted user ID: $id" : "Failed to delete user ID: $id",
            $this->getCurrentUserId()
        );
        return $result;
    }

    // Login user
    public function login(string $email, string $password): array
    {
        $user = $this->userModel->findByEmail($email);
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'email' => $user['email'],
                'role_id' => $user['role_id']
            ];

            AuditLogger::logAction("User logged in: {$email}", $user['id']);

            return [
                'success' => true,
                'message' => 'Login successful!',
                'user' => $user
            ];
        }

        AuditLogger::logAction("Failed login attempt: {$email}", $user['id'] ?? null);

        return [
            'success' => false,
            'message' => 'Invalid email or password.'
        ];
    }

    // Logout user
    public function logout(): void
    {
        $userId = $_SESSION['user']['id'] ?? null;
        $userEmail = $_SESSION['user']['email'] ?? 'unknown';
        AuditLogger::logAction("User logged out: $userEmail", $userId);
        session_destroy();
    }
}
