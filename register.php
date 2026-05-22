<?php
include "layout/navbar.php";

$first_name="";
$last_name="";
$email="";
$phone="";
$address="";


$first_name_error="";
$last_name_error="";
$email_error="";
$phone_error="";
$address_error="";
$password_error="";
$confirm_password_error="";

$error =false;
$success = false;

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'] ;
    $email = $_POST['email'] ;
    $phone = $_POST['phone'] ;
    $address = $_POST['address'] ;
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($first_name)){
        $first_name_error = "Please enter your first name.";
        $error = true;
    }
    if (empty($last_name)){
        $last_name_error = "Please enter your last name.";
        $error = true;
    }
    if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        $email_error="Email format is not valid";
        $error=true;
    }

    if(!preg_match("/^(\+|00\d{1,3})?[- ]?\d{7,12}$/",$phone)){
        $phone_error="phone format is not valid";
        $error=true;
    }

    if(strlen($password)<6){
        $password_error="Password must be at least 6 characters long.";
        $error=true;
    }
    if ($password != $confirm_password) {
        $confirm_password_error = "Passwords do not match.";
        $error = true;
    }

    if (!$error) {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $email_error = "Email is already registered.";
            $error = true;
        }
        $stmt->close();
    }

    if (!$error) {
        // Hash the password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into database
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone, address, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $role = 'user';
        $stmt->bind_param("sssssss", $first_name, $last_name, $email, $phone, $address, $password_hash, $role);

        if ($stmt->execute()) {
            $success = true;
            // Clear form values
            $first_name = $last_name = $email = $phone = $address = "";
        } else {
            $error = true;
            $email_error = "Error occurred during registration. Please try again.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<style>
    body {
        background: linear-gradient(135deg, #667eea, #764ba2);
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }
    .register-container {
        max-width: 360px;
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
    input[type="text"],
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
    .text-danger {
        background-color: #f8d7da;
        color: #842029;
        padding: 10px 15px;
        border-radius: 6px;
        margin-bottom: 15px;
        font-size: 14px;
    }
    .success-message {
        background-color: #d1e7dd;
        color: #0f5132;
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
</style>

<div class="register-container">
    <h2>Register</h2>
    <?php if ($success): ?>
        <div class="success-message">Registration successful! You can now <a href="/login.php">login</a>.</div>
    <?php endif; ?>
    <form method="post" novalidate>
        <label for="first_name">First Name</label>
        <input id="first_name" name="first_name" type="text" required value="<?= htmlspecialchars($first_name) ?>" />
        <?php if ($first_name_error): ?><div class="text-danger"><?= htmlspecialchars($first_name_error) ?></div><?php endif; ?>

        <label for="last_name">Last Name</label>
        <input id="last_name" name="last_name" type="text" required value="<?= htmlspecialchars($last_name) ?>" />
        <?php if ($last_name_error): ?><div class="text-danger"><?= htmlspecialchars($last_name_error) ?></div><?php endif; ?>

        <label for="email">Email</label>
        <input id="email" name="email" type="email" required value="<?= htmlspecialchars($email) ?>" />
        <?php if ($email_error): ?><div class="text-danger"><?= htmlspecialchars($email_error) ?></div><?php endif; ?>

        <label for="phone">Phone</label>
        <input id="phone" name="phone" type="text" required value="<?= htmlspecialchars($phone) ?>" />
        <?php if ($phone_error): ?><div class="text-danger"><?= htmlspecialchars($phone_error) ?></div><?php endif; ?>

        <label for="address">Address</label>
        <input id="address" name="address" type="text" required value="<?= htmlspecialchars($address) ?>" />
        <label for="password">Password</label>
        <input id="password" name="password" type="password" required />
        <?php if ($password_error): ?><div class="text-danger"><?= htmlspecialchars($password_error) ?></div><?php endif; ?>

        <label for="confirm_password">Confirm Password</label>
        <input id="confirm_password" name="confirm_password" type="password" required />
        <?php if ($confirm_password_error): ?><div class="text-danger"><?= htmlspecialchars($confirm_password_error) ?></div><?php endif; ?>

        <button type="submit">Register</button>
    </form>
</div>
<?php
include "layout/footer.php";
?>
