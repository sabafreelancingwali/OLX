<?php
// login.php - User login handling with secure validation
 
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 
session_start();
include 'db.php';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
 
    // Validation
    if (empty($email) || empty($password)) {
        echo "<script>alert('Email and password are required!'); window.location.href = 'login.php';</script>";
        exit;
    }
 
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format!'); window.location.href = 'login.php';</script>";
        exit;
    }
 
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
 
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            echo "<script>alert('Login successful! Welcome, " . htmlspecialchars($user['username']) . "'); window.location.href = 'index.php';</script>";
        } else {
            echo "<script>alert('Invalid email or password!'); window.location.href = 'login.php';</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . htmlspecialchars($e->getMessage()) . "'); window.location.href = 'login.php';</script>";
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - OLX Clone</title>
    <style>
        /* Professional, OLX-inspired CSS */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }
        .login-container h2 {
            color: #002f34;
            margin-bottom: 25px;
            font-size: 2em;
            font-weight: bold;
        }
        .login-container input {
            width: 100%;
            padding: 12px;
            margin: 12px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .login-container input:focus {
            border-color: #23e5db;
            box-shadow: 0 0 5px rgba(35, 229, 219, 0.3);
            outline: none;
        }
        .login-container button {
            width: 100%;
            padding: 12px;
            background: #002f34;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        .login-container button:hover {
            background: #23e5db;
        }
        .login-container a {
            color: #002f34;
            text-decoration: none;
            font-size: 0.9em;
            display: inline-block;
            margin-top: 15px;
        }
        .login-container a:hover {
            text-decoration: underline;
            color: #23e5db;
        }
        @media (max-width: 768px) {
            .login-container {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login to OLX Clone</h2>
        <form method="POST" action="login.php">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
            <p>Don't have an account? <a href="signup.php">Signup here</a></p>
        </form>
    </div>
</body>
</html>
