<?php
// search.php - Search and filters
 
include 'db.php';
 
$query = $_GET['query'] ?? '';
$category_id = $_GET['category_id'] ?? '';
$min_price = $_GET['min_price'] ?? 0;
$max_price = $_GET['max_price'] ?? PHP_INT_MAX;
$location = $_GET['location'] ?? '';
$condition = $_GET['condition'] ?? '';
 
// Build SQL
$sql = "SELECT ads.*, users.username, categories.name as category_name FROM ads 
        JOIN users ON ads.user_id = users.id 
        JOIN categories ON ads.category_id = categories.id 
        WHERE ads.status = 'active'";
$params = [];
 
if ($query) {
    $sql .= " AND (ads.title LIKE ? OR ads.description LIKE ?)";
    $params[] = "%$query%";
    $params[] = "%$query%";
}
if ($category_id) {
    $sql .= " AND ads.category_id = ?";
    $params[] = $category_id;
}
if ($min_price > 0) {
    $sql .= " AND ads.price >= ?";
    $params[] = $min_price;
}
if ($max_price < PHP_INT_MAX) {
    $sql .= " AND ads.price <= ?";
    $params[] = $max_price;
}
if ($location) {
    $sql .= " AND ads.location LIKE ?";
    $params[] = "%$location%";
}
if ($condition) {
    $sql .= " AND ads.condition = ?";
    $params[] = $condition;
}
 
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
// Fetch categories
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <style>
        /* Similar to homepage */
        body { font-family: 'Arial', sans-serif; margin: 0; padding: 0; background: #f4f4f4; color: #333; }
        header { background: #002f34; color: white; padding: 20px; text-align: center; }
        .filter-form { max-width: 800px; margin: 20px auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; }
        .filter-form input, .filter-form select { padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .filter-form button { grid-column: span 2; padding: 10px; background: #002f34; color: white; border: none; cursor: pointer; }
        .ads-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; max-width: 1200px; margin: 20px auto; }
        .ad-card { background: white; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .ad-card:hover { transform: scale(1.05); }
        .ad-card img { width: 100%; height: 200px; object-fit: cover; }
        .ad-info { padding: 15px; }
        .ad-info h3 { margin: 0; font-size: 1.2em; color: #002f34; }
        .ad-info p { margin: 5px 0; color: #555; }
        .price { font-weight: bold; color: #23e5db; }
        .contact-btn { background: #002f34; color: white; border: none; padding: 10px; cursor: pointer; width: 100%; }
    </style>
</head>
<body>
    <header>
        <h1>Search Results</h1>
    </header>
    <div class="filter-form">
        <form method="GET">
            <input type="text" name="query" value="<?php echo htmlspecialchars($query); ?>" placeholder="Search...">
            <select name="category_id">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $category_id ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="min_price" value="<?php echo $min_price > 0 ? $min_price : ''; ?>" placeholder="Min Price">
            <input type="number" name="max_price" value="<?php echo $max_price < PHP_INT_MAX ? $max_price : ''; ?>" placeholder="Max Price">
            <input type="text" name="location" value="<?php echo htmlspecialchars($location); ?>" placeholder="Location">
            <select name="condition">
                <option value="">All Conditions</option>
                <option value="new" <?php echo $condition == 'new' ? 'selected' : ''; ?>>New</option>
                <option value="used" <?php echo $condition == 'used' ? 'selected' : ''; ?>>Used</option>
                <option value="refurbished" <?php echo $condition == 'refurbished' ? 'selected' : ''; ?>>Refurbished</option>
            </select>
            <button type="submit">Apply Filters</button>
        </form>
    </div>
    <div class="ads-grid">
        <?php foreach ($results as $ad): ?>
            <div class="ad-card">
                <?php $images = json_decode($ad['images'] ?? '[]', true); ?>
                <img src="<?php echo $images[0] ?? 'placeholder.jpg'; ?>" alt="Ad Image">
                <div class="ad-info">
                    <h3><?php echo htmlspecialchars($ad['title']); ?></h3>
                    <p class="price">$<?php echo number_format($ad['price'], 2); ?></p>
                    <p><?php echo htmlspecialchars($ad['category_name']); ?> - <?php echo htmlspecialchars($ad['location']); ?></p>
                    <p>By: <?php echo htmlspecialchars($ad['username']); ?></p>
                    <button class="contact-btn" onclick="location.href='chat.php?ad_id=<?php echo $ad['id']; ?>&seller_id=<?php echo $ad['user_id']; ?>'">Contact Seller</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
