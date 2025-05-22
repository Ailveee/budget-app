<?php
require_once '../../models/Department.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'] ?? '';
  if ($name) {
    (new Department())->create($name);
    header('Location: index.php');
    exit;
  }
}
include '../../includes/header.php';
?>
<h1>Create Department</h1>
<form method="POST">
  <label>Name:</label>
  <input type="text" name="name" required />
  <button type="submit">Save</button>
</form>
<?php include '../../includes/footer.php'; ?>
