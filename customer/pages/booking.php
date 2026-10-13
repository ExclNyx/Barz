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

<section class="section wrap max-w-280 my-0 mx-auto [padding-left:24px] [padding-right:24px] py-14 px-0 max-[600px]:py-10 max-[600px]:px-0">
    <div class="page-head [margin-bottom:32px]">
        <h1 class="font-sans font-bold leading-tight tracking-tight text-2xl [margin-bottom:6px]">Booking Layanan</h1>
        <p>Pilih layanan dan waktu yang pas untukmu.</p>
    </div>

    <?php if (isset($error)): ?>
        <div class="notice notice-error rounded-xl py-3.5 px-4 text-[.9rem] [margin-bottom:20px] flex gap-2 items-start bg-[rgba(217,127,110,.1)] text-[#a84f3a] border-l-[4px] border-barz-danger" role="alert">
            <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-4.5 h-4.5 [margin-top:2px]" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span><?php echo $error; ?></span>
        </div>
    <?php endif; ?>

    <div class="book2 grid grid-cols-[2fr_1fr] gap-5 [align-items:start] max-[960px]:grid-cols-[1fr]">
        <div class="panel book-main bg-barz-surface border border-barz-line rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.04)] p-7 max-[600px]:p-5.5">
            <form method="POST" id="bookingForm">
                <!-- STEP 1: PILIH LAYANAN -->
                <div class="step [margin-bottom:32px] last:[margin-bottom:0]">
                    <div class="step-head flex items-center gap-3 [margin-bottom:16px]">
                        <span class="step-num w-8 h-8 rounded-full bg-barz-accent text-white inline-flex items-center justify-center font-bold text-[.85rem] flex-none">1</span>
                        <h3 class="font-sans font-bold leading-tight tracking-tight text-xl">Pilih Layanan</h3>
                    </div>
                    <div class="cat-tabs flex gap-2 [margin-bottom:20px] flex-wrap">
                        <button type="button" class="cat-tab on font-sans py-2 px-4 border border-barz-line rounded-2xl bg-white text-[.85rem] font-semibold cursor-pointer transition no-underline text-barz-ink hover:border-barz-accent [&.on]:bg-barz-ink [&.on]:text-white [&.on]:border-barz-ink" data-bcat="ALL">Semua</button>
                        <button type="button" class="cat-tab font-sans py-2 px-4 border border-barz-line rounded-2xl bg-white text-[.85rem] font-semibold cursor-pointer transition no-underline text-barz-ink hover:border-barz-accent [&.on]:bg-barz-ink [&.on]:text-white [&.on]:border-barz-ink" data-bcat="HAIRCUT">Haircut</button>
                        <button type="button" class="cat-tab font-sans py-2 px-4 border border-barz-line rounded-2xl bg-white text-[.85rem] font-semibold cursor-pointer transition no-underline text-barz-ink hover:border-barz-accent [&.on]:bg-barz-ink [&.on]:text-white [&.on]:border-barz-ink" data-bcat="HAIRSTYLING">Hairstyling</button>
                        <button type="button" class="cat-tab font-sans py-2 px-4 border border-barz-line rounded-2xl bg-white text-[.85rem] font-semibold cursor-pointer transition no-underline text-barz-ink hover:border-barz-accent [&.on]:bg-barz-ink [&.on]:text-white [&.on]:border-barz-ink" data-bcat="COLORING">Coloring</button>
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
                        <div class="book-sec [margin-bottom:20px]" data-bsec="<?php echo $gname; ?>">
                            <h4 class="book-sec-t font-sans font-bold leading-tight tracking-tight text-base [margin-bottom:12px] [padding-bottom:8px] border-b border-barz-line"><?php echo ucfirst(strtolower($gname)); ?></h4>
                            <div class="pick-grid grid grid-cols-2 gap-3 max-[600px]:grid-cols-[1fr]">
                                <?php foreach ($items as $service): ?>
                                <label class="pick cursor-pointer">
                                    <input type="radio" name="service_id" class="service-radio absolute opacity-0 pointer-events-none" value="<?php echo $service['id']; ?>" data-price="<?php echo $service['price']; ?>" data-duration="<?php echo $service['duration']; ?>" required <?php echo (isset($_GET['service']) && $_GET['service'] == $service['id']) ? 'checked' : ''; ?>>
                                    <div class="pick-box p-4.5 border border-barz-line rounded-2xl transition-colors bg-white hover:border-barz-accent-hover [.pick_input:checked__+__&]:border-barz-accent [.pick_input:checked__+__&]:bg-[rgba(136,144,99,.07)]">
                                        <h4 class="font-sans font-bold leading-tight tracking-tight [margin-bottom:4px]"><?php echo htmlspecialchars($service['name']); ?></h4>
                                        <p class="desc text-[.78rem] text-barz-muted [margin-bottom:10px]"><?php echo htmlspecialchars($service['description']); ?></p>
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
                <div class="step [margin-bottom:32px] last:[margin-bottom:0]">
                    <div class="step-head flex items-center gap-3 [margin-bottom:16px]">
                        <span class="step-num w-8 h-8 rounded-full bg-barz-accent text-white inline-flex items-center justify-center font-bold text-[.85rem] flex-none">2</span>
                        <h3 class="font-sans font-bold leading-tight tracking-tight text-xl">Pilih Tanggal & Jam</h3>
                    </div>

                    <!-- Venue header matching reference image -->
                    <div class="booking-venue-header text-center [margin-bottom:24px]">
                        <div class="venue-title text-xl font-extrabold text-[#0f172a] tracking-[-0.01em] [margin-bottom:4px]">Barz Barbershop</div>
                        <div class="venue-location inline-flex items-center gap-1.5 text-[0.86rem] text-[#64748b] font-medium">
                            <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-4 h-4 text-[#94a3b8]" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                                    </div>
                    </div>
                    
                    <div class="datetime-picker bg-transparent p-0 max-w-115 my-0 mx-auto">
                        <!-- Calendar Card (Top) -->
                        <div class="cal-panel [margin-bottom:20px]">
                            <div class="cal-card bg-white rounded-2xl border border-[#eef2f6] shadow-[0_4px_20px_-2px_rgba(0,_0,_0,_.05)] py-6 px-5 max-[600px]:py-4 max-[600px]:px-2.5">
                                <div class="cal-head [margin-bottom:16px]">
                                    <div class="cal-nav flex items-center justify-between py-0 px-1.5">
                                        <button type="button" class="cal-nav-btn font-sans w-8.5 h-8.5 bg-white border border-[#e2e8f0] cursor-pointer text-[#64748b] rounded-lg inline-flex items-center justify-center transition [&:hover:not(:disabled)]:bg-[#f8fafc] [&:hover:not(:disabled)]:text-[#0f172a] [&:hover:not(:disabled)]:border-[#cbd5e1] disabled:opacity-25 disabled:cursor-not-allowed" id="prevMonth" aria-label="Bulan sebelumnya">
                                            <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-4 h-4" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                                        </button>
                                        <span id="calMonthYear" class="cal-title text-base font-medium text-[#334155] tracking-[-0.01em] flex items-center gap-3"></span>
                                        <button type="button" class="cal-nav-btn font-sans w-8.5 h-8.5 bg-white border border-[#e2e8f0] cursor-pointer text-[#64748b] rounded-lg inline-flex items-center justify-center transition [&:hover:not(:disabled)]:bg-[#f8fafc] [&:hover:not(:disabled)]:text-[#0f172a] [&:hover:not(:disabled)]:border-[#cbd5e1] disabled:opacity-25 disabled:cursor-not-allowed" id="nextMonth" aria-label="Bulan berikutnya">
                                            <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-4 h-4" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="cal-weekdays grid grid-cols-7 [margin-bottom:8px]" id="calWeekdays"></div>
                                <div class="cal7 grid grid-cols-7 gap-1" id="calendarGrid"></div>
                                <input type="hidden" name="booking_date" id="bookingDate" required>
                            </div>

                            <p class="cal-hint text-[#94a3b8] text-[0.85rem] [font-style:italic] text-center mt-5 mx-0 mb-3.5">*booking lebih dari 1 sesi bisa secara langsung</p>

                            <!-- Legend matching reference image -->
                            <div class="slots-legend flex items-center justify-center gap-7 [margin-bottom:20px]">
                                <span class="legend-item inline-flex items-center gap-2 text-[0.85rem] font-medium text-[#334155]">
                                    <span class="legend-dot available w-4.5 h-4.5 rounded-[5px] inline-block flex-none [&.available]:bg-white [&.available]:border-[1.5px] [&.available]:border-dashed [&.available]:border-[#94a3b8] [&.reserved]:bg-[#142239] [&.reserved]:border-[1.5px] [&.reserved]:border-[#142239]"></span> Sesi tersedia
                                </span>
                                <span class="legend-item inline-flex items-center gap-2 text-[0.85rem] font-medium text-[#334155]">
                                    <span class="legend-dot reserved w-4.5 h-4.5 rounded-[5px] inline-block flex-none [&.available]:bg-white [&.available]:border-[1.5px] [&.available]:border-dashed [&.available]:border-[#94a3b8] [&.reserved]:bg-[#142239] [&.reserved]:border-[1.5px] [&.reserved]:border-[#142239]"></span> Sesi telah direservasi
                                </span>
                            </div>
                        </div>

                        <!-- Time Slots Panel (Bottom) -->
                        <div class="slots-panel">
                            <div id="timeSlots" class="slots-grid">
                                <div class="slots-placeholder text-center py-9 px-4 bg-white rounded-xl border border-dashed border-[#cbd5e1] text-[#64748b]">
                                    <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-8 h-8 mt-0 mx-auto mb-2 block text-[#94a3b8]" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <p class="m-0 text-[0.88rem]">Pilih tanggal untuk melihat jam</p>
                                </div>
                            </div>
                            <input type="hidden" name="start_time" id="startTime">
                        </div>
                    </div>
                </div>

                <!-- STEP 3: CATATAN -->
                <div class="step [margin-bottom:32px] last:[margin-bottom:0]">
                    <div class="step-head flex items-center gap-3 [margin-bottom:16px]">
                        <span class="step-num w-8 h-8 rounded-full bg-barz-accent text-white inline-flex items-center justify-center font-bold text-[.85rem] flex-none">3</span>
                        <h3 class="font-sans font-bold leading-tight tracking-tight text-xl">Catatan (Opsional)</h3>
                    </div>
                    <textarea class="area w-full py-3 px-3.5 border border-barz-line rounded bg-white text-barz-ink text-[.95rem] font-sans transition-colors focus:[outline:none] focus:border-barz-accent text-barz-muted" name="notes" rows="3" placeholder="Contoh: Model undercut, rapikan samping..."></textarea>
                </div>

                <button type="submit" class="button button-primary button-block font-sans inline-flex items-center justify-center gap-2 py-3.5 px-7 rounded-2xl font-semibold text-[.95rem] no-underline cursor-pointer border border-transparent transition-colors bg-barz-accent text-white hover:bg-barz-accent-hover w-full disabled:opacity-50 disabled:cursor-not-allowed" id="submitBtn" disabled>Konfirmasi Booking</button>
            </form>
        </div>

        <aside class="sum bg-[#4c3d19] text-white rounded-2xl shadow-[0_8px_24px_rgba(0,0,0,0.14)] p-7 sticky top-22 max-[960px]:static">
            <h3 class="font-sans font-bold leading-tight tracking-tight text-white text-xl [margin-bottom:16px] [padding-bottom:12px] border-b border-[rgba(255,255,255,.15)]">Ringkasan</h3>
            <div id="bookingSummary"><p class="idle text-[rgba(255,255,255,.6)] text-[0.88rem]">Silakan pilih layanan</p></div>
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
        weekdays.forEach(function(d) { wdHtml += '<div class="cal-weekday text-center text-[0.85rem] font-semibold text-[#1e293b] py-2 px-0 max-[600px]:text-xs">' + d + '</div>'; });
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
            
            btn.className = 'cal-day w-9.5 h-9.5 my-0 mx-auto border rounded-lg bg-transparent text-center cursor-pointer text-[0.92rem] font-medium text-[#334155] flex items-center justify-center transition font-sans [&:hover:not(.past):not(.other-month)]:bg-[#f1f5f9] [&:hover:not(.past):not(.other-month)]:text-[#0f172a] [&.other-month]:[color:#cbd5e1_!important] [&.other-month]:pointer-events-none [&.past]:[color:#cbd5e1_!important] [&.past]:pointer-events-none [&.today]:border-[1.5px] [&.today]:border-[#0ea5e9] [&.today]:text-[#0ea5e9] [&.today]:font-bold [&.selected]:[background-color:#0ea5e9_!important] [&.selected]:[color:#ffffff_!important] [&.selected]:font-bold [&.selected]:rounded-[10px] [&.selected]:shadow-[0_4px_12px_rgba(14,_165,_233,_.4)] max-[600px]:w-8 max-[600px]:h-8 max-[600px]:text-[0.82rem]';
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
                secs[s].classList.toggle('hidden', !(c === 'ALL' || secs[s].dataset.bsec === c));
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
        timeSlotsDiv.innerHTML = '<div class="slots-placeholder text-center py-9 px-4 bg-white rounded-xl border border-dashed border-[#cbd5e1] text-[#64748b]"><svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><p>Memuat jam tersedia...</p></div>';
        var slotRequest = window.BarzAPI
            ? window.BarzAPI.fetchSlots(selectedService.id, selectedDate)
            : fetch('/Barz/api/get_slots.php?service_id=' + encodeURIComponent(selectedService.id) + '&date=' + encodeURIComponent(selectedDate)).then(function(r){ return r.json(); });
        slotRequest
            .then(function(data) {
                var available = data.slots ? data.slots.filter(function(s){return s.available;}) : [];
                if (available.length === 0) {
                    timeSlotsDiv.innerHTML = '<div class="slots-placeholder text-center py-9 px-4 bg-white rounded-xl border border-dashed border-[#cbd5e1] text-[#64748b]"><p>Tidak ada jam tersedia di tanggal ini.</p></div>';
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
                var html = '<div class="slots-grid-5col grid grid-cols-5 gap-2 max-[600px]:gap-1.5">';
                allSlots.forEach(function(slot) {
                    var cls = 'slot-btn h-10.5 py-0 px-1 rounded-lg text-[0.82rem] font-semibold flex items-center justify-center font-sans transition tracking-[0.02em] [&.available]:bg-white [&.available]:border [&.available]:border-dashed [&.available]:border-[#cbd5e1] [&.available]:text-[#1e293b] [&.available]:cursor-pointer [&.available]:shadow-[0_1px_2px_rgba(0,0,0,0.03)] [&.available:hover]:border-[#0ea5e9] [&.available:hover]:border-solid [&.available:hover]:text-[#0ea5e9] [&.available:hover]:bg-[#f0f9ff] [&.available:hover]:-translate-y-px [&.reserved]:bg-[#142239] [&.reserved]:border [&.reserved]:border-[#142239] [&.reserved]:text-white [&.reserved]:cursor-not-allowed [&.reserved]:opacity-[0.98] [&.reserved]:shadow-[0_1px_3px_rgba(20,34,57,0.2)] [&.selected]:bg-[#0ea5e9] [&.selected]:border-[1.5px] [&.selected]:border-[#0ea5e9] [&.selected]:text-white [&.selected]:font-bold [&.selected]:cursor-pointer [&.selected]:scale-[1.02] [&.selected]:shadow-[0_3px_12px_rgba(14,165,233,0.45)]';
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
            .catch(function() { timeSlotsDiv.innerHTML = '<div class="slots-placeholder text-center py-9 px-4 bg-white rounded-xl border border-dashed border-[#cbd5e1] text-[#64748b]"><p>Gagal memuat jam. Coba lagi.</p></div>'; });
    }

    function updateSummary() {
        if (!selectedService) { summaryDiv.innerHTML = '<p class="idle">Silakan pilih layanan</p>'; return; }
        var h = '';
        h += '<div class="sum-row flex justify-between gap-3 text-sm [margin-bottom:12px]"><span class="k">Layanan</span><span class="v">' + esc(selectedService.name) + '</span></div>';
        h += '<div class="sum-row flex justify-between gap-3 text-sm [margin-bottom:12px]"><span class="k">Durasi</span><span class="v">' + selectedService.duration + ' menit</span></div>';
        if (selectedDate) {
            var d = new Date(selectedDate + 'T00:00:00');
            h += '<div class="sum-row flex justify-between gap-3 text-sm [margin-bottom:12px]"><span class="k">Tanggal</span><span class="v">' + esc(d.toLocaleDateString('id-ID',{day:'numeric',month:'short',year:'numeric'})) + '</span></div>';
        }
        if (selectedTime) {
            h += '<div class="sum-row flex justify-between gap-3 text-sm [margin-bottom:12px]"><span class="k">Jam</span><span class="v">' + esc(selectedTime) + '</span></div>';
        }
        h += '<div class="sum-total flex justify-between [margin-top:16px] [padding-top:16px] border-t border-[rgba(255,255,255,.2)] text-lg"><span>Total</span><span class="v">Rp ' + selectedService.price.toLocaleString('id-ID') + '</span></div>';
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