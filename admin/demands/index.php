<?php
require_once '../../models/Demand.php';
require_once '../../includes/header.php';

$demandModel = new Demand();
$demands = $demandModel->getAll();
?>

<h2>All Demands</h2>

<table>
    <tr>
        <th>User</th><th>Amount</th><th>Date</th><th>Status</th><th>Reason</th><th>Actions</th>
    </tr>
    <?php foreach ($demands as $d): ?>
        <tr>
            <td><?= htmlspecialchars($d['name']) ?></td>
            <td><?= $d['amount'] ?> TND</td>
            <td><?= $d['demand_date'] ?></td>
            <td><?= $d['status'] ?></td>
            <td><?= htmlspecialchars($d['reason']) ?></td>
            <td>
                <a href="edit.php?id=<?= $d['id'] ?>">Edit</a> |
                <a href="delete.php?id=<?= $d['id'] ?>" onclick="return confirm('Delete this demand?');">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include '../../includes/footer.php'; ?>