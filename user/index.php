<?php
require_once '../models/Auth.php';
require_once '../models/Demand.php';

$auth = new Auth($pdo);
if (!$auth->check()) {
    header("Location: ../auth/login.php");
    exit;
}

$demandModel = new Demand();
$demands = $demandModel->getByUser($auth->userId());
?>

<?php include '../includes/header.php'; ?>

<h1>My Demands</h1>
<button class="add-button" onclick="window.location.href='create.php'">+new Demand</button>

<?php if (empty($demands)): ?>
    <p>No demands found.</p>
<?php else: ?>
    <table>
        <thead><tr><th>Amount</th><th>Date</th><th>Status</th><th>Reason</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($demands as $d): ?>
            <tr>
                <td><?= htmlspecialchars($d['amount']) ?> TND</td>
                <td><?= htmlspecialchars($d['demand_date']) ?></td>
                <td><?= htmlspecialchars($d['status']) ?></td>
                <td><?= htmlspecialchars($d['reason']) ?></td>
                <td>
                    <?php if ($d['status'] === 'pending'): ?>
                        <a href="edit.php?id=<?= $d['id'] ?>">Edit</a> |
                    <?php endif; ?>
                    <a href="delete.php?id=<?= $d['id'] ?>" onclick="return confirm('Are you sure?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
