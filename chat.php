<?php
// chat.php - Chat system
 
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first!'); location.href = 'index.php';</script>";
    exit;
}
 
include 'db.php';
 
$user_id = $_SESSION['user_id'];
$ad_id = $_GET['ad_id'] ?? 0;
$seller_id = $_GET['seller_id'] ?? 0;
 
// If sending message
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'];
    $to_user_id = $_POST['to_user_id'];
    $ad_id = $_POST['ad_id'];
 
    $stmt = $pdo->prepare("INSERT INTO messages (from_user_id, to_user_id, ad_id, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $to_user_id, $ad_id, $message]);
    echo "<script>location.href = 'chat.php?ad_id=$ad_id&seller_id=$to_user_id';</script>";
}
 
// Fetch conversations or specific chat
if ($ad_id && $seller_id) {
    // Specific chat
    $stmt = $pdo->prepare("SELECT messages.*, u1.username as from_username, u2.username as to_username 
                           FROM messages 
                           JOIN users u1 ON messages.from_user_id = u1.id 
                           JOIN users u2 ON messages.to_user_id = u2.id 
                           WHERE ad_id = ? AND ((from_user_id = ? AND to_user_id = ?) OR (from_user_id = ? AND to_user_id = ?)) 
                           ORDER BY timestamp ASC");
    $stmt->execute([$ad_id, $user_id, $seller_id, $seller_id, $user_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
    // Mark as read
    $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE ad_id = ? AND to_user_id = ?");
    $stmt->execute([$ad_id, $user_id]);
} else {
    // List conversations
    $stmt = $pdo->prepare("SELECT DISTINCT ad_id, seller_id FROM messages WHERE from_user_id = ? OR to_user_id = ?");
    $stmt->execute([$user_id, $user_id]);
    $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // For simplicity, we'll assume chat.php without params shows list, but here we focus on specific.
}
 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <style>
        body { font-family: 'Arial', sans-serif; margin: 0; padding: 0; background: #f4f4f4; color: #333; }
        header { background: #002f34; color: white; padding: 20px; text-align: center; }
        .chat-window { max-width: 600px; margin: 20px auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); height: 400px; overflow-y: scroll; }
        .message { margin: 10px 0; padding: 10px; border-radius: 8px; }
        .message.sent { background: #dcf8c6; text-align: right; }
        .message.received { background: #fff; text-align: left; }
        .chat-form { max-width: 600px; margin: 0 auto; }
        .chat-form textarea { width: 80%; padding: 10px; }
        .chat-form button { padding: 10px; background: #002f34; color: white; border: none; cursor: pointer; }
        @media (max-width: 768px) { .chat-window, .chat-form { width: 90%; } }
    </style>
</head>
<body>
    <header>
        <h1>Chat</h1>
    </header>
    <?php if ($ad_id && $seller_id): ?>
        <div class="chat-window">
            <?php foreach ($messages as $msg): ?>
                <div class="message <?php echo $msg['from_user_id'] == $user_id ? 'sent' : 'received'; ?>">
                    <p><strong><?php echo htmlspecialchars($msg['from_username']); ?>:</strong> <?php echo htmlspecialchars($msg['message']); ?></p>
                    <small><?php echo $msg['timestamp']; ?></small>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="chat-form">
            <form method="POST">
                <input type="hidden" name="to_user_id" value="<?php echo $seller_id; ?>">
                <input type="hidden" name="ad_id" value="<?php echo $ad_id; ?>">
                <textarea name="message" placeholder="Type message..." required></textarea>
                <button type="submit">Send</button>
            </form>
        </div>
    <?php else: ?>
        <p>Select a chat from search or profile.</p>
    <?php endif; ?>
    <script>
        // Auto scroll to bottom
        const chatWindow = document.querySelector('.chat-window');
        if (chatWindow) chatWindow.scrollTop = chatWindow.scrollHeight;
    </script>
</body>
</html>
