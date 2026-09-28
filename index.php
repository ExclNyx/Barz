<?php
// Router storefront. Admin/superadmin/API pakai includes/init.php langsung.
require_once __DIR__ . '/includes/init.php';

$page = $_GET['page'] ?? 'home';
$baseDir = __DIR__;

switch ($page) {
    case 'home':
    case 'services':
    case 'booking':
    case 'my-bookings':
    case 'chat':
        include $baseDir . '/pages/' . $page . '.php';
        break;

    case 'admin':
        include $baseDir . '/admin/index.php';
        break;

    case 'login':
    case 'register':
        include $baseDir . '/auth/' . $page . '.php';
        break;

    default:
        http_response_code(404);
        echo "Page not found";
        break;
}
