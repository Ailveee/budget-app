<?php
session_start();
require_once '../../config/config.php';

// Allow only logged in admins
if (!isset($_SESSION['user_id']) || !($_SESSION['is_admin'] ?? false)) {
    header('Location: ../auth/login.php');
    exit();
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = trim($_POST['amount'] ?? '');
    $deposit_date = trim($_POST['deposit_date'] ?? '');

    if ($amount === '' || !is_numeric($amount) || $amount <= 0) {
        $errors[] = 'Please enter a valid amount.';
    }
    if ($deposit_date === '') {
        $errors[] = 'Please enter a deposit date.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO deposits (amount, deposit_date, created_by) VALUES (?, ?, ?)");
        $stmt->execute([$amount, $deposit_date, $_SESSION['user_id']]);
        $success = 'Deposit successfully recorded.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add Deposit</title>
    <link rel="stylesheet" href="../../assets/styles.css" />
</head>
<body>
<?php include '../../includes/header.php'; ?>

<h1>Add Deposit</h1>

<?php if ($success): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<?php if ($errors): ?>
    <ul class="error">
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="post" action="">
    <label>Amount (TND):
        <input type="number" step="0.01" min="0.01" name="amount" required value="<?= htmlspecialchars($_POST['amount'] ?? '') ?>">
    </label><br><br>

    <label>Deposit Date:
        <input type="date" name="deposit_date" required value="<?= htmlspecialchars($_POST['deposit_date'] ?? '') ?>">
    </label><br><br>

    <button type="submit">Add Deposit</button>
</form>

<p><a href="index.php">View All Deposits</a></p>

<?php include '../../includes/footer.php'; ?>
</body>
</html>
