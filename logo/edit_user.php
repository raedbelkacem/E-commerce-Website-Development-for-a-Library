<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

include 'header.php';
include 'db.php';

$user_id = intval($_GET['id'] ?? 0);
if ($user_id <= 0) {
    header('Location: admin_users.php');
    exit;
}

// Fetch user data
$stmt = $conn->prepare("SELECT username, email, role, is_active FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $role, $is_active);
if (!$stmt->fetch()) {
    $stmt->close();
    header('Location: admin_users.php');
    exit;
}
$stmt->close();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['username'] ?? '');
    $new_email = trim($_POST['email'] ?? '');
    $new_role = $_POST['role'] ?? 'user';
    $new_is_active = isset($_POST['is_active']) ? 1 : 0;
    $new_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate inputs
    if (empty($new_username)) {
        $errors[] = "Username is required.";
    }
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    if ($new_password !== '' && $new_password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    if ($new_password !== '' && !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $new_password)) {
        $errors[] = "Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.";
    }

    if (empty($errors)) {
        // Check for username/email uniqueness excluding current user
        $stmt = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
        $stmt->bind_param("ssi", $new_username, $new_email, $user_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Username or email already in use by another user.";
        }
        $stmt->close();
    }

    if (empty($errors)) {
        // Update user
        if ($new_password !== '') {
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ?, is_active = ?, password = ? WHERE id = ?");
            $stmt->bind_param("sssisi", $new_username, $new_email, $new_role, $new_is_active, $password_hash, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ?, is_active = ? WHERE id = ?");
            $stmt->bind_param("sssii", $new_username, $new_email, $new_role, $new_is_active, $user_id);
        }
        if ($stmt->execute()) {
            $success = true;
            $username = $new_username;
            $email = $new_email;
            $role = $new_role;
            $is_active = $new_is_active;
        } else {
            $errors[] = "Failed to update user.";
        }
        $stmt->close();
    }
}

?>

<div class="container mt-5">
    <h1>Edit User</h1>
    <a href="admin_users.php" class="btn btn-secondary mb-3">Back to User Management</a>

    <?php if ($success): ?>
        <div class="alert alert-success">User updated successfully.</div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" novalidate>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input id="username" name="username" type="text" class="form-control" required value="<?= htmlspecialchars($username) ?>">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" class="form-control" required value="<?= htmlspecialchars($email) ?>">
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select id="role" name="role" class="form-select" required>
                <option value="user" <?= $role === 'user' ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>

        <div class="form-check mb-3">
            <input id="is_active" name="is_active" type="checkbox" class="form-check-input" <?= $is_active ? 'checked' : '' ?>>
            <label for="is_active" class="form-check-label">Active</label>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">New Password (leave blank to keep current)</label>
            <input id="password" name="password" type="password" class="form-control" placeholder="New Password">
        </div>

        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm New Password</label>
            <input id="confirm_password" name="confirm_password" type="password" class="form-control" placeholder="Confirm New Password">
        </div>

        <button type="submit" class="btn btn-primary">Update User</button>
    </form>
</div>

<?php
$conn->close();
include 'footer.php';
?>
