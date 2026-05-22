<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

$errors = [];
$success = false;

// Database connection parameters
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "log_page";

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Fetch current user data
$stmt = $conn->prepare("SELECT first_name, last_name, email, phone, address FROM users WHERE id = ?");
if (!$stmt) {
    die("Error fetching user: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $email, $phone, $address);
$stmt->fetch();
$stmt->close();

// // Fetch number of purchases
// $users_count = 0;
// $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE user_id = ?");
// if (!$stmt) {
//     die("Error fetching users: " . $conn->error);
// }
// $stmt->bind_param("i", $user_id);
// $stmt->execute();
// $stmt->bind_result($users_count);
// $stmt->fetch();
// $stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        $new_first_name = trim($_POST['first_name']);
        $new_last_name = trim($_POST['last_name']);
        $new_email = trim($_POST['email']);
        $new_phone = trim($_POST['phone']);
        $new_address = trim($_POST['address']);
        $new_password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if (empty($new_first_name)) $errors[] = "First name is required.";
        if (empty($new_last_name)) $errors[] = "Last name is required.";
        if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
        if (!preg_match("/^(\+|00\d{1,3})?[- ]?\d{7,12}$/", $new_phone)) $errors[] = "Invalid phone format.";
        if (empty($new_address)) $errors[] = "Address is required.";
        if (!empty($new_password)) {
            if (strlen($new_password) < 6) $errors[] = "Password must be at least 6 characters.";
            if ($new_password !== $confirm_password) $errors[] = "Passwords do not match.";
        }

        // Check if email is used by another user
        if (empty($errors)) {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            if (!$stmt) {
                die("Error checking email: " . $conn->error);
            }
            $stmt->bind_param("si", $new_email, $user_id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errors[] = "Email is already used by another account.";
            }
            $stmt->close();
        }

        // Update user
        if (empty($errors)) {
            if (!empty($new_password)) {
                $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, email=?, phone=?, address=?, password=? WHERE id=?");
                if (!$stmt) {
                    die("Error updating profile: " . $conn->error);
                }
                $stmt->bind_param("ssssssi", $new_first_name, $new_last_name, $new_email, $new_phone, $new_address, $password_hash, $user_id);
            } else {
                $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, email=?, phone=?, address=? WHERE id=?");
                if (!$stmt) {
                    die("Error updating profile: " . $conn->error);
                }
                $stmt->bind_param("sssssi", $new_first_name, $new_last_name, $new_email, $new_phone, $new_address, $user_id);
            }

            if ($stmt->execute()) {
                $success = true;
                $_SESSION['first_name'] = $new_first_name;
                $_SESSION['last_name'] = $new_last_name;
                $first_name = $new_first_name;
                $last_name = $new_last_name;
                $email = $new_email;
                $phone = $new_phone;
                $address = $new_address;
            } else {
                $errors[] = "Failed to update profile.";
            }
            $stmt->close();
        }
    } elseif (isset($_POST['delete'])) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        if (!$stmt) {
            die("Error deleting account: " . $conn->error);
        }
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            session_unset();
            session_destroy();
            header("Location: /index.php");
            exit;
        } else {
            $errors[] = "Failed to delete account.";
            $stmt->close();
        }
    }
}

$conn->close();

include "layout/header.php";
?>

<div class="container py-5">
    <h2>Profile</h2>

    <!-- <p><strong>Number of purchases:</strong> <?= htmlspecialchars($purchase_count) ?></p> -->

    <?php if ($success): ?>
        <div class="alert alert-success">Profile updated successfully.</div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" novalidate>
        <div class="mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($first_name) ?>" required>
        </div>
        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($last_name) ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>" required>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <input class="form-control" id="address" name="address" value="<?= htmlspecialchars($address) ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">New Password (leave blank to keep current)</label>
            <input class="form-control" type="password" id="password" name="password">
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm New Password</label>
            <input class="form-control" type="password" id="confirm_password" name="confirm_password">
        </div>
        <div class="d-flex justify-content-between">
            <button type="submit" name="update" class="btn btn-primary">Update Profile</button>
            <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">Delete Account</button>
        </div>
    </form>
</div>

<?php include "layout/footer.php"; ?>
