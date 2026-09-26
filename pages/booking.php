<?php
requireLogin();
$pageTitle = 'Booking - Barz Barbershop';

$services = getServices(true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serviceId = (int)$_POST['service_id'];
    $bookingDate = $_POST['booking_date'];
    $startTime = $_POST['start_time'];
    $notes = sanitize($_POST['notes'] ?? '');

    $db = getDB();
    $stmt = $db->prepare("SELECT price, duration FROM services WHERE id = ?");
    $stmt->bind_param("i", $serviceId);
    $stmt->execute();
    $service = $stmt->get_result()->fetch_assoc();

    $endTime = date('H:i', strtotime($startTime) + ($service['duration'] * 60));

    $available = false;
    $barbers = getBarbers(true);
    foreach ($barbers as $barber) {
        if (isSlotAvailable($bookingDate, $startTime, $endTime, $barber['id'])) {
            $available = true;
            break;
        }
    }

    if ($available) {
        $bookingId = createBooking($_SESSION['user_id'], $serviceId, null, $bookingDate, $startTime, $endTime, $service['price'], $notes);

        if ($bookingId) {
            $_SESSION['success'] = 'Booking berhasil dibuat! Menunggu konfirmasi admin.';
            redirect('my-bookings');
        } else {
            $error = 'Gagal membuat booking. Silakan coba lagi.';
        }
    } else {
        $error = 'Slot waktu tidak tersedia. Silakan pilih waktu lain.';
    }
}
ob_start();
?>

<section class="section wrap">
    <div class="page-head">
        <h1>Booking Layanan</h1>
        <p>Pilih layanan dan waktu yang pas untukmu.</p>
    </div>

    <?php if (isset($error)): ?>
        <div class="notice notice-error" role="alert">
            <svg class="ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span><?php echo $error; ?></span>
        </div>
    <?php endif; ?>

    <div class="book2">
        <div class="panel book-main">
            <form method="POST" id="bookingForm">
                <div class="step">
                    <div class="step-head">
                        <span class="step-num">1</span>
                        <h3>Pilih Layanan</h3>
                    </div>
                    <div class="cat-tabs">
                        <button type="button" class="cat-tab on" data-bcat="ALL">Semua</button>
                        <button type="button" class="cat-tab" data-bcat="HAIRCUT">Haircut</button>
                        <button type="button" class="cat-tab" data-bcat="HAIRSTYLING">Hairstyling</button>
                        <button type="button" class="cat-tab" data-bcat="COLORING">Coloring</button>
                    </div>
                    <?php
                    $bgroups = ['HAIRCUT' => [], 'HAIRSTYLING' => [], 'COLORING' => []];
                    foreach ($services as $s) {
                        $c = serviceCategory($s);
                        if (!isset($bgroups[$c])) $bgroups[$c] = [];
                        $bgroups[$c][] = $s;
                    }
                    ?>
                    <?php foreach ($bgroups as $gname => $items): ?>
                        <?php if (count($items) === 0) continue; ?>
                        <div class="book-sec" data-bsec="<?php echo $gname; ?>">
                            <h4 class="book-sec-t"><?php echo ucfirst(strtolower($gname)); ?></h4>
                            <div class="pick-grid">
                                <?php foreach ($items as $service): ?>
                                <label class="pick">
                                    <input type="radio" name="service_id" class="service-radio" value="<?php echo $service['id']; ?>" data-price="<?php echo $service['price']; ?>" data-duration="<?php echo $service['duration']; ?>" required <?php echo (isset($_GET['service']) && $_GET['service'] == $service['id']) ? 'checked' : ''; ?>>
                                    <div class="pick-box">
                                        <h4><?php echo htmlspecialchars($service['name']); ?></h4>
                                        <p class="desc"><?php echo htmlspecialchars($service['description']); ?></p>
                                        <div class="pick-meta">
                                            <span class="mini-price"><?php echo formatPrice($service['price']); ?></span>
                                            <span class="mini-dur"><?php echo $service['duration']; ?> mnt</span>
                                        </div>
                                    </div>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="step">
                    <div class="step-head">
                        <span class="step-num">2</span>
                        <h3>Pilih Tanggal &amp; Jam</h3>
                    </div>
                    <div class="field">
                        <label class="label" for="bookingDate">Tanggal</label>
                        <div class="cal7" id="calendarGrid"></div>
                        <input type="hidden" name="booking_date" id="bookingDate" required>
                    </div>
                    <div class="field">
                        <span class="label">Jam</span>
                        <div id="timeSlots" class="slotbox">
                            <p>Pilih tanggal untuk melihat jam tersedia</p>
                        </div>
                    </div>
                </div>

                <div class="step">
                    <div class="step-head">
                        <span class="step-num">3</span>
                        <h3>Catatan (Opsional)</h3>
                    </div>
                    <textarea class="area" name="notes" rows="3" placeholder="Contoh: Model undercut, rapikan samping..."></textarea>
                </div>

                <button type="submit" class="button button-primary button-block" id="submitBtn" disabled>Konfirmasi Booking</button>
            </form>
        </div>

        <aside class="sum">
            <h3>Ringkasan</h3>
            <div id="bookingSummary"><p class="idle">Silakan pilih layanan</p></div>
        </aside>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarGrid = document.getElementById('calendarGrid');
    var bookingDateInput = document.getElementById('bookingDate');
    var timeSlotsDiv = document.getElementById('timeSlots');
    var summaryDiv = document.getElementById('bookingSummary');
    var submitBtn = document.getElementById('submitBtn');
    var selectedService = null, selectedDate = null, selectedTime = null;

    function esc(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}

    function generateCalendar() {
        var today = new Date(); today.setHours(0,0,0,0);
        calendarGrid.innerHTML = '';
        for (var i = 0; i < 30; i++) {
            (function(i){
                var date = new Date(today); date.setDate(date.getDate() + i);
                var dateStr = date.toISOString().split('T')[0];
                var dayName = date.toLocaleDateString('id-ID', {weekday:'short'});
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'cal-day';
                btn.innerHTML = '<b>' + date.getDate() + '</b><span>' + dayName + '</span>';
                btn.dataset.date = dateStr;
                btn.addEventListener('click', function() {
                    var all = calendarGrid.querySelectorAll('.cal-day');
                    for (var k = 0; k < all.length; k++) all[k].classList.remove('on');
                    btn.classList.add('on');
                    selectedDate = dateStr; bookingDateInput.value = dateStr;
                    if (selectedService) loadTimeSlots();
                    updateSummary();
                });
                calendarGrid.appendChild(btn);
            })(i);
        }
    }

    var radios = document.querySelectorAll('.service-radio');
    var bcatTabs = document.querySelectorAll('[data-bcat]');
    for (var t = 0; t < bcatTabs.length; t++) {
        bcatTabs[t].addEventListener('click', function() {
            for (var k = 0; k < bcatTabs.length; k++) bcatTabs[k].classList.remove('on');
            this.classList.add('on');
            var c = this.dataset.bcat;
            var secs = document.querySelectorAll('[data-bsec]');
            for (var s = 0; s < secs.length; s++) {
                secs[s].style.display = (c === 'ALL' || secs[s].dataset.bsec === c) ? '' : 'none';
            }
        });
    }
    for (var r = 0; r < radios.length; r++) {
        radios[r].addEventListener('change', function() {
            var h4 = this.closest('label').querySelector('h4');
            selectedService = {id: this.value, name: h4 ? h4.textContent : '', price: parseFloat(this.dataset.price), duration: parseInt(this.dataset.duration, 10)};
            selectedTime = null; submitBtn.disabled = true;
            updateSummary();
            if (selectedDate) loadTimeSlots();
        });
    }

    function loadTimeSlots() {
        timeSlotsDiv.className = 'slotbox';
        timeSlotsDiv.innerHTML = '<p>Memuat jam tersedia...</p>';
        fetch('/Barz/api/get_slots.php?service_id=' + encodeURIComponent(selectedService.id) + '&date=' + encodeURIComponent(selectedDate))
            .then(function(r){return r.json();})
            .then(function(data) {
                var available = data.slots ? data.slots.filter(function(s){return s.available;}) : [];
                if (available.length === 0) {
                    timeSlotsDiv.innerHTML = '<p>Tidak ada jam tersedia di tanggal ini.</p>';
                    return;
                }
                var html = '<div class="slotgrid">';
                available.forEach(function(s) {
                    html += '<button type="button" class="slot" data-time="' + esc(s.start) + '">' + esc(s.start) + '</button>';
                });
                html += '</div><input type="hidden" name="start_time" id="startTime" required>';
                timeSlotsDiv.className = 'slotbox ready';
                timeSlotsDiv.innerHTML = html;
                var btns = timeSlotsDiv.querySelectorAll('.slot');
                for (var i = 0; i < btns.length; i++) {
                    btns[i].addEventListener('click', function(e) {
                        e.preventDefault();
                        var all = timeSlotsDiv.querySelectorAll('.slot');
                        for (var k = 0; k < all.length; k++) all[k].classList.remove('on');
                        this.classList.add('on');
                        selectedTime = this.dataset.time;
                        document.getElementById('startTime').value = selectedTime;
                        updateSummary(); submitBtn.disabled = false;
                    });
                }
            })
            .catch(function() { timeSlotsDiv.innerHTML = '<p>Gagal memuat jam. Coba lagi.</p>'; });
    }

    function updateSummary() {
        if (!selectedService) { summaryDiv.innerHTML = '<p class="idle">Silakan pilih layanan</p>'; return; }
        var h = '';
        h += '<div class="sum-row"><span class="k">Layanan</span><span class="v">' + esc(selectedService.name) + '</span></div>';
        h += '<div class="sum-row"><span class="k">Durasi</span><span class="v">' + selectedService.duration + ' menit</span></div>';
        if (selectedDate) {
            var d = new Date(selectedDate + 'T00:00:00');
            h += '<div class="sum-row"><span class="k">Tanggal</span><span class="v">' + esc(d.toLocaleDateString('id-ID',{day:'numeric',month:'short',year:'numeric'})) + '</span></div>';
        }
        if (selectedTime) {
            h += '<div class="sum-row"><span class="k">Jam</span><span class="v">' + esc(selectedTime) + '</span></div>';
        }
        h += '<div class="sum-total"><span>Total</span><span class="v">Rp ' + selectedService.price.toLocaleString('id-ID') + '</span></div>';
        summaryDiv.innerHTML = h;
    }

    generateCalendar();
    var cs = document.querySelector('.service-radio:checked');
    if (cs) { var ev = document.createEvent('HTMLEvents'); ev.initEvent('change', true, false); cs.dispatchEvent(ev); }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../includes/layout.php';
?>
