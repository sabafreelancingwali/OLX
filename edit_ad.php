?php
// edit_ad.php - Edit ad
 
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first!'); location.href = 'index.php';</script>";
    exit;
}
 
include 'db.php';
 
$ad_id = $_GET['id'];
$user_id = $_SESSION['user_id'];
 
// Fetch ad
$stmt = $pdo->prepare("SELECT * FROM ads WHERE id = ? AND user_id = ?");
$stmt->execute([$ad_id, $user_id]);
$ad = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$ad) {
    echo "<script>alert('Ad not found!'); location.href = 'profile.php';</script>";
    exit;
}
 
// Fetch categories
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $location = $_POST['location'];
    $condition = $_POST['condition'];
 
    // Handle new images (append to existing)
    $images = json_decode($ad['images'] ?? '[]', true);
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $file_name = 'uploads/' . basename($_FILES['images']['name'][$key]);
            move_uploaded_file($tmp_name, $file_name);
            $images[] = $file_name;
        }
    }
    $images_json = json_encode($images);
 
    $stmt = $pdo->prepare("UPDATE ads SET title = ?, description = ?, price = ?, category_id = ?, images = ?, location = ?, condition = ? WHERE id = ?");
    $stmt->execute([$title, $description, $price, $category_id, $images_json, $location, $condition, $ad_id]);
    echo "<script>alert('Ad updated!'); location.href = 'profile.php';</script>";
}
 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Ad</title>
    <style>
        /* Similar CSS as post_ad */
        body { font-family: 'Arial', sans-serif; margin: 0; padding: 0; background: #f4f4f4; color: #333; }
        header { background: #002f34; color: white; padding: 20px; text-align: center; }
        .post-form { max-width: 600px; margin: 20px auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .post-form input, .post-form textarea, .post-form select { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; }
        .post-form button { padding: 10px; background: #002f34; color: white; border: none; cursor: pointer; width: 100%; }
    </style>
</head>
<body>
    <header>
        <h1>Edit Ad</h1>
    </header>
    <div class="post-form">
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="title" value="<?php echo htmlspecialchars($ad['title']); ?>" required>
            <textarea name="description" required><?php echo htmlspecialchars($ad['description']); ?></textarea>
            <input type="number" name="price" value="<?php echo $ad['price']; ?>" required step="0.01">
            <select name="category_id" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $ad['category_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="location" value="<?php echo htmlspecialchars($ad['location']); ?>">
            <select name="condition">
                <option value="new" <?php echo $ad['condition'] == 'new' ? 'selected' : ''; ?>>New</option>
                <option value="used" <?php echo $ad['condition'] == 'used' ? 'selected' : ''; ?>>Used</option>
                <option value="refurbished" <?php echo $ad['condition'] == 'refurbished' ? 'selected' : ''; ?>>Refurbished</option>
            </select>
            <input type="file" name="images[]" multiple accept="image/*"> (Existing images kept)
            <button type="submit">Update Ad</button>
        </form>
    </div>
</body>
</html>
