<?php
// post_ad.php - Post new ad
 
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first!'); location.href = 'index.php';</script>";
    exit;
}
 
include 'db.php';
 
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
    $user_id = $_SESSION['user_id'];
 
    // Handle image uploads (simple, store in uploads folder)
    $images = [];
    if (!empty($_FILES['images']['name'][0])) {
        mkdir('uploads', 0777, true); // Create if not exists
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $file_name = 'uploads/' . basename($_FILES['images']['name'][$key]);
            move_uploaded_file($tmp_name, $file_name);
            $images[] = $file_name;
        }
    }
 
    $images_json = json_encode($images);
 
    $stmt = $pdo->prepare("INSERT INTO ads (user_id, title, description, price, category_id, images, location, condition) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $title, $description, $price, $category_id, $images_json, $location, $condition]);
    echo "<script>alert('Ad posted!'); location.href = 'profile.php';</script>";
}
 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Ad</title>
    <style>
        body { font-family: 'Arial', sans-serif; margin: 0; padding: 0; background: #f4f4f4; color: #333; }
        header { background: #002f34; color: white; padding: 20px; text-align: center; }
        .post-form { max-width: 600px; margin: 20px auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .post-form input, .post-form textarea, .post-form select { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; }
        .post-form button { padding: 10px; background: #002f34; color: white; border: none; cursor: pointer; width: 100%; }
        @media (max-width: 768px) { .post-form { width: 90%; } }
    </style>
</head>
<body>
    <header>
        <h1>Post New Ad</h1>
    </header>
    <div class="post-form">
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Title" required>
            <textarea name="description" placeholder="Description" required></textarea>
            <input type="number" name="price" placeholder="Price" required step="0.01">
            <select name="category_id" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="location" placeholder="Location">
            <select name="condition">
                <option value="new">New</option>
                <option value="used">Used</option>
                <option value="refurbished">Refurbished</option>
            </select>
            <input type="file" name="images[]" multiple accept="image/*">
            <button type="submit">Post Ad</button>
        </form>
    </div>
</body>
</html>
