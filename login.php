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

?>
<?php
include "layout/navbar.php";
?>
<style>
    body {
        background: linear-gradient(135deg, #667eea, #764ba2);
        font-family: Arial, sans-serif;
    }
    .login-container {
        max-width: 320px;
        margin: 80px auto 40px auto;
        background: white;
        padding: 40px 30px;
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        text-align: center;
    }
    h2 {
        margin-bottom: 20px;
        font-weight: 600;
        color: #333;
    }
    form {
        text-align: left;
    }
    label {
        display: block;
        margin-bottom: 6px;
        color: #555;
        font-weight: 500;
    }
    input[type="email"],
    input[type="password"] {
        width: 100%;
        padding: 10px 12px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 6px;
        box-sizing: border-box;
        font-size: 14px;
    }
    .form-text {
        font-size: 12px;
        color: #888;
        margin-top: -12px;
        margin-bottom: 15px;
    }
    .error-message {
        background-color: #f8d7da;
        color: #842029;
        padding: 10px 15px;
        border-radius: 6px;
        margin-bottom: 15px;
        font-size: 14px;
    }
    button {
        width: 100%;
        background-color: #2ecc71;
        color: white;
        padding: 12px 0;
        border: none;
        border-radius: 25px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    button:hover {
        background-color: #27ae60;
    }
    .links {
        margin-top: 15px;
        font-size: 12px;
        color: #666;
        display: flex;
        justify-content: space-between;
    }
    .links a {
        color: #666;
        text-decoration: none;
    }
    .links a:hover {
        text-decoration: underline;
    }
</style>

<div class="login-container">
    <h2>Log In</h2>
    <?php if (!empty($error)): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" novalidate>
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
        <label for="password">Password</label>
        <input id="password" name="password" type="password" required />
        <div class="form-text">
            Must be at least 8 characters with uppercase, lowercase, number, and special character.
        </div>
        <button type="submit">Log In</button>
    </form>
    <div class="links">
        <a href="/forgot_password.php">Forgot Password?</a>
        <a href="/privacy_policy.php">Privacy Policy</a>
        <a href="/terms_conditions.php">Terms & Condition</a>
    </div>
</div>
<?php
include "layout/footer.php"
?>

