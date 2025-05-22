<?php
session_start();
require_once '../../config/config.php';

// Check admin session
if (!isset($_SESSION['user_id']) || !($_SESSION['is_admin'] ?? false)) {
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$demandId = (int)$_GET['id'];

// Get demand info
$stmt = $pdo->prepare("SELECT amount, status FROM demands WHERE id = ?");
$stmt->execute([$demandId]);
$demand = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$demand) {
    $_SESSION['message'] = "Demand not found.";
    header("Location: index.php");
    exit();
}

if (strtolower($demand['status']) !== 'pending') {
    $_SESSION['message'] = "Only pending demands can be accepted.";
    header("Location: index.php");
    exit();
}

// Budget calculations
$totalBudget = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM deposits")->fetchColumn();
$usedBudget = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM demands WHERE LOWER(status) = 'accepted'")->fetchColumn();

$availableBudget = $totalBudget - $usedBudget;

if ($demand['amount'] > $availableBudget) {
    $_SESSION['message'] = "Not enough budget to accept this demand.";
    header("Location: index.php");
    exit();
}

// Accept the demand
$stmt = $pdo->prepare("UPDATE demands SET status = 'Accepted' WHERE id = ?");
$stmt->execute([$demandId]);

$_SESSION['message'] = "Demand accepted successfully. Budget updated.";
header("Location: index.php");
exit();
