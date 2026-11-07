<?php

declare(strict_types=1);

use Barz\ManualMvc\Controllers\ServiceController;
use Barz\ManualMvc\Models\ServiceModel;

// Hubungkan aplikasi demo MVC dengan session dan database MySQLi Native Barz.
$projectRoot = dirname(__DIR__, 3);
require_once $projectRoot . '/includes/init.php';

if (!isLoggedIn()) {
    requireLogin();
}

if (!isAdmin()) {
    http_response_code(403);
    exit('Akses ditolak. Halaman ini hanya untuk admin.');
}

// Autoload manual agar alur Model -> Controller -> View terlihat jelas saat demo.
require_once dirname(__DIR__) . '/models/ServiceModel.php';
require_once dirname(__DIR__) . '/controllers/ServiceController.php';

$model = new ServiceModel(getDB());
$controller = new ServiceController($model);
$controller->handle();
