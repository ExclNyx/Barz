// Barz Barbershop - Main JavaScript (minimal)
(function() {
    'use strict';

    // ---- Auto-dismiss Alerts ----
    setTimeout(function() {
        var alerts = document.querySelectorAll('.notice:not(.notice-info)');
        alerts.forEach(function(alert) {
            if (!alert.querySelector('.btn-close')) {
                alert.classList.add('transition-opacity', 'duration-500', 'opacity-0');
                setTimeout(function() { if (alert.parentNode) alert.remove(); }, 500);
            }
        });
    }, 3000);
})();