<?php
ob_start();
require_once __DIR__ . '/includes/init.php';
ob_end_clean();

$db = getDB();

// Fixed settings - barbershop style
$openingTime = '09:00';
$closingTime = '21:00';

// Slot durations per service type (in minutes)
// Gentleman's Cut: 45 min, Perm: 120 min, Coloring: 120 min, Bleach: 150 min, etc.
// Use 15-min grid for maximum flexibility
$slotDuration = 15;

// Clear existing slots
$db->query("DELETE FROM time_slots");

$barbers = getBarbers(true);
$startDate = date('Y-m-d');
$endDate = date('Y-m-d', strtotime('+30 days'));

$totalSlots = 0;

// Loop through dates
$currentDate = $startDate;
while ($currentDate <= $endDate) {
    if ($currentDate < date('Y-m-d')) {
        $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        continue;
    }
    
    // Generate slots for each barber
    foreach ($barbers as $barber) {
        $current = strtotime($openingTime);
        $end = strtotime($closingTime);
        
        while ($current < $end) {
            $slotStart = date('H:i:s', $current);
            $slotEnd = date('H:i:s', $current + ($slotDuration * 60));
            
            $stmt = $db->prepare("INSERT INTO time_slots (date, start_time, end_time, barber_id, is_available) VALUES (?, ?, ?, ?, 1)");
            $stmt->bind_param("sssi", $currentDate, $slotStart, $slotEnd, $barber['id']);
            $stmt->execute();
            
            $totalSlots++;
            $current += ($slotDuration * 60);
        }
    }
    
    $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
}

echo json_encode(['success' => true, 'totalSlots' => $totalSlots, 'barbers' => count($barbers), 'period' => $startDate . ' to ' . $endDate]);
?>
