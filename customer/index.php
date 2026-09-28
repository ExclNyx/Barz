<?php
// Customer router - hanya halaman customer (public + login required)
require_once __DIR__ . '/../includes/init.php';

$page = $_GET['page'] ?? 'home';
$baseDir = __DIR__;

switch ($page) {
    case 'home':
    case 'services':
    case 'chat':
        // Public pages
        include $baseDir . '/pages/' . $page . '.php';
        break;

    case 'booking':
    case 'my-bookings':
        // Require login
        requireLogin();
        include $baseDir . '/pages/' . $page . '.php';
        break;

    default:
        http_response_code(404);
        echo "Page not found";
        break;
}