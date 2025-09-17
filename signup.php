<?php
// signup.php - User signup handling with validation and error handling
 
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 
session_start();
include 'db.php';
 
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $location = trim($_POST['location'] ?? '');
 
    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        echo "<script>alert('Username, email, and password are required!'); window.location.href = 'signup.php';</script>";
        exit;
    }
 
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format!'); window.location.href = 'signup.php';</script>";
        exit;
    }
 
    // Validate password length
    if (strlen($password) < 6) {
        echo "<script>alert('Password must be at least 6 characters long!'); window.location.href = 'signup.php';</script>";
        exit;
    }
 
    // Check for duplicate username or email
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetchColumn() > 0) {
            echo "<script>alert('Username or email already exists!'); window.location.href = 'signup.php';</script>";
            exit;
        }
 
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
 
        // Insert user
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, phone, location) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password, $phone, $location]);
 
        // Auto-login after signup
        $stmt = $pdo->prepare("SELECT id, username FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
 
        echo "<script>alert('Signup successful! Welcome, " . htmlspecialchars($username) . "'); window.location.href = 'index.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . htmlspecialchars($e->getMessage()) . "'); window.location.href = 'signup.php';</script>";
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - OLX Clone</title>
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
        .signup-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }
        .signup-container h2 {
            color: #002f34;
            margin-bottom: 25px;
            font-size: 2em;
            font-weight: bold;
        }
        .signup-container input {
            width: 100%;
            padding: 12px;
            margin: 12px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .signup-container input:focus {
            border-color: #23e5db;
            box-shadow: 0 0 5px rgba(35, 229, 219, 0.3);
            outline: none;
        }
        .signup-container button {
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
        .signup-container button:hover {
            background: #23e5db;
        }
        .signup-container a {
            color: #002f34;
            text-decoration: none;
            font-size: 0.9em;
            display: inline-block;
            margin-top: 15px;
        }
        .signup-container a:hover {
            text-decoration: underline;
            color: #23e5db;
        }
        @media (max-width: 768px) {
            .signup-container {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Signup to OLX Clone</h2>
        <form method="POST" action="signup.php">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password (min 6 chars)" required>
            <input type="text" name="phone" placeholder="Phone (Optional)">
            <input type="text" name="location" placeholder="Location (Optional)">
            <button type="submit">Signup</button>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </form>
    </div>
    <script>
        // Client-side validation for better UX
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.querySelector('input[name="password"]').value;
            if (password.length < 6) {
                alert('Password must be at least 6 characters long!');
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
