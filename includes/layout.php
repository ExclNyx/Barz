<!DOCTYPE html>
<html lang="id" class="text-[16px]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="barz-api-base-url" content="http://127.0.0.1:8000/api">
    <title><?php echo $pageTitle ?? 'Barz Barbershop'; ?></title>
    <link rel="stylesheet" href="/Barz/assets/css/tailwind.css">
</head>
<body class="font-sans bg-barz-bg text-barz-ink leading-relaxed antialiased">
    <header class="topbar sticky top-0 z-50 bg-[rgba(249,247,242,.92)] [-webkit-backdrop-filter:blur(12px)] [backdrop-filter:blur(12px)] border-b border-barz-line">
        <div class="wrap topbar-in max-w-280 my-0 mx-auto [padding-left:24px] [padding-right:24px] flex items-center justify-between gap-3 [padding-top:14px] [padding-bottom:14px]">
            <a href="/Barz/index.php?page=home" class="brand text-barz-accent inline-flex items-center gap-2 no-underline text-barz-ink font-extrabold text-xl tracking-[.12em]">
                <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] text-barz-accent w-5.5 h-5.5" viewBox="0 0 24 24"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/></svg>
                BARZ
            </a>
            <nav class="site-links flex items-center gap-1 max-[960px]:hidden" aria-label="Navigasi utama">
                <a href="/Barz/index.php?page=home" class="<?php echo ($page ?? '') == 'home' ? 'on' : ''; ?> text-barz-accent relative no-underline text-barz-ink text-[.92rem] font-medium pt-2 px-3.5 pb-2.5 rounded-2xl hover:text-barz-accent hover:bg-[rgba(136,144,99,.08)] [&.on]:text-barz-accent [&.on]:font-bold [&.on]:after:content-[''] [&.on]:after:absolute [&.on]:after:left-3.5 [&.on]:after:right-3.5 [&.on]:after:bottom-0.5 [&.on]:after:h-0.5 [&.on]:after:bg-barz-accent [&.on]:after:rounded-sm">Home</a>
                <a href="/Barz/index.php?page=services" class="<?php echo ($page ?? '') == 'services' ? 'on' : ''; ?> text-barz-accent relative no-underline text-barz-ink text-[.92rem] font-medium pt-2 px-3.5 pb-2.5 rounded-2xl hover:text-barz-accent hover:bg-[rgba(136,144,99,.08)] [&.on]:text-barz-accent [&.on]:font-bold [&.on]:after:content-[''] [&.on]:after:absolute [&.on]:after:left-3.5 [&.on]:after:right-3.5 [&.on]:after:bottom-0.5 [&.on]:after:h-0.5 [&.on]:after:bg-barz-accent [&.on]:after:rounded-sm">Layanan</a>
                <?php if (isLoggedIn()): ?>
                    <a href="/Barz/index.php?page=booking" class="<?php echo ($page ?? '') == 'booking' ? 'on' : ''; ?> text-barz-accent relative no-underline text-barz-ink text-[.92rem] font-medium pt-2 px-3.5 pb-2.5 rounded-2xl hover:text-barz-accent hover:bg-[rgba(136,144,99,.08)] [&.on]:text-barz-accent [&.on]:font-bold [&.on]:after:content-[''] [&.on]:after:absolute [&.on]:after:left-3.5 [&.on]:after:right-3.5 [&.on]:after:bottom-0.5 [&.on]:after:h-0.5 [&.on]:after:bg-barz-accent [&.on]:after:rounded-sm">Booking</a>
                    <a href="/Barz/index.php?page=my-bookings" class="<?php echo ($page ?? '') == 'my-bookings' ? 'on' : ''; ?> text-barz-accent relative no-underline text-barz-ink text-[.92rem] font-medium pt-2 px-3.5 pb-2.5 rounded-2xl hover:text-barz-accent hover:bg-[rgba(136,144,99,.08)] [&.on]:text-barz-accent [&.on]:font-bold [&.on]:after:content-[''] [&.on]:after:absolute [&.on]:after:left-3.5 [&.on]:after:right-3.5 [&.on]:after:bottom-0.5 [&.on]:after:h-0.5 [&.on]:after:bg-barz-accent [&.on]:after:rounded-sm">Booking Saya</a>
                    <a href="/Barz/index.php?page=chat" class="<?php echo ($page ?? '') == 'chat' ? 'on' : ''; ?> text-barz-accent relative no-underline text-barz-ink text-[.92rem] font-medium pt-2 px-3.5 pb-2.5 rounded-2xl hover:text-barz-accent hover:bg-[rgba(136,144,99,.08)] [&.on]:text-barz-accent [&.on]:font-bold [&.on]:after:content-[''] [&.on]:after:absolute [&.on]:after:left-3.5 [&.on]:after:right-3.5 [&.on]:after:bottom-0.5 [&.on]:after:h-0.5 [&.on]:after:bg-barz-accent [&.on]:after:rounded-sm">Chat</a>
                <?php else: ?>
                    <a href="/Barz/index.php?page=login" class="<?php echo ($page ?? '') == 'login' ? 'on' : ''; ?> text-barz-accent relative no-underline text-barz-ink text-[.92rem] font-medium pt-2 px-3.5 pb-2.5 rounded-2xl hover:text-barz-accent hover:bg-[rgba(136,144,99,.08)] [&.on]:text-barz-accent [&.on]:font-bold [&.on]:after:content-[''] [&.on]:after:absolute [&.on]:after:left-3.5 [&.on]:after:right-3.5 [&.on]:after:bottom-0.5 [&.on]:after:h-0.5 [&.on]:after:bg-barz-accent [&.on]:after:rounded-sm">Login</a>
                    <a href="/Barz/index.php?page=register" class="<?php echo ($page ?? '') == 'register' ? 'on' : ''; ?> text-barz-accent relative no-underline text-barz-ink text-[.92rem] font-medium pt-2 px-3.5 pb-2.5 rounded-2xl hover:text-barz-accent hover:bg-[rgba(136,144,99,.08)] [&.on]:text-barz-accent [&.on]:font-bold [&.on]:after:content-[''] [&.on]:after:absolute [&.on]:after:left-3.5 [&.on]:after:right-3.5 [&.on]:after:bottom-0.5 [&.on]:after:h-0.5 [&.on]:after:bg-barz-accent [&.on]:after:rounded-sm">Register</a>
                <?php endif; ?>
            </nav>
            <div class="site-actions flex items-center gap-2">
                <?php if (isLoggedIn()): ?>
                    <div class="user-pill flex items-center gap-2 bg-white border border-barz-line pt-1.5 pr-3 pb-1.5 pl-1.5 rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.04)]">
                        <span class="user-ava w-7 h-7 rounded-full bg-barz-accent text-white flex items-center justify-center font-bold text-[.8rem] flex-none"><?php echo strtoupper(substr($_SESSION['name'] ?? 'B', 0, 1)); ?></span>
                        <span class="nm text-[.85rem] font-semibold"><?php echo htmlspecialchars($_SESSION['name'] ?? ''); ?></span>
                        <a href="/Barz/auth/logout.php" class="lo text-barz-accent text-[.75rem] text-barz-danger no-underline hover:underline">Logout</a>
                    </div>
                <?php else: ?>
                    <a href="/Barz/index.php?page=login" class="tlink text-barz-accent font-semibold no-underline hover:underline py-2 px-3" >Login</a>
                    <a href="/Barz/index.php?page=register" class="button button-primary button-small text-barz-accent inline-flex items-center justify-center gap-2 py-3.5 px-7 rounded-2xl font-semibold text-[.95rem] no-underline cursor-pointer border border-transparent transition-colors bg-barz-accent text-white hover:bg-barz-accent-hover py-[9px] px-4 text-[.85rem] disabled:opacity-50 disabled:cursor-not-allowed">Register</a>
                <?php endif; ?>
                <button class="iconbtn menu-toggle font-sans items-center justify-center w-10 h-10 border border-transparent bg-transparent rounded-xl text-barz-ink cursor-pointer no-underline hover:text-barz-accent hover:border-barz-line hover:bg-barz-surface hidden max-[960px]:inline-flex" id="menuToggle" aria-label="Buka menu">
                    <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
            </div>
        </div>
        <nav class="mnav hidden border-t border-barz-line pt-2 px-6 pb-4 [&.open]:block" id="mnav" aria-label="Navigasi seluler">
            <a href="/Barz/index.php?page=home" class="text-barz-accent block no-underline text-barz-ink font-medium py-2.5 px-1 border-b border-barz-line last:border-b">Home</a>
            <a href="/Barz/index.php?page=services" class="text-barz-accent block no-underline text-barz-ink font-medium py-2.5 px-1 border-b border-barz-line last:border-b">Layanan</a>
            <?php if (isLoggedIn()): ?>
                <a href="/Barz/index.php?page=booking" class="text-barz-accent block no-underline text-barz-ink font-medium py-2.5 px-1 border-b border-barz-line last:border-b">Booking</a>
                <a href="/Barz/index.php?page=my-bookings" class="text-barz-accent block no-underline text-barz-ink font-medium py-2.5 px-1 border-b border-barz-line last:border-b">Booking Saya</a>
                <a href="/Barz/index.php?page=chat" class="text-barz-accent block no-underline text-barz-ink font-medium py-2.5 px-1 border-b border-barz-line last:border-b">Chat</a>
                <a href="/Barz/auth/logout.php" class="text-barz-accent block no-underline text-barz-ink font-medium py-2.5 px-1 border-b border-barz-line last:border-b">Logout</a>
            <?php else: ?>
                <a href="/Barz/index.php?page=login" class="text-barz-accent block no-underline text-barz-ink font-medium py-2.5 px-1 border-b border-barz-line last:border-b">Login</a>
                <a href="/Barz/index.php?page=register" class="text-barz-accent block no-underline text-barz-ink font-medium py-2.5 px-1 border-b border-barz-line last:border-b">Register</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <?php echo $content ?? ''; ?>
    </main>

    <footer class="foot bg-barz-ink text-barz-footer border-t-0 pt-12 px-0 pb-6 [margin-top:40px]">
        <div class="wrap max-w-280 my-0 mx-auto [padding-left:24px] [padding-right:24px]">
            <div class="foot-grid grid grid-cols-[1.5fr_1fr_1fr_1fr] gap-8 [margin-bottom:32px] max-[960px]:grid-cols-2 max-[600px]:grid-cols-[1fr]">
                <div>
                    <div class="foot-brand flex items-center gap-2 [margin-bottom:12px]">
                        <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] text-barz-accent" viewBox="0 0 24 24"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/></svg>
                        <strong class="text-white text-lg tracking-[.04em]">BARZ</strong>
                    </div>
                    <p class="text-[.8rem] text-barz-line leading-[1.7]">Book, datang, kelihatan keren. Barbershop modern dengan booking online tanpa antri.</p>
                    <p class="text-[.8rem] text-barz-line leading-[1.7]">&copy; 2026 Barz Barbershop.</p>
                </div>
                <div>
                    <h4 class="font-sans font-bold leading-tight tracking-tight text-white text-[.85rem] uppercase tracking-[.06em] [margin-bottom:16px] [padding-bottom:8px] border-b border-[rgba(136,144,99,.3)]">Alamat</h4>
                    <p class="text-[.8rem] text-barz-line leading-[1.7]">Jl. Boulevard Barbershop No. 88,<br>Kawasan Niaga Utama Malang Center,<br>Jawa Timur, Indonesia.</p>
                </div>
                <div>
                    <h4 class="font-sans font-bold leading-tight tracking-tight text-white text-[.85rem] uppercase tracking-[.06em] [margin-bottom:16px] [padding-bottom:8px] border-b border-[rgba(136,144,99,.3)]">Kontak</h4>
                    <ul class="list-none">
                        <li class="text-[.8rem] text-barz-line leading-[1.7]">Telp/WA: +62 812-3456-7890</li>
                        <li class="text-[.8rem] text-barz-line leading-[1.7]">Email: info@barzbarbershop.com</li>
                        <li class="text-[.8rem] text-barz-line leading-[1.7]">Buka: Setiap Hari (09:00 - 21:00 WIB)</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-sans font-bold leading-tight tracking-tight text-white text-[.85rem] uppercase tracking-[.06em] [margin-bottom:16px] [padding-bottom:8px] border-b border-[rgba(136,144,99,.3)]">Sosial Media</h4>
                    <div class="foot-social flex gap-2.5 [margin-top:12px]">
                        <a href="#" aria-label="Instagram" class="text-barz-accent text-barz-line no-underline hover:text-white w-9 h-9 rounded-lg bg-[rgba(255,255,255,.08)] flex items-center justify-center transition-colors hover:bg-barz-accent"><svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><line x1="17.5" y1="6.5" x2="17.5" y2="6.5"/></svg></a>
                        <a href="#" aria-label="TikTok" class="text-barz-accent text-barz-line no-underline hover:text-white w-9 h-9 rounded-lg bg-[rgba(255,255,255,.08)] flex items-center justify-center transition-colors hover:bg-barz-accent"><svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]" viewBox="0 0 24 24"><path d="M9 12a4 4 0 1 0 4 4V4c.6 2.5 2.4 4.3 5 5"/></svg></a>
                        <a href="#" aria-label="YouTube" class="text-barz-accent text-barz-line no-underline hover:text-white w-9 h-9 rounded-lg bg-[rgba(255,255,255,.08)] flex items-center justify-center transition-colors hover:bg-barz-accent"><svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]" viewBox="0 0 24 24"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-2C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33 2.78 2.78 0 0 0 1.95 2c1.71.46 8.59.46 8.59.46s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-2 29 29 0 0 0 .46-5.33 29 29 0 0 0-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/></svg></a>
                        <a href="#" aria-label="WhatsApp" class="text-barz-accent text-barz-line no-underline hover:text-white w-9 h-9 rounded-lg bg-[rgba(255,255,255,.08)] flex items-center justify-center transition-colors hover:bg-barz-accent"><svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></a>
                    </div>
                </div>
            </div>
            <div class="foot-copy border-t border-[rgba(136,144,99,.2)] [padding-top:16px] text-[.75rem] text-[#cfbb9990] text-center">Barz Barbershop — Book, datang, kelihatan keren.</div>
        </div>
    </footer>

    <?php if (isLoggedIn()): ?>
    <div class="chat-fab-zone fixed bottom-6 right-6 z-[100] max-[600px]:bottom-4 max-[600px]:right-4">
        <div class="chat-win absolute bottom-17 right-0 w-90 max-w-[calc(100vw_-_48px)] bg-white border border-barz-line rounded-2xl shadow-[0_8px_24px_rgba(0,0,0,0.14)] overflow-hidden flex flex-col max-[600px]:-right-2 hidden" id="chatWindow" >
            <div class="chat-win-head bg-barz-accent text-white py-3 px-4 flex justify-between items-center">
                <h3 class="font-sans font-bold leading-tight tracking-tight text-base">Barz Chat</h3>
                <button class="chat-x font-sans bg-transparent border text-white text-xl cursor-pointer leading-none" id="chatClose" aria-label="Tutup chat">&times;</button>
            </div>
            <div class="chat-win-log p-3.5 bg-barz-bg h-70 overflow-y-auto" id="chatMessages"></div>
            <form class="chat-win-form flex gap-2 p-2.5 border-t border-barz-line" id="chatForm">
                <input type="text" class="input w-full py-3 px-3.5 border border-barz-line rounded bg-white text-barz-ink text-[.95rem] font-sans transition-colors focus:[outline:none] focus:border-barz-accent text-barz-muted min-h-10 rounded-xl" id="chatMessageInput" placeholder="Tulis pesan..." autocomplete="off">
                <button type="submit" class="sendbtn font-sans w-11.5 flex-none border rounded-xl bg-barz-accent text-white cursor-pointer flex items-center justify-center hover:bg-barz-accent-hover" aria-label="Kirim">
                    <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-4.5 h-4.5" viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </form>
        </div>
        <button class="chat-fab font-sans w-14 h-14 border rounded-full bg-barz-accent text-white cursor-pointer flex items-center justify-center shadow-[0_4px_16px_rgba(0,0,0,.15)] hover:bg-barz-accent-hover" id="chatToggle" aria-label="Buka chat">
            <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-6 h-6" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        </button>
    </div>
    <?php endif; ?>

    <script src="/Barz/assets/js/main.js"></script>
    <script src="/Barz/assets/js/api.js"></script>
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
