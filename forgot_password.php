<?php
session_start();

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $error = "Please enter your email address.";
    } else {
        // Database connection parameters - replace with your actual credentials
        $db_host = "localhost";
        $db_user = "root";
        $db_pass = "";
        $db_name = "log_page";

        // Create connection
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            // Here you would generate a password reset token and send an email
            // For now, just simulate success
            $message = "If this email is registered, a password reset link has been sent.";
        } else {
            $message = "If this email is registered, a password reset link has been sent.";
        }

        $stmt->close();
        $conn->close();
    }
}

include "layout/header.php";
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-6 mx-auto border shadow p-4">
            <h2 class="text-center mb-4">Forgot Password</h2>
            <hr />

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($message)): ?>
                <div class="alert alert-info" role="alert">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="post" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label">Enter your email address</label>
                    <input class="form-control" id="email" name="email" type="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary me-md-2">Send Reset Link</button>
                    <a href="/login.php" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include "layout/footer.php";
?>
