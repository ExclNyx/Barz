<?php
// Root router: arahkan ke area yang benar
require_once __DIR__ . '/includes/init.php';

$area = $_GET['area'] ?? 'customer';
$page = $_GET['page'] ?? 'home';

switch ($area) {
    case 'customer':
        include __DIR__ . '/customer/index.php';
        break;

    case 'admin':
        if (!isAdmin() && !isSuperAdmin()) {
            header('Location: /Barz/index.php?area=customer&page=login');
            exit;
        }
        include __DIR__ . '/admin/index.php';
        break;

    case 'superadmin':
        if (!isSuperAdmin()) {
            header('Location: /Barz/index.php?area=customer&page=home');
            exit;
        }
        include __DIR__ . '/superadmin/index.php';
        break;

    case 'api':
        include __DIR__ . '/api/' . $page . '.php';
        break;

    case 'auth':
        include __DIR__ . '/auth/' . $page . '.php';
        break;

    case 'generate':
        include __DIR__ . '/generate_slots.php';
        break;

    default:
        http_response_code(404);
        echo "Area not found";
        break;
}