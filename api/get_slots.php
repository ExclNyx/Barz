<?php
ob_start();
require_once __DIR__ . '/../includes/init.php';
ob_end_clean();

header('Content-Type: application/json');

$serviceId = (int)($_GET['service_id'] ?? 0);
$date = $_GET['date'] ?? '';
$barberId = !empty($_GET['barber_id']) ? (int)$_GET['barber_id'] : null;

// Validate inputs
if (!$serviceId || !$date) {
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

// Validate date format (YYYY-MM-DD)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    echo json_encode(['error' => 'Invalid date format']);
    exit;
}

// Validate date is not in the past
if ($date < date('Y-m-d')) {
    echo json_encode(['error' => 'Cannot book past dates', 'slots' => []]);
    exit;
}

$slots = getAvailableSlots($date, $serviceId, $barberId);

echo json_encode(['slots' => $slots]);