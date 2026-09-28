<?php
require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$message = trim($input['message'] ?? '');

if (empty($message)) {
    echo json_encode(['success' => false, 'error' => 'Message is empty']);
    exit;
}

$userId = $_SESSION['user_id'];
$db = getDB();

// Get or create chat
$stmt = $db->prepare("SELECT id FROM chats WHERE customer_id = ? AND status = 'active' ORDER BY id DESC LIMIT 1");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($chat = $result->fetch_assoc()) {
    $chatId = $chat['id'];
} else {
    $stmt = $db->prepare("INSERT INTO chats (customer_id, status) VALUES (?, 'active')");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $chatId = $db->insert_id;
}

// Insert message
$stmt = $db->prepare("INSERT INTO chat_messages (chat_id, sender_id, message) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $chatId, $userId, $message);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message_id' => $db->insert_id]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to send message']);
}
