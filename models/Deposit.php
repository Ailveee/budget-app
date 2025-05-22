<?php
require_once __DIR__ . '/../config/config.php';

class Deposit {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // Create a new deposit (optional $date if you want to manually set the timestamp)
    public function create($amount, $departmentId, $date = null) {
        if ($date) {
            $stmt = $this->db->prepare("INSERT INTO deposits (amount, deposited_at, department_id) VALUES (?, ?, ?)");
            return $stmt->execute([$amount, $date, $departmentId]);
        } else {
            // Let MySQL assign the default timestamp
            $stmt = $this->db->prepare("INSERT INTO deposits (amount, department_id) VALUES (?, ?)");
            return $stmt->execute([$amount, $departmentId]);
        }
    }

    // Get all deposits with department names, ordered by deposit date
    public function getAll() {
        return $this->db->query("
            SELECT d.*, dept.name AS department_name
            FROM deposits d
            LEFT JOIN departments dept ON d.department_id = dept.id
            ORDER BY d.deposited_at DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete a deposit by ID
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM deposits WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
