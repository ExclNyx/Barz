/* Interaksi UI admin Barz: sidebar mobile, menu aktif, dan pencarian tabel. */
(function () {
    var hamburgerBtn = document.getElementById('hamburgerBtn');
    var sidebar = document.getElementById('sidebar');
    var sidebarOverlay = document.getElementById('sidebarOverlay');

    function closeSidebar() {
        if (!sidebar) return;
        sidebar.classList.remove('show');
        if (sidebarOverlay) sidebarOverlay.classList.remove('show');
        if (hamburgerBtn) hamburgerBtn.setAttribute('aria-expanded', 'false');
    }

    function toggleSidebar() {
        if (!sidebar) return;
        var isOpen = sidebar.classList.toggle('show');
        if (sidebarOverlay) sidebarOverlay.classList.toggle('show', isOpen);
        if (hamburgerBtn) hamburgerBtn.setAttribute('aria-expanded', String(isOpen));
    }

    if (hamburgerBtn) hamburgerBtn.addEventListener('click', toggleSidebar);
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);
    if (sidebar) {
        sidebar.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.matchMedia('(max-width: 767px)').matches) closeSidebar();
            });
        });
    }
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeSidebar();
    });

    // Tandai navigasi yang aktif berdasarkan nama file.
    var match = window.location.pathname.match(/\/([^\/]+)\.php$/);
    var currentPage = match ? match[1] : 'index';
    document.querySelectorAll('[data-nav]').forEach(function (link) {
        link.classList.toggle('on', link.getAttribute('data-nav') === currentPage);
    });

    // Pencarian lokal di tabel layanan.
    var serviceSearch = document.querySelector('[data-service-search]');
    if (serviceSearch) {
        var serviceRows = Array.prototype.slice.call(document.querySelectorAll('[data-service-row]'));
        var serviceEmpty = document.getElementById('serviceSearchEmpty');
        serviceSearch.addEventListener('input', function () {
            var query = serviceSearch.value.trim().toLowerCase();
            var visibleCount = 0;
            serviceRows.forEach(function (row) {
                var matches = (row.getAttribute('data-search') || '').indexOf(query) !== -1;
                row.classList.toggle('hidden', !matches);
                if (matches) visibleCount += 1;
            });
            if (serviceEmpty) serviceEmpty.classList.toggle('hidden', visibleCount > 0 || serviceRows.length === 0);
        });
    }

    // Pencarian lokal di tabel booking.
    var bookingSearch = document.querySelector('[data-booking-search]');
    if (bookingSearch) {
        var bookingRows = Array.prototype.slice.call(document.querySelectorAll('[data-booking-row]'));
        var bookingEmpty = document.querySelector('[data-booking-search-empty]');
        bookingSearch.addEventListener('input', function () {
            var query = bookingSearch.value.trim().toLowerCase();
            var visibleCount = 0;
            bookingRows.forEach(function (row) {
                var matches = (row.getAttribute('data-search') || '').indexOf(query) !== -1;
                row.classList.toggle('hidden', !matches);
                if (matches) visibleCount += 1;
            });
            if (bookingEmpty) bookingEmpty.classList.toggle('hidden', visibleCount > 0 || bookingRows.length === 0);
        });
    }
})();
