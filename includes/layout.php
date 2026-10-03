<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Barz Barbershop'; ?></title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <?php include __DIR__ . '/../assets/tailwind.php'; ?>
</head>
<body>
    <header class="topbar">
        <div class="wrap topbar-in">
            <a href="/Barz/index.php?page=home" class="brand">
                <svg class="ic" viewBox="0 0 24 24"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/></svg>
                BARZ
            </a>
            <nav class="site-links" aria-label="Navigasi utama">
                <a href="/Barz/index.php?page=home" class="<?php echo ($page ?? '') == 'home' ? 'on' : ''; ?>">Home</a>
                <a href="/Barz/index.php?page=services" class="<?php echo ($page ?? '') == 'services' ? 'on' : ''; ?>">Layanan</a>
                <?php if (isLoggedIn()): ?>
                    <a href="/Barz/index.php?page=booking" class="<?php echo ($page ?? '') == 'booking' ? 'on' : ''; ?>">Booking</a>
                    <a href="/Barz/index.php?page=my-bookings" class="<?php echo ($page ?? '') == 'my-bookings' ? 'on' : ''; ?>">Booking Saya</a>
                    <a href="/Barz/index.php?page=chat" class="<?php echo ($page ?? '') == 'chat' ? 'on' : ''; ?>">Chat</a>
                <?php else: ?>
                    <a href="/Barz/index.php?page=login" class="<?php echo ($page ?? '') == 'login' ? 'on' : ''; ?>">Login</a>
                    <a href="/Barz/index.php?page=register" class="<?php echo ($page ?? '') == 'register' ? 'on' : ''; ?>">Register</a>
                <?php endif; ?>
            </nav>
            <div class="site-actions">
                <?php if (isLoggedIn()): ?>
                    <div class="user-pill">
                        <span class="user-ava"><?php echo strtoupper(substr($_SESSION['name'] ?? 'B', 0, 1)); ?></span>
                        <span class="nm"><?php echo htmlspecialchars($_SESSION['name'] ?? ''); ?></span>
                        <a href="/Barz/auth/logout.php" class="lo">Logout</a>
                    </div>
                <?php else: ?>
                    <a href="/Barz/index.php?page=login" class="tlink px-3 py-2">Login</a>
                    <a href="/Barz/index.php?page=register" class="button button-primary button-small">Register</a>
                <?php endif; ?>
                <button class="iconbtn menu-toggle" id="menuToggle" aria-label="Buka menu">
                    <svg class="ic" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
            </div>
        </div>
        <nav class="mnav" id="mnav" aria-label="Navigasi seluler">
            <a href="/Barz/index.php?page=home">Home</a>
            <a href="/Barz/index.php?page=services">Layanan</a>
            <?php if (isLoggedIn()): ?>
                <a href="/Barz/index.php?page=booking">Booking</a>
                <a href="/Barz/index.php?page=my-bookings">Booking Saya</a>
                <a href="/Barz/index.php?page=chat">Chat</a>
                <a href="/Barz/auth/logout.php">Logout</a>
            <?php else: ?>
                <a href="/Barz/index.php?page=login">Login</a>
                <a href="/Barz/index.php?page=register">Register</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <?php echo $content ?? ''; ?>
    </main>

    <footer class="foot">
        <div class="wrap">
            <div class="foot-grid">
                <div>
                    <div class="foot-brand">
                        <svg class="ic" viewBox="0 0 24 24"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/></svg>
                        <strong>BARZ</strong>
                    </div>
                    <p>Book, datang, kelihatan keren. Barbershop modern dengan booking online tanpa antri.</p>
                    <p>&copy; 2026 Barz Barbershop.</p>
                </div>
                <div>
                    <h4>Alamat</h4>
                    <p>Jl. Boulevard Barbershop No. 88,<br>Kawasan Niaga Utama Malang Center,<br>Jawa Timur, Indonesia.</p>
                </div>
                <div>
                    <h4>Kontak</h4>
                    <ul>
                        <li>Telp/WA: +62 812-3456-7890</li>
                        <li>Email: info@barzbarbershop.com</li>
                        <li>Buka: Setiap Hari (09:00 - 21:00 WIB)</li>
                    </ul>
                </div>
                <div>
                    <h4>Sosial Media</h4>
                    <div class="foot-social">
                        <a href="#" aria-label="Instagram"><svg class="ic" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><line x1="17.5" y1="6.5" x2="17.5" y2="6.5"/></svg></a>
                        <a href="#" aria-label="TikTok"><svg class="ic" viewBox="0 0 24 24"><path d="M9 12a4 4 0 1 0 4 4V4c.6 2.5 2.4 4.3 5 5"/></svg></a>
                        <a href="#" aria-label="YouTube"><svg class="ic" viewBox="0 0 24 24"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-2C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33 2.78 2.78 0 0 0 1.95 2c1.71.46 8.59.46 8.59.46s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-2 29 29 0 0 0 .46-5.33 29 29 0 0 0-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/></svg></a>
                        <a href="#" aria-label="WhatsApp"><svg class="ic" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></a>
                    </div>
                </div>
            </div>
            <div class="foot-copy">Barz Barbershop — Book, datang, kelihatan keren.</div>
        </div>
    </footer>

    <?php if (isLoggedIn()): ?>
    <div class="chat-fab-zone">
        <div class="chat-win hidden" id="chatWindow" >
            <div class="chat-win-head">
                <h3>Barz Chat</h3>
                <button class="chat-x" id="chatClose" aria-label="Tutup chat">&times;</button>
            </div>
            <div class="chat-win-log" id="chatMessages"></div>
            <form class="chat-win-form" id="chatForm">
                <input type="text" class="input" id="chatMessageInput" placeholder="Tulis pesan..." autocomplete="off">
                <button type="submit" class="sendbtn" aria-label="Kirim">
                    <svg class="ic" viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </form>
        </div>
        <button class="chat-fab" id="chatToggle" aria-label="Buka chat">
            <svg class="ic" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        </button>
    </div>
    <?php endif; ?>

    <script src="/Barz/assets/js/main.js"></script>
    <script>
    (function(){
        var t=document.getElementById('menuToggle'),m=document.getElementById('mnav');
        if(t&&m){t.addEventListener('click',function(){m.classList.toggle('open');});}
        var w=document.getElementById('chatWindow'),b=document.getElementById('chatToggle'),x=document.getElementById('chatClose');
        if(b&&w){b.addEventListener('click',function(){w.classList.toggle('hidden');});}
        if(x&&w){x.addEventListener('click',function(){w.classList.add('hidden');});}
    })();
    </script>
</body>
</html>
