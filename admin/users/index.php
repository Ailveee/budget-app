<?php
require_once '../../models/Auth.php';
require_once '../../models/User.php';

$auth = new Auth($pdo);
if (!$auth->check() || !$auth->isAdmin()) {
    header('Location: ../../auth/login.php');
    exit;
}

$userModel = new User();
$users = $userModel->getAllWithDepartments();
?>

<?php include '../../includes/header.php'; ?>

<h1>Manage Users</h1>
<button class="add-button" onclick="window.location.href='create.php'">+ Add New User</button>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Department</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['department_name'] ?? 'N/A') ?></td>
                <td><?= $user['role'] == 1 ? 'Admin' : 'User' ?></td>
                <td>
                    <a href="edit.php?id=<?= $user['id'] ?>">Edit</a> |
                    <a href="delete.php?id=<?= $user['id'] ?>" onclick="return confirm('Delete this user?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../../includes/footer.php'; ?>
