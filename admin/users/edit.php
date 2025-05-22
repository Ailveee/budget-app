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

if (!isset($_GET['id'])) {
    die('No user specified.');
}
$id = (int)$_GET['id'];

$user = $userModel->getById($id);
if (!$user) {
    die('User not found.');
}

$name = $user['name'];
$email = $user['email'];
$role = $user['role'];
$departmentId = $user['department_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $roleInput = $_POST['role'] ?? 'user';
    $departmentId = $_POST['department_id'] ?? null;
    $role = $roleInput === 'admin' ? 1 : 0;

    if (!$name || !$email) {
        $error = 'Name and email are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email.';
    } elseif ($password && $password !== $password_confirm) {
        $error = 'Passwords do not match.';
    } elseif ($password && strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } else {
        if ($userModel->update($id, $name, $email, $departmentId, $role, $password ?: null)) {
            header('Location: index.php');
            exit();
        } else {
            $error = 'Update failed, email might already be in use.';
        }
    }
}
?>

<?php include '../../includes/header.php'; ?>

<h1>Edit User</h1>

<?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post">
    <label>Name:<br>
        <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required>
    </label><br><br>

    <label>Email:<br>
        <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
    </label><br><br>

    <label>Password (leave blank to keep current):<br>
        <input type="password" name="password">
    </label><br><br>

    <label>Confirm Password:<br>
        <input type="password" name="password_confirm">
    </label><br><br>

    <label>Department:</label>
    <select name="department_id">
        <?php
        $departments = $userModel->getAllDepartments();
        foreach ($departments as $department) {
            echo '<option value="' . htmlspecialchars($department['id']) . '" ' . 
                ($department['id'] == $departmentId ? 'selected' : '') . '>' . 
                htmlspecialchars($department['name']) . '</option>';
        }
        ?>
    </select><br><br>

    <label>Role:<br>
        <select name="role">
            <option value="user" <?= $role === 0 ? 'selected' : '' ?>>User</option>
            <option value="admin" <?= $role === 1 ? 'selected' : '' ?>>Admin</option>
        </select>
    </label><br><br>

    <button type="submit">Update User</button>
</form>

<a href="index.php">Back to Users</a>

<?php include '../../includes/footer.php'; ?>
