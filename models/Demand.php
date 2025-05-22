<?php
require_once __DIR__ . '/../config/config.php';

class Demand {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($userId, $amount, $date, $reason) {
        $stmt = $this->db->prepare("INSERT INTO demands (user_id, amount, demand_date, reason, status) VALUES (?, ?, ?, ?, 'pending')");
        return $stmt->execute([$userId, $amount, $date, $reason]);
    }

    public function getByUser($userId) {
        $stmt = $this->db->prepare("SELECT * FROM demands WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getAll() {
        return $this->db->query("SELECT d.*, u.name FROM demands d LEFT JOIN users u ON d.user_id = u.id ORDER BY created_at DESC")->fetchAll();
    }

    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE demands SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM demands WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
