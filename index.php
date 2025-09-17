<?php
// index.php - Homepage displaying featured and recent listings
 
session_start();
include 'db.php';
 
// Fetch categories
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
// Fetch recent ads (latest 10 active)
$stmt = $pdo->query("SELECT ads.*, users.username, categories.name as category_name FROM ads 
                     JOIN users ON ads.user_id = users.id 
                     JOIN categories ON ads.category_id = categories.id 
                     WHERE ads.status = 'active' ORDER BY ads.created_at DESC LIMIT 10");
$recent_ads = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
// Featured ads (e.g., high price or random, here random 5)
$stmt = $pdo->query("SELECT ads.*, users.username, categories.name as category_name FROM ads 
                     JOIN users ON ads.user_id = users.id 
                     JOIN categories ON ads.category_id = categories.id 
                     WHERE ads.status = 'active' ORDER BY RAND() LIMIT 5");
$featured_ads = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OLX Clone - Homepage</title>
    <style>
        /* Amazing, real-looking CSS */
        body { font-family: 'Arial', sans-serif; margin: 0; padding: 0; background: #f4f4f4; color: #333; }
        header { background: #002f34; color: white; padding: 20px; text-align: center; }
        header h1 { margin: 0; font-size: 2.5em; }
        nav { display: flex; justify-content: space-around; background: #fff; padding: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        nav a { text-decoration: none; color: #002f34; font-weight: bold; }
        .search-bar { margin: 20px auto; width: 80%; max-width: 800px; }
        .search-bar input { width: 70%; padding: 10px; border: 1px solid #ddd; border-radius: 4px 0 0 4px; }
        .search-bar button { padding: 10px; background: #002f34; color: white; border: none; border-radius: 0 4px 4px 0; cursor: pointer; }
        .section { padding: 20px; }
        .ads-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .ad-card { background: white; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .ad-card:hover { transform: scale(1.05); }
        .ad-card img { width: 100%; height: 200px; object-fit: cover; }
        .ad-info { padding: 15px; }
        .ad-info h3 { margin: 0; font-size: 1.2em; color: #002f34; }
        .ad-info p { margin: 5px 0; color: #555; }
        .price { font-weight: bold; color: #23e5db; }
        footer { background: #002f34; color: white; text-align: center; padding: 10px; margin-top: 20px; }
        @media (max-width: 768px) { .ads-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header>
        <h1>OLX Clone</h1>
    </header>
    <nav>
        <a href="#" onclick="document.getElementById('loginForm').style.display='block'; return false;">Login</a>
        <a href="#" onclick="document.getElementById('signupForm').style.display='block'; return false;">Signup</a>
        <a href="post_ad.php">Post Ad</a>
        <a href="profile.php">Profile</a>
        <a href="chat.php">Chat</a>
        <a href="search.php">Search</a>
    </nav>
    <div class="search-bar">
        <form action="search.php" method="GET">
            <input type="text" name="query" placeholder="Search for products...">
            <button type="submit">Search</button>
        </form>
    </div>
    <div class="section">
        <h2>Featured Listings</h2>
        <div class="ads-grid">
            <?php foreach ($featured_ads as $ad): ?>
                <div class="ad-card">
                    <?php $images = json_decode($ad['images'] ?? '[]', true); ?>
                    <img src="<?php echo $images[0] ?? 'placeholder.jpg'; ?>" alt="Ad Image">
                    <div class="ad-info">
                        <h3><?php echo htmlspecialchars($ad['title']); ?></h3>
                        <p class="price">$<?php echo number_format($ad['price'], 2); ?></p>
                        <p><?php echo htmlspecialchars($ad['category_name']); ?> - <?php echo htmlspecialchars($ad['location']); ?></p>
                        <p>By: <?php echo htmlspecialchars($ad['username']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="section">
        <h2>Recent Listings</h2>
        <div class="ads-grid">
            <?php foreach ($recent_ads as $ad): ?>
                <div class="ad-card">
                    <?php $images = json_decode($ad['images'] ?? '[]', true); ?>
                    <img src="<?php echo $images[0] ?? 'placeholder.jpg'; ?>" alt="Ad Image">
                    <div class="ad-info">
                        <h3><?php echo htmlspecialchars($ad['title']); ?></h3>
                        <p class="price">$<?php echo number_format($ad['price'], 2); ?></p>
                        <p><?php echo htmlspecialchars($ad['category_name']); ?> - <?php echo htmlspecialchars($ad['location']); ?></p>
                        <p>By: <?php echo htmlspecialchars($ad['username']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <footer>
        <p>&copy; 2025 OLX Clone. All rights reserved.</p>
    </footer>
    <!-- Inline modals for login/signup -->
    <div id="loginForm" style="display:none; position:fixed; top:20%; left:50%; transform:translate(-50%,-20%); background:white; padding:20px; border:1px solid #ddd; z-index:1000;">
        <form action="login.php" method="POST">
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>
    </div>
    <div id="signupForm" style="display:none; position:fixed; top:20%; left:50%; transform:translate(-50%,-20%); background:white; padding:20px; border:1px solid #ddd; z-index:1000;">
        <form action="signup.php" method="POST">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="text" name="phone" placeholder="Phone"><br>
            <input type="text" name="location" placeholder="Location"><br>
            <button type="submit">Signup</button>
        </form>
    </div>
    <script>
        // Inline JS for modals
        document.addEventListener('click', function(e) {
            if (e.target.id !== 'loginForm' && e.target.id !== 'signupForm') {
                document.getElementById('loginForm').style.display = 'none';
                document.getElementById('signupForm').style.display = 'none';
            }
        });
    </script>
</body>
</html>
