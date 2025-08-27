<?php

namespace App\Controllers;

class HomeController {
    public function index() {
        include __DIR__ . '/../views/home/index.php';
    }
    public function dashboard(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }
        include __DIR__ . '/../views/home/dashboard.php';
    }
}
