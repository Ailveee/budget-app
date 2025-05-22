<?php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$errors = [];
$amount = '';
$demand_date = '';
$reason = '';

// Fetch user's department_id
$stmt = $pdo->prepare("SELECT department_id FROM users WHERE id = ?");
$stmt->execute([$userId]);
$departmentId = $stmt->fetchColumn();

if (!$departmentId) {
    die("Department not found for the user.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'] ?? '';
    $demand_date = $_POST['demand_date'] ?? '';
    $reason = $_POST['reason'] ?? '';

    if (empty($amount) || !is_numeric($amount) || $amount <= 0) {
        $errors[] = "Please enter a valid amount.";
    }

    if (empty($demand_date) || !DateTime::createFromFormat('Y-m-d', $demand_date)) {
        $errors[] = "Invalid or missing date. Use YYYY-MM-DD format.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO demands (user_id, department_id, amount, demand_date, reason, status)
                               VALUES (:user_id, :department_id, :amount, :demand_date, :reason, 'pending')");
        $stmt->execute([
            'user_id' => $userId,
            'department_id' => $departmentId,
            'amount' => $amount,
            'demand_date' => $demand_date,
            'reason' => $reason ?: null,
        ]);
        $_SESSION['message'] = "Demand created successfully.";
        
        // Redirect to the user's demands page
        header('Location: index.php');
        exit();
    }
}
?>

<?php include '../includes/header.php'; ?>

<h1>Create a New Demand</h1>

<?php if ($errors): ?>
    <div class="errors">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="create.php" method="POST">
    <label for="amount">Amount (TND):</label><br>
    <input type="number" step="0.01" name="amount" id="amount" value="<?= htmlspecialchars($amount) ?>" required><br><br>

    <label for="demand_date">Demand Date:</label><br>
    <input type="date" name="demand_date" id="demand_date" value="<?= htmlspecialchars($demand_date) ?>" required><br><br>

    <label for="reason">Reason (optional):</label><br>
    <textarea name="reason" id="reason" rows="4"><?= htmlspecialchars($reason) ?></textarea><br><br>

    <button type="submit">Submit Demand</button>
</form>

<?php include '../includes/footer.php'; ?>
