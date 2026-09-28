<?php
requireLogin();
$pageTitle = 'Chat - Barz Barbershop';

ob_start();
?>

<section class="section wrap">
    <div class="page-head">
        <h1>Live Chat</h1>
        <p><span class="online-dot"><i></i> Online — admin membalas dalam 5-15 menit</span></p>
    </div>

    <div class="chat-shell">
        <div class="chat-head">
            <div class="avatar" aria-hidden="true">
                <svg class="ic" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div>
                <strong>Admin Barz</strong>
                <small>Membalas dalam 5-15 menit</small>
            </div>
        </div>

        <div id="chatMessagesPage" class="chat-log">
            <div class="chat-empty">Memuat pesan...</div>
        </div>

        <form id="chatFormPage" class="chat-form">
            <input type="text" id="chatMessageInputPage" class="input" placeholder="Tulis pesan..." required autocomplete="off">
            <button type="submit" class="sendbtn" aria-label="Kirim pesan">
                <svg class="ic" viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
            </button>
        </form>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadMessages();
    setInterval(loadMessages, 3000);

    document.getElementById('chatFormPage').addEventListener('submit', function(e) {
        e.preventDefault();
        sendMessagePage();
    });
});

function escS(s) {
    var d = document.createElement('div');
    d.appendChild(document.createTextNode(s));
    return d.innerHTML;
}

function loadMessages() {
    fetch('/Barz/api/chat_messages.php')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var c = document.getElementById('chatMessagesPage');
            if (data.messages && data.messages.length > 0) {
                var html = '<div class="msgcol">';
                data.messages.forEach(function(msg) {
                    if (msg.is_sent) {
                        html += '<div class="msg sent"><div class="bubble">' + escS(msg.message) + '</div><span class="msg-time">' + escS(msg.time) + '</span></div>';
                    } else {
                        html += '<div class="msg in"><div class="bubble"><span class="who">Admin</span>' + escS(msg.message) + '</div><span class="msg-time">' + escS(msg.time) + '</span></div>';
                    }
                });
                html += '</div>';
                c.innerHTML = html;
                c.scrollTop = c.scrollHeight;
            } else {
                c.innerHTML = '<div class="chat-empty"><svg class="ic" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg><p>Belum ada pesan. Mulai ngobrol sama admin!</p></div>';
            }
        });
}

function sendMessagePage() {
    var input = document.getElementById('chatMessageInputPage');
    var message = input.value.trim();
    if (!message) return;
    fetch('/Barz/api/send_message.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: message })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            input.value = '';
            loadMessages();
        }
    });
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../customer/layout.php';
?>