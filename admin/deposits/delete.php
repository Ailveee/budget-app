<?php
require_once '../../models/Deposit.php';
$depositModel = new Deposit();
$id = $_GET['id'] ?? null;
if ($id) {
    $depositModel->delete($id);
}
header('Location: index.php');
