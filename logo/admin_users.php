<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

include 'header.php';
include 'db.php'; // Assuming db.php contains DB connection logic

// Handle delete user request
if (isset($_GET['delete_user_id'])) {
    $delete_user_id = intval($_GET['delete_user_id']);
    if ($delete_user_id !== $_SESSION['user_id']) { // Prevent deleting self
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $delete_user_id);
        $stmt->execute();
        $stmt->close();
        header("Location: admin_users.php");
        exit;
    }
}

// Fetch all users
$result = $conn->query("SELECT id, username, email, role, created_at, is_active FROM users ORDER BY created_at DESC");

?>

<div class="container mt-5">
    <h1>User Management</h1>
    <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Active</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td><?= $user['is_active'] ? 'Yes' : 'No' ?></td>
                <td><?= htmlspecialchars($user['created_at']) ?></td>
                <td>
                    <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                    <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                    <a href="admin_users.php?delete_user_id=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
$conn->close();
include 'footer.php';
?>
