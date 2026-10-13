<?php
requireLogin();
$pageTitle = 'Chat - Barz Barbershop';

ob_start();
?>

<section class="section wrap max-w-280 my-0 mx-auto [padding-left:24px] [padding-right:24px] py-14 px-0 max-[600px]:py-10 max-[600px]:px-0">
    <div class="page-head [margin-bottom:32px]">
        <h1 class="font-sans font-bold leading-tight tracking-tight text-2xl [margin-bottom:6px]">Live Chat</h1>
        <p><span class="online-dot inline-flex items-center gap-1.5 bg-[rgba(111,168,111,.14)] text-[#2d6a2d] text-[.75rem] font-bold py-1 px-2.5 rounded"><i class="w-2 h-2 rounded-full bg-barz-success [font-style:normal]"></i> Online — admin membalas dalam 5-15 menit</span></p>
    </div>

    <div class="chat-shell bg-white border border-barz-line rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.04)] flex flex-col overflow-hidden">
        <div class="chat-head bg-[rgba(249,247,242,.7)] border-b border-barz-line py-3.5 px-4.5 flex items-center gap-3">
            <div class="avatar w-10 h-10 rounded-full bg-barz-ink text-white flex items-center justify-center flex-none" aria-hidden="true">
                <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div>
                <strong class="block text-[.9rem]">Admin Barz</strong>
                <small class="text-barz-muted text-[.78rem]">Membalas dalam 5-15 menit</small>
            </div>
        </div>

        <div id="chatMessagesPage" class="chat-log p-5 overflow-y-auto flex-1 bg-[rgba(249,247,242,.5)] min-h-75 max-h-[52vh]">
            <div class="chat-empty text-center text-barz-muted py-10 px-0">Memuat pesan...</div>
        </div>

        <form id="chatFormPage" class="chat-form p-3.5 border-t border-barz-line bg-white flex gap-2">
            <input type="text" id="chatMessageInputPage" class="input w-full py-3 px-3.5 border border-barz-line rounded bg-white text-barz-ink text-[.95rem] font-sans transition-colors focus:[outline:none] focus:border-barz-accent text-barz-muted rounded-xl" placeholder="Tulis pesan..." required autocomplete="off">
            <button type="submit" class="sendbtn font-sans w-11.5 flex-none border rounded-xl bg-barz-accent text-white cursor-pointer flex items-center justify-center hover:bg-barz-accent-hover" aria-label="Kirim pesan">
                <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-4.5 h-4.5" viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
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
                var html = '<div class="msgcol flex flex-col gap-3.5">';
                data.messages.forEach(function(msg) {
                    if (msg.is_sent) {
                        html += '<div class="msg sent max-w-[80%] flex flex-col [&.sent]:[align-self:flex-end] [&.sent]:items-end [&.in]:[align-self:flex-start] [&.in]:items-start"><div class="bubble py-2.5 px-4 rounded-2xl text-[.9rem] shadow-[0_2px_8px_rgba(0,0,0,0.04)] [.msg.sent_&]:bg-barz-accent [.msg.sent_&]:text-white [.msg.sent_&]:[border-top-right-radius:4px] [.msg.in_&]:bg-white [.msg.in_&]:border [.msg.in_&]:border-barz-line [.msg.in_&]:[border-top-left-radius:4px]">' + escS(msg.message) + '</div><span class="msg-time text-[.68rem] text-barz-muted [margin-top:4px]">' + escS(msg.time) + '</span></div>';
                    } else {
                        html += '<div class="msg in max-w-[80%] flex flex-col [&.sent]:[align-self:flex-end] [&.sent]:items-end [&.in]:[align-self:flex-start] [&.in]:items-start"><div class="bubble py-2.5 px-4 rounded-2xl text-[.9rem] shadow-[0_2px_8px_rgba(0,0,0,0.04)] [.msg.sent_&]:bg-barz-accent [.msg.sent_&]:text-white [.msg.sent_&]:[border-top-right-radius:4px] [.msg.in_&]:bg-white [.msg.in_&]:border [.msg.in_&]:border-barz-line [.msg.in_&]:[border-top-left-radius:4px]"><span class="who">Admin</span>' + escS(msg.message) + '</div><span class="msg-time text-[.68rem] text-barz-muted [margin-top:4px]">' + escS(msg.time) + '</span></div>';
                    }
                });
                html += '</div>';
                c.innerHTML = html;
                c.scrollTop = c.scrollHeight;
            } else {
                c.innerHTML = '<div class="chat-empty text-center text-barz-muted py-10 px-0"><svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg><p>Belum ada pesan. Mulai ngobrol sama admin!</p></div>';
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