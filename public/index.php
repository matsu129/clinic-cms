<?php
// autoload
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/config/config.php';

use App\Controllers\AuthController;

session_start();

// get URL
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Load Controller
function loadController($controllerName, $method = 'index', $params = []) {
    $fullClass = 'App\\Controllers\\' . $controllerName;
    if (class_exists($fullClass)) {
        $controller = new $fullClass();
        if (method_exists($controller, $method)) {
            call_user_func_array([$controller, $method], $params);
            return;
        } else {
            http_response_code(500);
            echo "<h1>500 Internal Server Error</h1><p>Method '{$method}' not found in {$controllerName}.</p>";
            return;
        }
    }
    http_response_code(404);
    echo "<h1>404 Not Found</h1><p>Controller {$controllerName} not found.</p>";
}

// Routing
switch ($request) {
    case '/':
    case '/home':
        loadController('HomeController');
        break;

    case '/patients':
        loadController('PatientController');
        break;

    case '/appointments':
        loadController('AppointmentController');
        break;

    case '/audit-logs':
        loadController('AuditLogController');
        break;

  // --- Auth ---
    case '/auth/login':
        $auth = new AuthController($pdo);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['password'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];

            if ($auth->login($email, $password)) {
                header('Location: /dashboard');
                exit;
            } else {
                $error = 'Email/Password is not valid';
                include BASE_PATH.'/src/views/auth/login.php';
            }
        } else {
            $auth->showLogin();
        }
        break;

    case '/auth/logout':
        $auth = new AuthController($pdo);
        $auth->logout();
        break;

    case '/dashboard':
        $auth = new AuthController($pdo);
        if (!$auth->check()) {
            header('Location: /auth/login');
            exit;
        }
        $section = $_GET['section'] ?? 'patients';
        include BASE_PATH.'/src/views/home/dashboard.php';
        break;

    case '/doctors':
        loadController('DoctorController');
        break;

    case '/medical-notes':
        loadController('MedicalNoteController');
        break;

    case '/users':
        loadController('UserController');
        break;

    default:
        http_response_code(404);
        echo "<h1>404 Not Found</h1><p>Route '{$request}' does not exist.</p>";
        break;
}
