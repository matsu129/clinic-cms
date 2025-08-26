<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../vendor/autoload.php';

use Core\Auth;

class test_authController extends TestCase
{
    protected function setUp(): void
    {
        // セッションをリセット
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_SESSION = [];
    }

    public function testLoginSetsSessionVariables()
    {
        Auth::login(123);
        $this->assertEquals(123, $_SESSION['user_id']);
        $this->assertNotEmpty($_SESSION['token']);
        $this->assertIsString($_SESSION['token']);
        $this->assertArrayHasKey('last_activity', $_SESSION);
    }

    public function testCheckLogin()
    {
        $this->assertFalse(Auth::checkLogin());

        Auth::login(456);
        $this->assertTrue(Auth::checkLogin());
    }

    public function testGetToken()
    {
        Auth::login(789);
        $token = Auth::getToken();
        $this->assertEquals($_SESSION['token'], $token);
    }

    public function testValidateToken()
    {
        Auth::login(101);
        $validToken = $_SESSION['token'];
        $this->assertTrue(Auth::validateToken($validToken));
        $this->assertFalse(Auth::validateToken('invalid_token'));
    }

    public function testLogout()
    {
        Auth::login(202);
        Auth::logout();
        $this->assertArrayNotHasKey('user_id', $_SESSION);
    }

    public function testCheckTimeoutWithinLimit()
    {
        Auth::login(303);
        sleep(1);
        $this->assertTrue(Auth::checkTimeout(5));
    }

    public function testCheckTimeoutExceeded()
    {
        Auth::login(404);
        $_SESSION['last_activity'] = time() - 3600; // 1時間前
        $this->assertFalse(Auth::checkTimeout(10));
        $this->assertFalse(Auth::checkLogin());
    }
}
