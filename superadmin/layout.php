<!DOCTYPE html>
<html lang="id" class="text-[16px]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="barz-api-base-url" content="http://127.0.0.1:8000/api">
    <title><?php echo $pageTitle ?? 'Super Admin - Barz Barbershop'; ?></title>
    <link rel="stylesheet" href="/Barz/assets/css/tailwind.css">
</head>
<body class="font-sans bg-barz-bg text-barz-ink leading-[1.55] antialiased overflow-x-hidden">
    <button class="burger fixed block md:hidden top-3.5 left-3.5 z-50 bg-barz-deep text-[#f5f0e8] border border-[rgba(136,144,99,.2)] rounded-lg p-2.5 cursor-pointer shadow-[0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)]" id="hamburgerBtn" aria-label="Buka sidebar">
        <svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-5 h-5" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
    <div class="veil hidden fixed inset-0 bg-[rgba(45,58,31,.6)] [backdrop-filter:blur(2px)] z-[39] [&.show]:block" id="sidebarOverlay"></div>
    <div class="shell flex min-h-screen bg-barz-bg">
        <aside class="side fixed top-0 left-0 w-65 h-screen bg-barz-deep border-r border-[rgba(136,144,99,.15)] z-40 flex flex-col transition max-[768px]:[transform:translateX(-100%)] max-[768px]:[&.show]:[transform:translateX(0)]" id="sidebar">
            <div class="side-head py-5.5 px-5 border-b border-[rgba(136,144,99,.15)]">
                <div class="side-brand flex items-center gap-3">
                    <div class="side-brand-icon super w-9.5 h-9.5 rounded-[10px] bg-[rgba(136,144,99,0.12)] text-barz-accent flex items-center justify-center flex-none bg-[rgba(201,168,118,.15)] text-barz-gold">
                        <svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-5 h-5" viewBox="0 0 24 24"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                    </div>
                    <div>
                        <h2 class="font-sans text-white text-base font-extrabold tracking-wider leading-[1.2]">BARZ SUPER</h2>
                        <span class="side-badge super inline-block text-[0.65rem] font-bold uppercase tracking-[0.06em] py-0.5 px-[7px] rounded bg-[rgba(136,144,99,0.12)] text-barz-accent [margin-top:2px] bg-[rgba(201,168,118,.2)] text-barz-gold">Executive Suite</span>
                    </div>
                </div>
            </div>
            <nav aria-label="Navigasi super admin" class="py-4 px-3 flex-1 overflow-y-auto flex flex-col gap-1">
                <a href="/Barz/superadmin/index.php" data-nav="index" class="no-underline flex items-center gap-3 py-2.5 px-3.5 rounded-lg text-barz-muted-light text-[0.88rem] font-medium transition-all hover:bg-[rgba(136,144,99,.1)] hover:text-[#f5f0e8] hover:translate-x-0.5 [&.on]:bg-barz-accent [&.on]:text-white [&.on]:font-semibold [&.on]:shadow-[0_2px_8px_rgba(136,_144,_99,_.3)]">
                    <svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Dashboard Eksekutif
                </a>
                <a href="/Barz/superadmin/admins.php" data-nav="admins" class="no-underline flex items-center gap-3 py-2.5 px-3.5 rounded-lg text-barz-muted-light text-[0.88rem] font-medium transition-all hover:bg-[rgba(136,144,99,.1)] hover:text-[#f5f0e8] hover:translate-x-0.5 [&.on]:bg-barz-accent [&.on]:text-white [&.on]:font-semibold [&.on]:shadow-[0_2px_8px_rgba(136,_144,_99,_.3)]">
                    <svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Kelola Admin
                </a>
                <a href="/Barz/superadmin/buying.php" data-nav="buying" class="no-underline flex items-center gap-3 py-2.5 px-3.5 rounded-lg text-barz-muted-light text-[0.88rem] font-medium transition-all hover:bg-[rgba(136,144,99,.1)] hover:text-[#f5f0e8] hover:translate-x-0.5 [&.on]:bg-barz-accent [&.on]:text-white [&.on]:font-semibold [&.on]:shadow-[0_2px_8px_rgba(136,_144,_99,_.3)]">
                    <svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    Laporan Buying
                </a>
            </nav>
            <div class="side-foot py-4 px-3.5 border-t border-[rgba(136,144,99,.15)] bg-[rgba(0,0,0,0.15)]">
                <div class="user-pill-side flex items-center gap-2.5 py-1.5 px-2 [margin-bottom:12px] bg-[rgba(255,255,255,0.04)] rounded-lg border border-[rgba(255,255,255,0.06)]">
                    <div class="user-ava-side super w-8 h-8 rounded-lg bg-barz-accent text-white font-bold text-[0.82rem] flex items-center justify-center flex-none bg-barz-gold"><?php echo strtoupper(substr($_SESSION['name'] ?? 'S', 0, 1)); ?></div>
                    <div class="user-meta-side flex flex-col overflow-hidden">
                        <span class="user-name-side text-[0.82rem] font-semibold text-[#f5f0e8] whitespace-nowrap overflow-hidden text-ellipsis"><?php echo htmlspecialchars($_SESSION['name'] ?? 'Super Admin'); ?></span>
                        <span class="user-role-side text-[0.7rem] text-barz-muted-light">Super Administrator</span>
                    </div>
                </div>
                <div class="side-foot-links flex flex-col gap-0.5">
                    <a href="/Barz/admin/index.php" title="Panel Admin Regular" class="no-underline flex items-center gap-2.5 py-2 px-2.5 rounded-md text-barz-muted-light text-[0.82rem] font-medium transition-all hover:bg-[rgba(136,144,99,.1)] hover:text-[#f5f0e8]">
                        <svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                        <span>Panel Admin</span>
                    </a>
                    <a href="/Barz/index.php?page=home" title="Ke Website" class="no-underline flex items-center gap-2.5 py-2 px-2.5 rounded-md text-barz-muted-light text-[0.82rem] font-medium transition-all hover:bg-[rgba(136,144,99,.1)] hover:text-[#f5f0e8]">
                        <svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]" viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                        <span>Ke Website</span>
                    </a>
                    <a href="/Barz/auth/logout.php" title="Logout" class="side-link-danger text-barz-accent no-underline flex items-center gap-2.5 py-2 px-2.5 rounded-md text-barz-muted-light text-[0.82rem] font-medium transition-all hover:bg-[rgba(136,144,99,.1)] hover:text-[#f5f0e8] hover:bg-[rgba(217,127,110,.15)] hover:text-barz-danger">
                        <svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </aside>
        <div class="main [margin-left:260px] flex-1 min-w-0 min-h-screen max-[768px]:[margin-left:0]" id="mainContent">
            <?php echo $content ?? ''; ?>
        </div>
    </div>
    <script src="/Barz/assets/js/api.js"></script>
    <script src="/Barz/admin/assets/js/admin.js"></script>
</body>
</html>
