<?php
session_start();
require_once '../../config/config.php';

// Protect admin page
if (!isset($_SESSION['user_id']) || !($_SESSION['is_admin'] ?? false)) {
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: demands.php");
    exit();
}

$demandId = (int)$_GET['id'];

// Delete the demand
$stmt = $pdo->prepare("DELETE FROM demands WHERE id = ?");
$stmt->execute([$demandId]);

$_SESSION['message'] = "Demand deleted successfully";
header("Location: index.php");
exit();
