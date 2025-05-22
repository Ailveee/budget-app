<?php
require_once __DIR__ . '/../config/config.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($full_name, $email, $password, $role, $department_id) {
    $stmt = $this->db->prepare("INSERT INTO users (name, email, password, role, department_id) VALUES (?, ?, ?, ?, ?)");
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $roleInt = $role === 'admin' ? 1 : 0;
    return $stmt->execute([$full_name, $email, $hashedPassword, $roleInt, $department_id]);
}

    public function getAll() {
        return $this->db->query("SELECT * FROM users")->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $name, $email, $departmentId, $role, $password = null) {
        if ($password) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, department_id = ?, role = ?, password = ? WHERE id = ?");
            return $stmt->execute([$name, $email, $departmentId, $role, $hash, $id]);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, department_id = ?, role = ? WHERE id = ?");
            return $stmt->execute([$name, $email, $departmentId, $role, $id]);
        }
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function getByDepartment($departmentId) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE department_id = ?");
        $stmt->execute([$departmentId]);
        return $stmt->fetchAll();
    }

    public function getAllDepartments() {
        $stmt = $this->db->query("SELECT * FROM departments");
        return $stmt->fetchAll();
    }

    public function getAllWithDepartments() {
    $stmt = $this->db->query("
        SELECT users.*, departments.name AS department_name 
        FROM users 
        LEFT JOIN departments ON users.department_id = departments.id
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
