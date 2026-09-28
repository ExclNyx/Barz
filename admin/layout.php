<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Admin - Barz Barbershop'; ?></title>
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
                    <svg class="ic" viewBox="0 0 24 24"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/></svg>
                    BARZ ADMIN
                </h2>
            </div>
            <nav aria-label="Navigasi admin">
                <a href="/Barz/admin/index.php" data-nav="index">
                    <svg class="ic" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Dashboard
                </a>
                <a href="/Barz/admin/bookings.php" data-nav="bookings">
                    <svg class="ic" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Bookings
                </a>
                <a href="/Barz/admin/services.php" data-nav="services">
                    <svg class="ic" viewBox="0 0 24 24"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/></svg>
                    Layanan
                </a>
                <a href="/Barz/admin/barbers.php" data-nav="barbers">
                    <svg class="ic" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Barbers
                </a>
                <a href="/Barz/admin/chats.php" data-nav="chats">
                    <svg class="ic" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    Chats
                </a>
                <a href="/Barz/admin/customers.php" data-nav="customers">
                    <svg class="ic" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Customers
                </a>
            </nav>
            <div class="side-foot">
                <?php if (isSuperAdmin()): ?>
                <a href="/Barz/superadmin/index.php">
                    <svg class="ic" viewBox="0 0 24 24"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                    Panel Super Admin
                </a>
                <?php endif; ?>
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
