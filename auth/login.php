<?php
session_start();
require_once '../config/config.php';
require_once '../models/Auth.php';

$auth = new Auth($pdo);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($auth->login($email, $password)) {
        if ($_SESSION['is_admin']) {
            header('Location: ../admin/dashboard.php');
        } else {
            header('Location: ../user/index.php');
        }
        exit();
    } else {
        $errors[] = "Invalid email or password.";
    }
}
?>

<?php include '../includes/header.php'; ?>

<h1>Login</h1>

<?php if ($errors): ?>
    <div class="errors">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST">
    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Login</button>
</form>

<?php include '../includes/footer.php'; ?>
