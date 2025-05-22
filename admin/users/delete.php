<?php
require_once '../../models/Auth.php';
require_once '../../models/User.php';

$auth = new Auth($pdo);
if (!$auth->check() || !$auth->isAdmin()) {
    header('Location: ../../auth/login.php');
    exit;
}

if (!isset($_GET['id'])) {
    die('No user specified.');
}

$id = (int)$_GET['id'];

$userModel = new User();

if ($userModel->delete($id)) {
    header('Location: index.php');
} else {
    die('Failed to delete user.');
}
