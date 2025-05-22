<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine dashboard URL based on user role
$dashboardUrl = '/budget_app/user/index.php'; // default user dashboard
if (!empty($_SESSION['is_admin'])) {
    $dashboardUrl = '/budget_app/admin/dashboard.php';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Budget App</title>
    <link rel="stylesheet" href="/budget_app/assets/styles.css">
</head>

<body>

    <header>
    <nav>
        <div class="left">
            <a href="<?= $dashboardUrl ?>" class="app-name">💰 BudgetApp</a>
            <?php if (!empty($_SESSION['user_id'])): ?>
                <span>Hello, <?= htmlspecialchars($_SESSION['user_full_name'] ?? 'User') ?>!</span>
                <a href="<?= $dashboardUrl ?>">Home</a>
                <?php if (!empty($_SESSION['is_admin'])): ?>
                    <a href="/budget_app/admin/demands/index.php">Demands</a>
                    <a href="/budget_app/admin/users/index.php">Users</a>
                    <a href="/budget_app/admin/deposits/index.php">Deposits</a>
                    <a href="/budget_app/admin/department/index.php">Department</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="/budget_app/auth/login.php">Login</a>
                <a href="/budget_app/auth/register.php">Register</a>
            <?php endif; ?>
        </div>

        <?php if (!empty($_SESSION['user_id'])): ?>
            <div class="right">
                <a href="/budget_app/auth/logout.php">Logout</a>
            </div>
        <?php endif; ?>
    </nav>
</header>


    <hr>

    <main>