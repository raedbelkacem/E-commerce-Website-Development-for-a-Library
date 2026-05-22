<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destroy all session variables
$_SESSION = array();

// Destroy the session
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Logged Out</title>
    <style>
        /* Background gradient */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        /* Container styling */
        .container {
            background: white;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 320px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            font-weight: 600;
            color: #333;
        }
        p {
            color: #666;
            margin-bottom: 30px;
        }
        a.button {
            display: inline-block;
            background-color: #2ecc71;
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        a.button:hover {
            background-color: #27ae60;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>You have been logged out</h2>
        <p>Thank you for visiting. Please log in again to continue.</p>
        <a href="login.php" class="button">Log In</a>
    </div>
</body>
</html>
