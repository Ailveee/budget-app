<?php
require_once '../../models/Deposit.php';
require_once '../../models/Department.php';
require_once '../../includes/header.php';

$depositModel = new Deposit();
$departmentModel = new Department();
$departments = $departmentModel->getAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $departmentId = $_POST['department_id'];
    $amount = $_POST['amount'];
    $date = $_POST['deposit_date'];


    $depositModel->create($amount, $departmentId, $date);
    header('Location: index.php');
    exit;
}
?>

<h2>New Deposit</h2>
<form method="post">
    <label>Department:</label>
    <select name="department_id" required>
        <?php foreach ($departments as $dept): ?>
            <option value="<?= htmlspecialchars($dept['id']) ?>">
                <?= htmlspecialchars($dept['name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <label>Amount:</label>
    <input type="number" name="amount" step="0.01" required><br>

    <label>Date:</label>
    <input type="date" name="deposit_date"><br>


    <button type="submit">Add</button>
</form>

<a href="index.php">Back to Department</a>

<?php include '../../includes/footer.php'; ?>
