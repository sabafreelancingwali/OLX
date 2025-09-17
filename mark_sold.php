
<?php
// mark_sold.php - Mark ad as sold
 
session_start();
include 'db.php';
 
$ad_id = $_GET['id'];
$user_id = $_SESSION['user_id'];
 
$stmt = $pdo->prepare("UPDATE ads SET status = 'sold' WHERE id = ? AND user_id = ?");
$stmt->execute([$ad_id, $user_id]);
echo "<script>alert('Ad marked as sold!'); location.href = 'profile.php';</script>";
?>
