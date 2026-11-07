<?php
require_once __DIR__ . '/../includes/init.php';
requireAdmin();

require_once __DIR__ . '/../php-native/models/BarberModel.php';
require_once __DIR__ . '/../php-native/controllers/Admin/BarberController.php';

$pageTitle = 'Kelola Barber - Admin';
$barberModel = new BarberModel(getDB());
$barberController = new AdminBarberController($barberModel);
$viewData = $barberController->handle();

extract($viewData, EXTR_SKIP);
ob_start();
require __DIR__ . '/../php-native/views/Admin/barbers.php';
$content = ob_get_clean();

include __DIR__ . '/layout.php';
