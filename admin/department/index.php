<?php
require_once '../../models/Department.php';
$departmentModel = new Department();
$departments = $departmentModel->getAll();
include '../../includes/header.php';
?>
<h1>Departments</h1>
<button class="add-button" onclick="window.location.href='create.php'">+ Add Department</button>
<table>
  <tr><th>Name</th><th>Actions</th></tr>
  <?php foreach ($departments as $dept): ?>
    <tr>
      <td><?= htmlspecialchars($dept['name']) ?></td>
      <td>
        <a href="edit.php?id=<?= $dept['id'] ?>">Edit</a> |
        <a href="delete.php?id=<?= $dept['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
<?php include '../../includes/footer.php'; ?>