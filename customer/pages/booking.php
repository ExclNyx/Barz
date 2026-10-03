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
            redirect('my-bookings', 'customer');
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
                <!-- STEP 1: PILIH LAYANAN -->
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

                <!-- STEP 2: PILIH TANGGAL & JAM - REFERENCE STYLE -->
                <div class="step">
                    <div class="step-head">
                        <span class="step-num">2</span>
                        <h3>Pilih Tanggal & Jam</h3>
                    </div>

                    <!-- Venue header matching reference image -->
                    <div class="booking-venue-header">
                        <div class="venue-title">Barz Barbershop</div>
                        <div class="venue-location">
                            <svg class="ic" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                                    </div>
                    </div>
                    
                    <div class="datetime-picker">
                        <!-- Calendar Card (Top) -->
                        <div class="cal-panel">
                            <div class="cal-card">
                                <div class="cal-head">
                                    <div class="cal-nav">
                                        <button type="button" class="cal-nav-btn" id="prevMonth" aria-label="Bulan sebelumnya">
                                            <svg class="ic" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                                        </button>
                                        <span id="calMonthYear" class="cal-title"></span>
                                        <button type="button" class="cal-nav-btn" id="nextMonth" aria-label="Bulan berikutnya">
                                            <svg class="ic" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="cal-weekdays" id="calWeekdays"></div>
                                <div class="cal7" id="calendarGrid"></div>
                                <input type="hidden" name="booking_date" id="bookingDate" required>
                            </div>

                            <p class="cal-hint">*booking lebih dari 1 sesi bisa secara langsung</p>

                            <!-- Legend matching reference image -->
                            <div class="slots-legend">
                                <span class="legend-item">
                                    <span class="legend-dot available"></span> Sesi tersedia
                                </span>
                                <span class="legend-item">
                                    <span class="legend-dot reserved"></span> Sesi telah direservasi
                                </span>
                            </div>
                        </div>

                        <!-- Time Slots Panel (Bottom) -->
                        <div class="slots-panel">
                            <div id="timeSlots" class="slots-grid">
                                <div class="slots-placeholder">
                                    <svg class="ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <p>Pilih tanggal untuk melihat jam</p>
                                </div>
                            </div>
                            <input type="hidden" name="start_time" id="startTime">
                        </div>
                    </div>
                </div>

                <!-- STEP 3: CATATAN -->
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
    var startTimeInput = document.getElementById('startTime');
    var selectedService = null, selectedDate = null, selectedTime = null;

    // Calendar state - Reference Style (Sunday / Sun start)
    var currentMonth = new Date();
    currentMonth.setDate(1);
    var today = new Date();
    today.setHours(0,0,0,0);
    var weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

    function esc(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}

    function renderCalendar() {
        var year = currentMonth.getFullYear();
        var month = currentMonth.getMonth();
        
        // Update header - "September 2026" format matching reference photo
        var monthName = currentMonth.toLocaleDateString('en-US', { month: 'long' });
        document.getElementById('calMonthYear').innerHTML = 
            '<span class="cal-month">' + monthName + '</span><span class="cal-year">' + year + '</span>';
        
        // First day of month & days in month (Sunday start = 0)
        var firstDay = new Date(year, month, 1);
        var startDay = firstDay.getDay(); // 0=Sunday
        var daysInMonth = new Date(year, month + 1, 0).getDate();
        var daysInPrevMonth = new Date(year, month, 0).getDate();
        var startIndex = startDay;
        
        // Weekday headers (Sun-Sat)
        var wdHtml = '';
        weekdays.forEach(function(d) { wdHtml += '<div class="cal-weekday">' + d + '</div>'; });
        document.getElementById('calWeekdays').innerHTML = wdHtml;
        
        // Generate days - fixed 6 rows x 7 cols = 42 cells
        calendarGrid.innerHTML = '';
        var totalCells = 42;
        
        for (var i = 0; i < totalCells; i++) {
            var btn = document.createElement('button');
            btn.type = 'button';
            var dayNum, dateObj, dateStr, isCurrentMonth = true, isPast = false, isToday = false;
            
            if (i < startIndex) {
                // Previous month days
                dayNum = daysInPrevMonth - startIndex + i + 1;
                dateObj = new Date(year, month - 1, dayNum);
                isCurrentMonth = false;
            } else if (i >= startIndex + daysInMonth) {
                // Next month days
                dayNum = i - (startIndex + daysInMonth) + 1;
                dateObj = new Date(year, month + 1, dayNum);
                isCurrentMonth = false;
            } else {
                // Current month
                dayNum = i - startIndex + 1;
                dateObj = new Date(year, month, dayNum);
            }
            
            dateObj.setHours(0,0,0,0);
            dateStr = dateObj.toISOString().split('T')[0];
            isToday = dateObj.getTime() === today.getTime();
            isPast = dateObj < today;
            
            btn.className = 'cal-day';
            if (!isCurrentMonth) btn.classList.add('other-month');
            if (isPast && isCurrentMonth) btn.classList.add('past');
            if (isToday) btn.classList.add('today');
            if (selectedDate === dateStr) btn.classList.add('selected');
            
            btn.textContent = dayNum;
            btn.dataset.date = dateStr;
            
            if (isCurrentMonth && !isPast) {
                btn.addEventListener('click', function() {
                    var all = calendarGrid.querySelectorAll('.cal-day');
                    for (var k = 0; k < all.length; k++) all[k].classList.remove('selected');
                    this.classList.add('selected');
                    selectedDate = this.dataset.date;
                    bookingDateInput.value = selectedDate;
                    
                    // Auto-select first service if user hasn't selected one yet
                    if (!selectedService) {
                        var firstRadio = document.querySelector('.service-radio:checked') || document.querySelector('.service-radio');
                        if (firstRadio) {
                            firstRadio.checked = true;
                            var h4 = firstRadio.closest('label').querySelector('h4');
                            selectedService = {
                                id: firstRadio.value,
                                name: h4 ? h4.textContent : '',
                                price: parseFloat(firstRadio.dataset.price),
                                duration: parseInt(firstRadio.dataset.duration, 10)
                            };
                        }
                    }
                    
                    if (selectedService) loadTimeSlots();
                    updateSummary();
                });
            }
            
            calendarGrid.appendChild(btn);
        }
        
        // Update nav button state
        var maxDate = new Date(today.getFullYear(), today.getMonth() + 12, 0);
        document.getElementById('prevMonth').disabled = currentMonth <= today;
        document.getElementById('nextMonth').disabled = currentMonth >= maxDate;
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
            startTimeInput.value = '';
            updateSummary();
            if (selectedDate) loadTimeSlots();
        });
    }

    // Month navigation
    document.getElementById('prevMonth').addEventListener('click', function() {
        currentMonth.setMonth(currentMonth.getMonth() - 1);
        renderCalendar();
    });
    document.getElementById('nextMonth').addEventListener('click', function() {
        currentMonth.setMonth(currentMonth.getMonth() + 1);
        renderCalendar();
    });

    function loadTimeSlots() {
        timeSlotsDiv.className = 'slots-grid';
        timeSlotsDiv.innerHTML = '<div class="slots-placeholder"><svg class="ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><p>Memuat jam tersedia...</p></div>';
        fetch('/Barz/api/get_slots.php?service_id=' + encodeURIComponent(selectedService.id) + '&date=' + encodeURIComponent(selectedDate))
            .then(function(r){return r.json();})
            .then(function(data) {
                var available = data.slots ? data.slots.filter(function(s){return s.available;}) : [];
                if (available.length === 0) {
                    timeSlotsDiv.innerHTML = '<div class="slots-placeholder"><p>Tidak ada jam tersedia di tanggal ini.</p></div>';
                    return;
                }
                
                // Generate 15-min slots from 09:00 to 20:45 (reference style)
                var allSlots = [];
                var start = 9 * 60; // 09:00
                var end = 20 * 60 + 45; // 20:45
                for (var m = start; m <= end; m += 15) {
                    var h = Math.floor(m / 60);
                    var min = m % 60;
                    var timeStr = String(h).padStart(2, '0') + ':' + String(min).padStart(2, '0');
                    var slotData = available.find(function(s) { return s.start === timeStr; });
                    allSlots.push({
                        time: timeStr,
                        available: slotData ? true : false
                    });
                }
                
                // 5 columns grid
                var html = '<div class="slots-grid-5col">';
                allSlots.forEach(function(slot) {
                    var cls = 'slot-btn';
                    if (slot.available) {
                        cls += ' available';
                    } else {
                        cls += ' reserved';
                    }
                    if (selectedTime === slot.time) cls += ' selected';
                    html += '<button type="button" class="' + cls + '" data-time="' + esc(slot.time) + '"' + (slot.available ? '' : ' disabled') + '>' + esc(slot.time) + '</button>';
                });
                html += '</div>';
                timeSlotsDiv.innerHTML = html;
                
                var btns = timeSlotsDiv.querySelectorAll('.slot-btn.available');
                for (var i = 0; i < btns.length; i++) {
                    btns[i].addEventListener('click', function() {
                        var all = timeSlotsDiv.querySelectorAll('.slot-btn');
                        for (var k = 0; k < all.length; k++) all[k].classList.remove('selected');
                        this.classList.add('selected');
                        selectedTime = this.dataset.time;
                        startTimeInput.value = selectedTime;
                        updateSummary(); submitBtn.disabled = false;
                    });
                }
            })
            .catch(function() { timeSlotsDiv.innerHTML = '<div class="slots-placeholder"><p>Gagal memuat jam. Coba lagi.</p></div>'; });
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

    // Initial render
    renderCalendar();
    var cs = document.querySelector('.service-radio:checked');
    if (cs) { var ev = document.createEvent('HTMLEvents'); ev.initEvent('change', true, false); cs.dispatchEvent(ev); }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../customer/layout.php';
?>