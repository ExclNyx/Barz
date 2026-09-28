<?php
require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];
$role = $_SESSION['role'];
$db = getDB();

$chatId = isset($_GET['chat_id']) ? (int)$_GET['chat_id'] : 0;

if ($role === 'admin' || $role === 'super_admin') {
    // Admin viewing specific chat
    if ($chatId) {
        // Verify chat exists
        $stmt = $db->prepare("SELECT * FROM chats WHERE id = ?");
        $stmt->bind_param("i", $chatId);
        $stmt->execute();
        $chat = $stmt->get_result()->fetch_assoc();
        
        if (!$chat) {
            echo json_encode(['error' => 'Chat not found']);
            exit;
        }
        
        // Get messages
        $stmt = $db->prepare("
            SELECT cm.*, u.name as sender_name
            FROM chat_messages cm
            JOIN users u ON cm.sender_id = u.id
            WHERE cm.chat_id = ?
            ORDER BY cm.created_at ASC
        ");
        $stmt->bind_param("i", $chatId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $messages[] = [
                'id' => $row['id'],
                'message' => $row['message'],
                'sender_name' => $row['sender_name'],
                'is_sent' => ($row['sender_id'] == $userId),
                'time' => date('H:i', strtotime($row['created_at'])),
                'is_read' => (bool)$row['is_read']
            ];
        }
        
        // Mark as read
        $db->query("UPDATE chat_messages SET is_read = 1 WHERE chat_id = $chatId AND sender_id != $userId AND is_read = 0");
        
        echo json_encode(['messages' => $messages, 'chat_id' => $chatId]);
        exit;
    }
}

// Customer viewing their own chat
$chatId = 0;
// Get or create chat for customer
$stmt = $db->prepare("SELECT id FROM chats WHERE customer_id = ? AND status = 'active' ORDER BY id DESC LIMIT 1");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($chat = $result->fetch_assoc()) {
    $chatId = $chat['id'];
} else {
    // Create new chat
    $stmt = $db->prepare("INSERT INTO chats (customer_id, status) VALUES (?, 'active')");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $chatId = $db->insert_id;
}

// Get messages
$stmt = $db->prepare("
    SELECT cm.*, u.name as sender_name, 
           CASE WHEN cm.sender_id = ? THEN 1 ELSE 0 END as is_sent
    FROM chat_messages cm
    JOIN users u ON cm.sender_id = u.id
    WHERE cm.chat_id = ?
    ORDER BY cm.created_at ASC
");
$stmt->bind_param("ii", $userId, $chatId);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = [
        'id' => $row['id'],
        'message' => $row['message'],
        'sender_name' => $row['sender_name'],
        'is_sent' => (bool)$row['is_sent'],
        'time' => date('H:i', strtotime($row['created_at'])),
        'is_read' => (bool)$row['is_read']
    ];
}

// Mark received messages as read
$stmt = $db->prepare("UPDATE chat_messages SET is_read = 1 WHERE chat_id = ? AND sender_id != ? AND is_read = 0");
$stmt->bind_param("ii", $chatId, $userId);
$stmt->execute();

echo json_encode([
    'chat_id' => $chatId,
    'messages' => $messages,
    'unread_count' => 0
]);
