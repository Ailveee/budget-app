<?php
require_once __DIR__ . '/../config/config.php';
require_once 'User.php';

class Auth {
    private $userModel;
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
        $this->userModel = new User();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login($email, $password) {
    $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name']; // optional
        $_SESSION['user_full_name'] = $user['name']; // ✅ This is needed
        $_SESSION['user_role'] = $user['role'] == 1 ? 'admin' : 'user';
        $_SESSION['is_admin'] = $user['role'] == 1;

        return true;
    }

    return false;
    }

    

    public function logout() {
        session_destroy();
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function check() {
        return $this->isLoggedIn();
    }

    public function userId() {
        return $_SESSION['user_id'] ?? null;
    }

    public function isAdmin() {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
    }
}
