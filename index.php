<?php
session_start();

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    // Redirect based on user role
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/index.php");
    }
    exit();
} else {
    // Not logged in, redirect to login page
    header("Location: auth/login.php");
    exit();
}
