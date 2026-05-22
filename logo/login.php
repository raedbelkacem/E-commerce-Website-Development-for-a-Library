<?php
session_start();

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } 
    // Vérifie que le mot de passe est fort
    elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        $error = "Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.";
    } 
    else {
        // Paramètres de connexion à la base de données
        $db_host = "localhost";
        $db_user = "root";
        $db_pass = "";
        $db_name = "log_page";

        // Créer une connexion
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

        // Vérifier la connexion
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Préparer et exécuter la requête
        $stmt = $conn->prepare("SELECT id, first_name, last_name, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $first_name, $last_name, $hashed_password, $role);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                // Connexion réussie
                $_SESSION['user_id'] = $id;
                $_SESSION['first_name'] = $first_name;
                $_SESSION['last_name'] = $last_name;
                $_SESSION['role'] = $role;

                $stmt->close();
                $conn->close();

                header("Location: /index.php");
                exit;
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Invalid email or password.";
        }

        $stmt->close();
        $conn->close();
    }
}

include "header.php";
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-6 mx-auto border shadow p-4">
            <h2 class="text-center mb-4">Login</h2>
            <hr />

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input class="form-control" id="email" name="email" type="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input class="form-control" id="password" name="password" type="password" required>
                    <div class="form-text">
                        Must be at least 8 characters with uppercase, lowercase, number, and special character.
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary me-md-2">Login</button>
                    <a href="/index.php" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
            <div class="mt-3 text-end">
                <a href="/forgot_password.php">Forgot Password?</a>
            </div>
        </div>
    </div>
</div>

<?php
include "footer.php";
?>
