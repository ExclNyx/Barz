/* Admin Panel JavaScript - sidebar toggle + penanda nav aktif */
(function() {
    var hamburgerBtn = document.getElementById('hamburgerBtn');
    var sidebar = document.getElementById('sidebar');
    var sidebarOverlay = document.getElementById('sidebarOverlay');

    function toggleSidebar() {
        if (!sidebar) return;
        sidebar.classList.toggle('show');
        if (sidebarOverlay) sidebarOverlay.classList.toggle('show');
    }

    if (hamburgerBtn) hamburgerBtn.addEventListener('click', toggleSidebar);
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', toggleSidebar);

    // Tandai menu aktif dari nama file (index/bookings/services/barbers/chats/customers/admins/buying)
    var m = window.location.pathname.match(/\/([^\/]+)\.php$/);
    var cur = m ? m[1] : 'index';
    document.querySelectorAll('[data-nav]').forEach(function(link) {
        link.classList.toggle('on', link.getAttribute('data-nav') === cur);
    });
})();
