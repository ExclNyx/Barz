<?php
require_once __DIR__ . '/../includes/init.php';
requireAdmin();

$pageTitle = 'Chats - Admin';
$db = getDB();

$chatId = isset($_GET['chat_id']) ? (int)$_GET['chat_id'] : 0;

// Handle sending admin reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_message'])) {
    $replyChatId = (int)$_POST['chat_id'];
    $message = sanitize($_POST['message'] ?? '');
    $adminId = $_SESSION['user_id'];

    if ($message && $replyChatId) {
        $stmt = $db->prepare("INSERT INTO chat_messages (chat_id, sender_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $replyChatId, $adminId, $message);
        $stmt->execute();
        // Update chat updated_at
        $db->query("UPDATE chats SET updated_at = NOW() WHERE id = $replyChatId");
        $_SESSION['success'] = 'Balasan terkirim';
        header('Location: /Barz/admin/chats.php?chat_id=' . $replyChatId);
        exit;
    }
}

// If viewing specific chat - show conversation
if ($chatId) {
    $chat = $db->query("SELECT c.*, u.name as customer_name, u.phone as customer_phone, u.email as customer_email FROM chats c JOIN users u ON c.customer_id = u.id WHERE c.id = $chatId")->fetch_assoc();
    if (!$chat) {
        header('Location: /Barz/admin/chats.php');
        exit;
    }

    // Mark messages as read
    $db->query("UPDATE chat_messages SET is_read = 1 WHERE chat_id = $chatId AND sender_id != {$_SESSION['user_id']}");

    // Get messages
    $messages = $db->query("SELECT cm.*, u.name as sender_name FROM chat_messages cm JOIN users u ON cm.sender_id = u.id WHERE cm.chat_id = $chatId ORDER BY cm.created_at ASC")->fetch_all(MYSQLI_ASSOC);

    ob_start();
?>
<div class="page">
    <div class="page-top">
        <a href="/Barz/admin/chats.php" class="btn btn-s btn-o mr-3">
            <svg class="ic" viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg> Kembali
        </a>
        <h1>Chat: <?php echo htmlspecialchars($chat['customer_name']); ?></h1>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="notice"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="chat-shell max-w-[800px]">
        <div class="chat-head">
            <div class="avatar" aria-hidden="true">
                <svg class="ic" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div>
                <strong><?php echo htmlspecialchars($chat['customer_name']); ?></strong>
                <small><?php echo htmlspecialchars($chat['customer_email']); ?></small>
            </div>
        </div>

        <div id="chatMessagesAdmin" class="chat-log">
            <div class="msgcol">
                <?php foreach ($messages as $msg): ?>
                    <?php if ($msg['sender_id'] == $_SESSION['user_id']): ?>
                    <div class="msg sent">
                        <div class="bubble"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></div>
                        <span class="msg-time"><?php echo date('H:i', strtotime($msg['created_at'])); ?></span>
                    </div>
                    <?php else: ?>
                    <div class="msg in">
                        <div class="bubble"><span class="who"><?php echo htmlspecialchars($msg['sender_name']); ?></span><?php echo nl2br(htmlspecialchars($msg['message'])); ?></div>
                        <span class="msg-time"><?php echo date('H:i', strtotime($msg['created_at'])); ?></span>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <form method="POST" class="chat-form">
            <input type="hidden" name="chat_id" value="<?php echo $chatId; ?>">
            <input type="text" name="message" class="input" placeholder="Ketik balasan..." required autocomplete="off">
            <button type="submit" name="reply_message" class="sendbtn" aria-label="Kirim balasan">
                <svg class="ic" viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
            </button>
        </form>
    </div>

    <script>
    // Auto-refresh messages every 3 seconds
    setInterval(function() {
        fetch('/Barz/api/chat_messages.php?chat_id=<?php echo $chatId; ?>')
            .then(function(r){return r.json();})
            .then(function(data) {
                if (data.messages) {
                    var container = document.getElementById('chatMessagesAdmin').querySelector('.msgcol') || document.createElement('div');
                    if (!document.getElementById('chatMessagesAdmin').querySelector('.msgcol')) {
                        document.getElementById('chatMessagesAdmin').innerHTML = '<div class="msgcol"></div>';
                        container = document.getElementById('chatMessagesAdmin').querySelector('.msgcol');
                    }
                    var html = '';
                    data.messages.forEach(function(msg) {
                        if (msg.is_sent) {
                            html += '<div class="msg sent"><div class="bubble">' + msg.message + '</div><span class="msg-time">' + msg.time + '</span></div>';
                        } else {
                            html += '<div class="msg in"><div class="bubble"><span class="who">' + msg.sender_name + '</span>' + msg.message + '</div><span class="msg-time">' + msg.time + '</span></div>';
                        }
                    });
                    container.innerHTML = html;
                    document.getElementById('chatMessagesAdmin').scrollTop = document.getElementById('chatMessagesAdmin').scrollHeight;
                }
            });
    }, 3000);
    </script>
</div>
<?php
    $content = ob_get_clean();
    include __DIR__ . '/layout.php';
    return;
}

// List all chats
$chats = $db->query("
    SELECT c.*, u.name as customer_name, u.phone as customer_phone, u.email as customer_email,
           (SELECT COUNT(*) FROM chat_messages WHERE chat_id = c.id AND sender_id != c.customer_id AND is_read = 0) as unread_admin
    FROM chats c
    JOIN users u ON c.customer_id = u.id
    WHERE c.status = 'active'
    ORDER BY c.updated_at DESC
")->fetch_all(MYSQLI_ASSOC);

ob_start();
?>
<div class="page">
    <div class="page-top">
        <h1>Chat Customer</h1>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="notice"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if (empty($chats)): ?>
        <div class="card">
            <div class="center">
                <svg class="ic" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <p>Tidak ada chat aktif</p>
            </div>
        </div>
    <?php else: ?>
        <div class="chatgrid">
            <?php foreach ($chats as $chat): ?>
            <div class="chatcard">
                <div class="rowline">
                    <div>
                        <h4><?php echo htmlspecialchars($chat['customer_name']); ?></h4>
                        <small><?php echo htmlspecialchars($chat['customer_email']); ?></small>
                    </div>
                    <?php if ($chat['unread_admin'] > 0): ?>
                    <span class="count"><?php echo $chat['unread_admin']; ?> unread</span>
                    <?php endif; ?>
                </div>
                <p class="meta">
                    <svg class="ic" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg> <?php echo formatDate($chat['updated_at']); ?>
                </p>
                <a href="/Barz/admin/chats.php?chat_id=<?php echo $chat['id']; ?>" class="btn btn-s btn-p">Buka Chat</a>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>