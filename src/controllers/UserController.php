<?php
declare(strict_types=1);

namespace Controllers;

require_once __DIR__ . '/../models/User.php';
use Models\User;

class UserController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    // Get all users
    public function index(): array
    {
        return $this->userModel->getAll();
    }

    // Get user by ID
    public function show(int $id): ?array
    {
        return $this->userModel->findById($id);
    }

    // Register new user
    public function register(array $data): bool
    {
        if (!isset($data['email'], $data['password'], $data['full_name'])) {
            throw new \InvalidArgumentException('Missing required fields.');
        }
        return $this->userModel->create($data);
    }

    // Update user
    public function updateUser(int $id, string $email, string $fullName): bool
    {
        if (empty($email) || empty($fullName)) {
            throw new \InvalidArgumentException('Email and full name are required.');
        }
        return $this->userModel->update($id, [
            'email' => $email,
            'full_name' => $fullName,
            'role_id' => 2,      // default role
            'is_active' => 1     // default active
        ]);
    }

    // Delete user
    public function deleteUser(int $id): bool
    {
        return $this->userModel->delete($id);
    }

    // Login user
    public function login(string $email, string $password): array
    {
        $user = $this->userModel->findByEmail($email);
        if ($user && password_verify($password, $user['password_hash'])) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user'] = [
                'id' => $user['id'],
                'email' => $user['email'],
                'role_id' => $user['role_id']
            ];

            return [
                'success' => true,
                'message' => 'Login successful!',
                'user' => $user
            ];
        }

        return [
            'success' => false,
            'message' => 'Invalid email or password.'
        ];
    }

    // Logout user
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
}
