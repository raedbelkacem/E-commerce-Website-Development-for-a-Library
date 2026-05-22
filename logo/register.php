<?php
include "header.php";

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
<div class="container py-5">
    <div class="row">
        <div class="col-lg-6 mx-auto border shadow p-4">
            <h2 class="text-center mb-4">Register</h2>
            <hr />
            <?php if ($success): ?>
                <div class="alert alert-success" role="alert">
                    Registration successful! You can now <a href="/login.php">login</a>.
                </div>
            <?php endif; ?>
            <form method="post">
                <div class="row mb-3">
                    <label class="col-sm-4 col-form-label">First_Name</label>
                    <div class="col-sm-8">
                        <input class="form-control" name="first_name" value="<?= $first_name?>">
                        <span class="text-danger"><?= $first_name_error?></span>
                     </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-4 col-form-label">Last_Name</label>
                    <div class="col-sm-8">
                        <input class="form-control" name="last_name" value="<?= $last_name?>">
                        <span class="text-danger"><?= $last_name_error?></span>
                     </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-4 col-form-label">Email</label>
                    <div class="col-sm-8">
                        <input class="form-control" name="email" value="<?= $email?>">
                        <span class="text-danger"><?= $email_error?></span>
                     </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-4 col-form-label">Phonee</label>
                    <div class="col-sm-8">
                        <input class="form-control" name="phone" value="<?= $phone?>">
                        <span class="text-danger"><?= $phone_error?></span>
                     </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-4 col-form-label">Address</label>
                    <div class="col-sm-8">
                        <input class="form-control" name="address" value="<?= $address?>">
                        <span class="text-danger"><?= $address_error?></span>
                     </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-4 col-form-label">Password</label>
                    <div class="col-sm-8">
                        <input class="form-control" type="password" name="password">
                        <span class="text-danger"><?= $password_error?></span>
                     </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-4 col-form-label">Confirm Password</label>
                    <div class="col-sm-8">
                        <input class="form-control" type="password" name="confirm_password" >
                        <span class="text-danger"><?= $confirm_password_error?></span>
                     </div>
                     <div class="row mb-3">
                        <div class="offset-sm-4 col-sm-4 d-grid">
                            <button type="submit" class="btn btn-primary">Register</button>
                        </div>
                        <div class="col-sm-4 d-grid">
                            <a href="/index.php" class="btn btn-outline-primary">
                                cancel
                            </a>
                        </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
include "footer.php";
?>
