<?php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'] ?? 'user';

$demandId = $_GET['id'] ?? null;
if (!$demandId || !is_numeric($demandId)) {
    header('Location: ../user/index.php');
    exit;
}

// Fetch demand
$stmt = $pdo->prepare("SELECT * FROM demands WHERE id = ?");
$stmt->execute([$demandId]);
$demand = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$demand) {
    // Could redirect with error message instead of die
    die('Demand not found.');
}

// Access control: only creator or admin
if ($userRole !== 'admin' && $demand['created_by'] != $userId) {
    die('Access denied.');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = trim($_POST['amount'] ?? '');
    $demand_date = trim($_POST['demand_date'] ?? '');
    $reason = trim($_POST['reason'] ?? '');
    $newStatus = $demand['status']; // default keep same

    if ($userRole === 'admin' && isset($_POST['status'])) {
        $newStatus = trim($_POST['status']);
        $validStatuses = ['Pending', 'Accepted', 'Rejected'];
        if (!in_array($newStatus, $validStatuses, true)) {
            $errors[] = "Invalid status selected.";
        }
    }

    if ($amount === '' || !filter_var($amount, FILTER_VALIDATE_FLOAT) || floatval($amount) <= 0) {
        $errors[] = "Please enter a valid amount.";
    }

    if ($demand_date === '') {
        $errors[] = "Demand date is required.";
    }

    // Budget check if admin accepting new demand
    if ($userRole === 'admin' && $newStatus === 'Accepted' && $demand['status'] !== 'Accepted') {
        $totalBudget = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM deposits")->fetchColumn();
        $usedBudget = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM demands WHERE status = 'Accepted'")->fetchColumn();
        $availableBudget = $totalBudget - $usedBudget;

        if (floatval($amount) > $availableBudget) {
            $errors[] = "Cannot accept demand: insufficient available budget (Available: TND " . number_format($availableBudget, 2) . ").";
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE demands SET amount = ?, demand_date = ?, reason = ?, status = ? WHERE id = ?");
        $stmt->execute([floatval($amount), $demand_date, $reason, $newStatus, $demandId]);

        if ($userRole === 'admin') {
            $_SESSION['message'] = "Demand updated successfully.";
            header('Location: ../admin/demands/index.php');
        } else {
            $_SESSION['message'] = "Your demand has been updated.";
            header('Location: ../user/index.php');
        }
        
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Update Demand</title>
    <link rel="stylesheet" href="../assets/styles.css" />
</head>
<body>

<?php include '../includes/header.php'; ?>

<main>
    <h2>Update Demand</h2>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <label for="amount">Amount:</label>
        <input type="number" step="0.01" min="0.01" id="amount" name="amount" value="<?= htmlspecialchars($demand['amount']) ?>" required>

        <label for="demand_date">Demand Date:</label>
        <input type="date" id="demand_date" name="demand_date" value="<?= htmlspecialchars($demand['demand_date']) ?>" required>

        <label for="reason">Reason:</label>
        <textarea id="reason" name="reason"><?= htmlspecialchars($demand['reason']) ?></textarea>

        <?php if ($userRole === 'admin'): ?>
            <label for="status">Status:</label>
            <select id="status" name="status">
                <option value="Pending" <?= $demand['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Accepted" <?= $demand['status'] === 'Accepted' ? 'selected' : '' ?>>Accepted</option>
                <option value="Rejected" <?= $demand['status'] === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
            </select>
        <?php else: ?>
            <p>Status: <strong><?= htmlspecialchars($demand['status']) ?></strong> (Only admin can change status)</p>
        <?php endif; ?>

        <button type="submit">Update Demand</button>
    </form>

    <p>
        <a href="delete_demand.php?id=<?= $demandId ?>" onclick="return confirm('Are you sure you want to delete this demand?');">Delete Demand</a>
    </p>

    <?php if ($userRole === 'admin'): ?>
        <p><a href="../admin/demands/index.php?id=<?= $demandId ?>">View Demand Details</a></p>
    <?php else: ?>
        <p><a href="../user/index.php">Back to Demands List</a></p>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>

</body>
</html>
