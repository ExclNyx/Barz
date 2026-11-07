<?php
require_once __DIR__ . '/../includes/init.php';
requireAdmin();

require_once __DIR__ . '/../php-native/models/ServiceModel.php';
require_once __DIR__ . '/../php-native/controllers/Admin/ServiceController.php';

$pageTitle = 'Kelola Layanan - Admin';
$serviceModel = new ServiceModel(getDB());
$serviceController = new AdminServiceController($serviceModel);
$viewData = $serviceController->handle();

extract($viewData, EXTR_SKIP);
ob_start();
require __DIR__ . '/../php-native/views/Admin/services.php';
$content = ob_get_clean();

include __DIR__ . '/layout.php';
