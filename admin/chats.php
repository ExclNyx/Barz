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
<div class="page py-8 px-9 max-w-340 my-0 mx-auto max-[768px]:py-5 max-[768px]:px-4 max-[768px]:[padding-top:68px]">
    <div class="page-top flex justify-between items-start gap-4 flex-wrap [margin-bottom:28px] [padding-bottom:20px] border-b border-barz-line max-[768px]:flex-col max-[768px]:items-stretch">
        <a href="/Barz/admin/chats.php" class="btn btn-s btn-o text-barz-accent no-underline inline-flex items-center justify-center gap-2 py-2.5 px-4.5 rounded-2xl text-[0.86rem] font-semibold cursor-pointer border border-transparent transition-all font-sans bg-barz-surface text-barz-ink border-barz-line hover:bg-barz-bg hover:text-barz-ink hover:border-barz-footer py-1.5 px-3 text-[0.78rem] rounded-lg mr-3" >
            <svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-4 h-4" viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg> Kembali
        </a>
        <h1 class="font-sans text-barz-ink font-bold tracking-[-0.015em] text-[1.65rem] font-extrabold tracking-[-0.02em]">Chat: <?php echo htmlspecialchars($chat['customer_name']); ?></h1>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="notice rounded-[10px] py-3.5 px-4.5 text-[0.88rem] [margin-bottom:20px] flex items-center gap-2.5 border border-transparent"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="chat-shell bg-barz-surface border border-barz-line rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] flex flex-col overflow-hidden max-w-200" >
        <div class="chat-head py-4 px-5 border-b border-barz-line bg-barz-bg flex items-center gap-3">
            <div class="avatar w-10 h-10 rounded-[10px] bg-barz-deep text-white flex items-center justify-center flex-none" aria-hidden="true">
                <svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div>
                <strong class="block text-base text-barz-ink"><?php echo htmlspecialchars($chat['customer_name']); ?></strong>
                <small class="text-xs text-[#6b7a5c]"><?php echo htmlspecialchars($chat['customer_email']); ?></small>
            </div>
        </div>

        <div id="chatMessagesAdmin" class="chat-log p-5 bg-barz-bg h-112.5 overflow-y-auto">
            <div class="msgcol flex flex-col gap-3.5">
                <?php foreach ($messages as $msg): ?>
                    <?php if ($msg['sender_id'] == $_SESSION['user_id']): ?>
                    <div class="msg sent max-w-[75%] flex flex-col [&.sent]:[align-self:flex-end] [&.sent]:items-end [&.in]:[align-self:flex-start] [&.in]:items-start">
                        <div class="bubble py-2.5 px-4 rounded-[14px] text-sm leading-[1.45] shadow-[0_1px_2px_rgba(0,0,0,0.04)] [.msg.sent_&]:bg-barz-accent [.msg.sent_&]:text-white [.msg.sent_&]:[border-bottom-right-radius:3px] [.msg.in_&]:bg-barz-surface [.msg.in_&]:border [.msg.in_&]:border-barz-line [.msg.in_&]:text-barz-ink [.msg.in_&]:[border-bottom-left-radius:3px]"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></div>
                        <span class="msg-time text-[0.7rem] text-[#6b7a5c] [margin-top:4px] py-0 px-1"><?php echo date('H:i', strtotime($msg['created_at'])); ?></span>
                    </div>
                    <?php else: ?>
                    <div class="msg in max-w-[75%] flex flex-col [&.sent]:[align-self:flex-end] [&.sent]:items-end [&.in]:[align-self:flex-start] [&.in]:items-start">
                        <div class="bubble py-2.5 px-4 rounded-[14px] text-sm leading-[1.45] shadow-[0_1px_2px_rgba(0,0,0,0.04)] [.msg.sent_&]:bg-barz-accent [.msg.sent_&]:text-white [.msg.sent_&]:[border-bottom-right-radius:3px] [.msg.in_&]:bg-barz-surface [.msg.in_&]:border [.msg.in_&]:border-barz-line [.msg.in_&]:text-barz-ink [.msg.in_&]:[border-bottom-left-radius:3px]"><span class="who text-[0.82rem] font-medium text-[#6b7a5c] [margin-top:4px] block text-[0.72rem] font-bold [margin-bottom:2px] text-barz-accent"><?php echo htmlspecialchars($msg['sender_name']); ?></span><?php echo nl2br(htmlspecialchars($msg['message'])); ?></div>
                        <span class="msg-time text-[0.7rem] text-[#6b7a5c] [margin-top:4px] py-0 px-1"><?php echo date('H:i', strtotime($msg['created_at'])); ?></span>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <form method="POST" class="chat-form py-4 px-5 border-t border-barz-line bg-barz-surface flex gap-2.5">
            <input type="hidden" name="chat_id" value="<?php echo $chatId; ?>">
            <input type="text" name="message" class="input w-full py-2.5 px-3.5 border border-barz-line rounded-lg text-sm font-sans bg-barz-surface text-barz-ink transition-all focus:[outline:none] focus:border-barz-accent focus:shadow-[0_0_0_3px_rgba(136,144,99,0.12)] text-barz-muted-light" placeholder="Ketik balasan..." required autocomplete="off">
            <button type="submit" name="reply_message" class="sendbtn w-11 h-11 border rounded-[10px] bg-barz-accent text-white cursor-pointer flex items-center justify-center flex-none transition-all hover:bg-barz-accent-hover hover:[transform:translateY(-1px)]" aria-label="Kirim balasan">
                <svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]" viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
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
                        document.getElementById('chatMessagesAdmin').innerHTML = '<div class="msgcol flex flex-col gap-3.5"></div>';
                        container = document.getElementById('chatMessagesAdmin').querySelector('.msgcol');
                    }
                    var html = '';
                    data.messages.forEach(function(msg) {
                        if (msg.is_sent) {
                            html += '<div class="msg sent max-w-[75%] flex flex-col [&.sent]:[align-self:flex-end] [&.sent]:items-end [&.in]:[align-self:flex-start] [&.in]:items-start"><div class="bubble py-2.5 px-4 rounded-[14px] text-sm leading-[1.45] shadow-[0_1px_2px_rgba(0,0,0,0.04)] [.msg.sent_&]:bg-barz-accent [.msg.sent_&]:text-white [.msg.sent_&]:[border-bottom-right-radius:3px] [.msg.in_&]:bg-barz-surface [.msg.in_&]:border [.msg.in_&]:border-barz-line [.msg.in_&]:text-barz-ink [.msg.in_&]:[border-bottom-left-radius:3px]">' + msg.message + '</div><span class="msg-time text-[0.7rem] text-[#6b7a5c] [margin-top:4px] py-0 px-1">' + msg.time + '</span></div>';
                        } else {
                            html += '<div class="msg in max-w-[75%] flex flex-col [&.sent]:[align-self:flex-end] [&.sent]:items-end [&.in]:[align-self:flex-start] [&.in]:items-start"><div class="bubble py-2.5 px-4 rounded-[14px] text-sm leading-[1.45] shadow-[0_1px_2px_rgba(0,0,0,0.04)] [.msg.sent_&]:bg-barz-accent [.msg.sent_&]:text-white [.msg.sent_&]:[border-bottom-right-radius:3px] [.msg.in_&]:bg-barz-surface [.msg.in_&]:border [.msg.in_&]:border-barz-line [.msg.in_&]:text-barz-ink [.msg.in_&]:[border-bottom-left-radius:3px]"><span class="who text-[0.82rem] font-medium text-[#6b7a5c] [margin-top:4px]">' + msg.sender_name + '</span>' + msg.message + '</div><span class="msg-time text-[0.7rem] text-[#6b7a5c] [margin-top:4px] py-0 px-1">' + msg.time + '</span></div>';
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
<div class="page py-8 px-9 max-w-340 my-0 mx-auto max-[768px]:py-5 max-[768px]:px-4 max-[768px]:[padding-top:68px]">
    <div class="page-top flex justify-between items-start gap-4 flex-wrap [margin-bottom:28px] [padding-bottom:20px] border-b border-barz-line max-[768px]:flex-col max-[768px]:items-stretch">
        <h1 class="font-sans text-barz-ink font-bold tracking-[-0.015em] text-[1.65rem] font-extrabold tracking-[-0.02em]">Chat Customer</h1>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="notice rounded-[10px] py-3.5 px-4.5 text-[0.88rem] [margin-bottom:20px] flex items-center gap-2.5 border border-transparent"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if (empty($chats)): ?>
        <div class="card bg-barz-surface border border-barz-line rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] [margin-bottom:24px] overflow-hidden">
            <div class="center text-center text-[#6b7a5c] py-9 px-5">
                <svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <p>Tidak ada chat aktif</p>
            </div>
        </div>
    <?php else: ?>
        <div class="chatgrid grid grid-cols-[repeat(auto-fill,_minmax(300px,_1fr))] gap-4.5">
            <?php foreach ($chats as $chat): ?>
            <div class="chatcard bg-barz-surface border border-barz-line rounded-2xl p-5 shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] flex flex-col gap-3 transition hover:border-barz-accent hover:shadow-[0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)]">
                <div class="rowline flex justify-between items-start gap-2.5">
                    <div>
                        <h4 class="font-sans text-barz-ink font-bold tracking-[-0.015em] text-base [margin-bottom:2px]"><?php echo htmlspecialchars($chat['customer_name']); ?></h4>
                        <small class="text-xs text-[#6b7a5c]"><?php echo htmlspecialchars($chat['customer_email']); ?></small>
                    </div>
                    <?php if ($chat['unread_admin'] > 0): ?>
                    <span class="count inline-flex items-center justify-center py-0.5 px-2 rounded-full text-[0.72rem] font-bold bg-[#f5e0e0] text-barz-danger"><?php echo $chat['unread_admin']; ?> unread</span>
                    <?php endif; ?>
                </div>
                <p class="meta inline-flex items-center gap-1.5 text-[0.78rem] text-[#6b7a5c]">
                    <svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg> <?php echo formatDate($chat['updated_at']); ?>
                </p>
                <a href="/Barz/admin/chats.php?chat_id=<?php echo $chat['id']; ?>" class="btn btn-s btn-p text-barz-accent no-underline inline-flex items-center justify-center gap-2 py-2.5 px-4.5 rounded-2xl text-[0.86rem] font-semibold cursor-pointer border border-transparent transition-all font-sans bg-barz-accent text-white border-barz-accent hover:bg-barz-accent-hover hover:border-barz-accent-hover hover:[transform:translateY(-1px)] hover:shadow-[0_4px_10px_rgba(136,_144,_99,_.3)] py-1.5 px-3 text-[0.78rem] rounded-lg">Buka Chat</a>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>