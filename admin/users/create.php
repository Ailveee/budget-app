<?php
require_once '../../models/Auth.php';
require_once '../../models/User.php';

$auth = new Auth($pdo);
if (!$auth->check() || !$auth->isAdmin()) {
    header('Location: ../../auth/login.php');
    exit;
}

$userModel = new User();
$error = '';
$full_name = '';
$email = '';
$role = 'user';
$department_id = '';

$departments = $userModel->getAllDepartments(); // You must have this function in your model

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $role = $_POST['role'] ?? 'user';
    $department_id = $_POST['department_id'] ?? '';

    if (!$full_name || !$email || !$password || !$password_confirm || !$department_id) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } elseif ($password !== $password_confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif (!$userModel->create($full_name, $email, $password, $role, $department_id)) {
        $error = 'Email already in use.';
    } else {
        header('Location: index.php');
        exit();
    }
}
?>

<?php include '../../includes/header.php'; ?>

<h1>Add New User</h1>

<?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post">
    <label>Full Name:<br>
        <input type="text" name="full_name" value="<?= htmlspecialchars($full_name) ?>" required>
    </label><br><br>

    <label>Email:<br>
        <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
    </label><br><br>

    <label>Password:<br>
        <input type="password" name="password" required>
    </label><br><br>

    <label>Confirm Password:<br>
        <input type="password" name="password_confirm" required>
    </label><br><br>

    <label>Role:<br>
        <select name="role">
            <option value="user" <?= $role === 'user' ? 'selected' : '' ?>>User</option>
            <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>
    </label><br><br>

    <label>Department:</label>
    <select name="department_id" required>
        <option value="">-- Select Department --</option>
        <?php foreach ($departments as $dept): ?>
            <option value="<?= $dept['id'] ?>" <?= $department_id == $dept['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($dept['name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <button type="submit">Create User</button>
</form>

<a href="index.php">Back to Users</a>

<?php include '../../includes/footer.php'; ?>
