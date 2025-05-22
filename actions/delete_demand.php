<?php
session_start();
require_once '../config/config.php';

// Protect admin page and logged users
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: demands.php");
    exit();
}

$demandId = (int)$_GET['id'];
$userId = $_SESSION['user_id'];
$isAdmin = $_SESSION['is_admin'] ?? false;

// Check if demand exists and ownership/admin rights
$stmt = $pdo->prepare("SELECT created_by FROM demands WHERE id = ?");
$stmt->execute([$demandId]);
$demand = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$demand) {
    header("Location: demands.php?msg=Demand+not+found");
    exit();
}

// Authorization check
if (!$isAdmin && $demand['created_by'] != $userId) {
    header("Location: demands.php?msg=Unauthorized+action");
    exit();
}

// Proceed with deletion
$stmt = $pdo->prepare("DELETE FROM demands WHERE id = ?");
$stmt->execute([$demandId]);

header("Location: demands.php?msg=Demand+deleted+successfully");
exit();
