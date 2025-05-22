<?php
require_once '../../models/Department.php';
$id = $_GET['id'] ?? null;
if ($id) {
  (new Department())->delete($id);
}
header('Location: index.php');
exit;

