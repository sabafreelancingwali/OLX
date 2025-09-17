<?php
// profile.php - User profile management
 
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first!'); location.href = 'index.php';</script>";
    exit;
}
 
include 'db.php';
 
$user_id = $_SESSION['user_id'];
 
// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
 
// Fetch user's ads
$stmt = $pdo->prepare("SELECT ads.*, categories.name as category_name FROM ads JOIN categories ON ads.category_id = categories.id WHERE user_id = ?");
$stmt->execute([$user_id]);
$ads = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
// Handle updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['phone'];
    $location = $_POST['location'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];
 
    $stmt = $pdo->prepare("UPDATE users SET phone = ?, location = ?, password = ? WHERE id = ?");
    $stmt->execute([$phone, $location, $password, $user_id]);
    echo "<script>alert('Profile updated!'); location.href = 'profile.php';</script>";
}
 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        /* Amazing CSS similar to homepage */
        body { font-family: 'Arial', sans-serif; margin: 0; padding: 0; background: #f4f4f4; color: #333; }
        header { background: #002f34; color: white; padding: 20px; text-align: center; }
        .profile-form { max-width: 600px; margin: 20px auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .profile-form input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; }
        .profile-form button { padding: 10px; background: #002f34; color: white; border: none; cursor: pointer; width: 100%; }
        .ads-list { max-width: 800px; margin: 20px auto; }
        .ad-item { background: white; padding: 15px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .ad-item button { background: #ff0000; color: white; border: none; padding: 5px 10px; cursor: pointer; }
        @media (max-width: 768px) { .profile-form { width: 90%; } }
    </style>
</head>
<body>
    <header>
        <h1>Your Profile</h1>
    </header>
    <div class="profile-form">
        <form method="POST">
            <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
            <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
            <input type="password" name="password" placeholder="New Password (optional)">
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" placeholder="Phone">
            <input type="text" name="location" value="<?php echo htmlspecialchars($user['location']); ?>" placeholder="Location">
            <button type="submit">Update Profile</button>
        </form>
    </div>
    <div class="ads-list">
        <h2>Your Ads</h2>
        <?php foreach ($ads as $ad): ?>
            <div class="ad-item">
                <h3><?php echo htmlspecialchars($ad['title']); ?></h3>
                <p>Price: $<?php echo number_format($ad['price'], 2); ?> - Status: <?php echo $ad['status']; ?></p>
                <button onclick="location.href='edit_ad.php?id=<?php echo $ad['id']; ?>'">Edit</button>
                <button onclick="if(confirm('Delete?')) location.href='delete_ad.php?id=<?php echo $ad['id']; ?>'">Delete</button>
                <button onclick="if(confirm('Mark as Sold?')) location.href='mark_sold.php?id=<?php echo $ad['id']; ?>'">Mark Sold</button>
            </div>
        <?php endforeach; ?>
    </div>
    <script>
        // Inline JS if needed
    </script>
</body>
</html>
