<?php
// delete_ad.php - Delete ad
 
session_start();
include 'db.php';
 
$ad_id = $_GET['id'];
$user_id = $_SESSION['user_id'];
 
$stmt = $pdo->prepare("UPDATE ads SET status = 'deleted' WHERE id = ? AND user_id = ?");
$stmt->execute([$ad_id, $user_id]);
echo "<script>alert('Ad deleted!'); location.href = 'profile.php';</script>";
?>
