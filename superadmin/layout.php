<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Super Admin - Barz Barbershop'; ?></title>
    <link rel="stylesheet" href="/Barz/admin/assets/css/admin.css">
</head>
<body>
    <button class="burger" id="hamburgerBtn" aria-label="Buka sidebar">
        <svg class="ic" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
    <div class="veil" id="sidebarOverlay"></div>
    <div class="shell">
        <aside class="side" id="sidebar">
            <div class="side-head">
                <h2>
                    <svg class="ic" viewBox="0 0 24 24"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                    BARZ SUPER
                </h2>
            </div>
            <nav aria-label="Navigasi super admin">
                <a href="/Barz/superadmin/index.php" data-nav="index">
                    <svg class="ic" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Dashboard Eksekutif
                </a>
                <a href="/Barz/superadmin/admins.php" data-nav="admins">
                    <svg class="ic" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Kelola Admin
                </a>
                <a href="/Barz/superadmin/buying.php" data-nav="buying">
                    <svg class="ic" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    Laporan Buying
                </a>
            </nav>
            <div class="side-foot">
                <a href="/Barz/admin/index.php">
                    <svg class="ic" viewBox="0 0 24 24"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/></svg>
                    Panel Admin
                </a>
                <a href="/Barz/index.php?page=home">
                    <svg class="ic" viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                    Ke Website
                </a>
                <a href="/Barz/auth/logout.php">
                    <svg class="ic" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Logout
                </a>
            </div>
        </aside>
        <div class="main" id="mainContent">
            <?php echo $content ?? ''; ?>
        </div>
    </div>
    <script src="/Barz/admin/assets/js/admin.js"></script>
</body>
</html>
