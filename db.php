<?php
// db.php - Database connection file
 
$host = 'localhost'; // Assuming localhost, change if needed
$dbname = 'dbu25tvnllgw0o';
$username = 'uei4bkjtcem6s';
$password = 'wmhalmspfjgz';
 
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
