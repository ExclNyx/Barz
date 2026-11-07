<?php
/** Generate the upcoming booking slots. Run from the project root using CLI. */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Jalankan script ini melalui terminal, bukan browser.');
}

require_once __DIR__ . '/includes/init.php';

$db = getDB();
$openingTime = substr((string) getSetting('opening_time', '09:00'), 0, 5);
$closingTime = substr((string) getSetting('closing_time', '21:00'), 0, 5);
$slotDuration = max(5, (int) getSetting('slot_duration', '15'));
$startDate = date('Y-m-d');
$endDate = date('Y-m-d', strtotime('+30 days'));
$barbers = getBarbers(true);

if (!$barbers) {
    fwrite(STDERR, "Tidak ada barber aktif. Aktifkan minimal satu barber terlebih dahulu." . PHP_EOL);
    exit(1);
}

$openingTimestamp = strtotime($openingTime);
$closingTimestamp = strtotime($closingTime);
if ($openingTimestamp === false || $closingTimestamp === false || $openingTimestamp >= $closingTimestamp) {
    fwrite(STDERR, "Pengaturan jam operasional tidak valid." . PHP_EOL);
    exit(1);
}

$totalSlots = 0;
$db->begin_transaction();

try {
    // Hapus slot masa depan yang belum terhubung dengan booking, lalu buat ulang.
    $delete = $db->prepare('DELETE FROM time_slots WHERE date >= ? AND booking_id IS NULL');
    $delete->bind_param('s', $startDate);
    $delete->execute();
    $delete->close();

    $insert = $db->prepare(
        'INSERT INTO time_slots (date, start_time, end_time, barber_id, is_available)
         VALUES (?, ?, ?, ?, 1)'
    );

    for ($date = $startDate; $date <= $endDate; $date = date('Y-m-d', strtotime($date . ' +1 day'))) {
        foreach ($barbers as $barber) {
            for ($time = $openingTimestamp; $time + ($slotDuration * 60) <= $closingTimestamp; $time += ($slotDuration * 60)) {
                $slotStart = date('H:i:s', $time);
                $slotEnd = date('H:i:s', $time + ($slotDuration * 60));
                $barberId = (int) $barber['id'];

                $insert->bind_param('sssi', $date, $slotStart, $slotEnd, $barberId);
                $insert->execute();
                $totalSlots++;
            }
        }
    }

    $insert->close();
    $db->commit();
} catch (Throwable $exception) {
    $db->rollback();
    fwrite(STDERR, 'Gagal membuat slot: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}

fwrite(STDOUT, json_encode([
    'success' => true,
    'totalSlots' => $totalSlots,
    'barbers' => count($barbers),
    'period' => $startDate . ' sampai ' . $endDate,
    'slotDurationMinutes' => $slotDuration,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL);
