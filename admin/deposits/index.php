<?php
require_once '../../models/Deposit.php';
$deposits = (new Deposit())->getAll();
include '../../includes/header.php';
?>
<h1>Deposits</h1>
<button class="add-button" onclick="window.location.href='create.php'">+ Add Deposit</button>
<table>
  <tr><th>Amount</th><th>Date</th><th>Department</th><th>Actions</th></tr>
  <?php foreach ($deposits as $deposit): ?>
    <tr>
      <td><?= $deposit['amount'] ?> TND</td>
      <td><?= $deposit['deposited_at'] ?></td>
      <td><?= $deposit['department_name'] ?></td>
      <td>
        <a href="edit.php?id=<?= $deposit['id'] ?>">Edit</a> |
        <a href="delete.php?id=<?= $deposit['id'] ?>" onclick="return confirm('Delete this?')">Delete</a>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
<?php include '../../includes/footer.php'; ?>