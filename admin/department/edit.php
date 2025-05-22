<?php
require_once '../../models/Department.php';
$model = new Department();
$id = $_GET['id'] ?? null;
if (!$id || !$dept = $model->getById($id)) exit('Not found');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $model->update($id, $_POST['name']);
  header('Location: index.php'); exit;
}
include '../../includes/header.php';
?>
<h1>Edit Department</h1>
<form method="POST">
  <label>Name:</label>
  <input type="text" name="name" value="<?= htmlspecialchars($dept['name']) ?>" required />
  <button type="submit">Update</button>
</form>
<?php include '../../includes/footer.php'; ?>