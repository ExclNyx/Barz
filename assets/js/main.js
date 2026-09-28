// Barz Barbershop - Main JavaScript
(function() {
    'use strict';

    // ---- Chat Widget ----
    var chatToggle = document.getElementById('chatToggle');
    var chatWindow = document.getElementById('chatWindow');
    var chatClose = document.getElementById('chatClose');
    var chatForm = document.getElementById('chatForm');
    var chatMessages = document.getElementById('chatMessages');
    var chatInput = document.getElementById('chatMessageInput');
    var unreadBadge = document.getElementById('unreadBadge');

    if (chatToggle && chatWindow) {
        chatToggle.addEventListener('click', function() {
            var isOpen = chatWindow.style.display === 'block';
            chatWindow.style.display = isOpen ? 'none' : 'flex';
            if (!isOpen) loadChatMessages(true);
        });
        if (chatClose) {
            chatClose.addEventListener('click', function() {
                chatWindow.style.display = 'none';
            });
        }
        if (chatForm) {
            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                sendMessage();
            });
        }
        setInterval(function() {
            if (chatWindow && chatWindow.style.display === 'flex') {
                loadChatMessages(false);
            }
        }, 5000);
    }

    function loadChatMessages(scrollToBottom) {
        if (!chatMessages) return;
        fetch('/Barz/api/chat_messages.php')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.messages && data.messages.length > 0) {
                    var html = '';
                    data.messages.forEach(function(msg) {
                        var isSent = msg.is_sent;
                        html += '<div class="chat-message ' + (isSent ? 'sent' : 'received') + '">' +
                            '<div class="chat-message-content">' +
                            '<div>' + escapeHtml(msg.message) + '</div>' +
                            '<div class="chat-message-time">' + msg.time + '</div>' +
                            '</div></div>';
                    });
                    chatMessages.innerHTML = html;
                    if (scrollToBottom) chatMessages.scrollTop = chatMessages.scrollHeight;
                    if (data.unread_count > 0 && unreadBadge) {
                        unreadBadge.textContent = data.unread_count;
                        unreadBadge.style.display = 'block';
                    } else if (unreadBadge) {
                        unreadBadge.style.display = 'none';
                    }
                } else {
                    chatMessages.innerHTML = '<p class="text-muted text-center small">Belum ada pesan. Mulai chat dengan admin!</p>';
                }
            })
            .catch(function(err) { console.error('Error:', err); });
    }

    function sendMessage() {
        if (!chatInput) return;
        var message = chatInput.value.trim();
        if (!message) return;
        fetch('/Barz/api/send_message.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: message })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) { chatInput.value = ''; loadChatMessages(); }
            else { alert('Gagal mengirim pesan.'); }
        })
        .catch(function(err) { alert('Terjadi kesalahan.'); });
    }

    // ---- Page-specific Chat ----
    var chatFormPage = document.getElementById('chatFormPage');
    var chatMessagesPage = document.getElementById('chatMessagesPage');
    var chatInputPage = document.getElementById('chatMessageInputPage');

    if (chatFormPage) {
        chatFormPage.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!chatInputPage) return;
            var message = chatInputPage.value.trim();
            if (!message) return;
            fetch('/Barz/api/send_message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: message })
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) { chatInputPage.value = ''; loadMessagesPage(); }
            });
        });
    }

    function loadMessagesPage() {
        if (!chatMessagesPage) return;
        fetch('/Barz/api/chat_messages.php')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.messages && data.messages.length > 0) {
                    var html = '<div class="space-y-4 flex flex-col">';
                    data.messages.forEach(function(msg) {
                        var isSent = msg.is_sent;
                        if (isSent) {
                            html += '<div class="self-end max-w-[80%] flex flex-col items-end">' +
                                '<div class="bg-barz-accent text-white px-5 py-3 rounded-2xl rounded-tr-sm shadow-sm text-sm">' +
                                escapeHtml(msg.message) + '</div>' +
                                '<span class="text-[10px] text-gray-400 mt-1">' + msg.time + '</span></div>';
                        } else {
                            html += '<div class="self-start max-w-[80%] flex flex-col items-start">' +
                                '<div class="bg-white border border-barz-border text-barz-text px-5 py-3 rounded-2xl rounded-tl-sm shadow-sm text-sm">' +
                                '<strong class="text-barz-dark text-xs block mb-1">Admin</strong>' +
                                escapeHtml(msg.message) + '</div>' +
                                '<span class="text-[10px] text-gray-400 mt-1">' + msg.time + '</span></div>';
                        }
                    });
                    html += '</div>';
                    chatMessagesPage.innerHTML = html;
                    chatMessagesPage.scrollTop = chatMessagesPage.scrollHeight;
                } else {
                    chatMessagesPage.innerHTML = '<div class="h-full flex flex-col items-center justify-center text-center text-gray-400"><i class="bi bi-chat-dots text-4xl mb-2"></i><p>Belum ada pesan. Mulai ngobrol!</p></div>';
                }
            });
    }
    if (chatMessagesPage) {
        loadMessagesPage();
        setInterval(loadMessagesPage, 3000);
    }

    // ---- Calendar for Booking Page ----
    var calendarGrid = document.getElementById('calendarGrid');
    if (calendarGrid) {
        generateCalendar(calendarGrid, function(dateStr) {
            var bookingDate = document.getElementById('bookingDate');
            if (bookingDate) bookingDate.value = dateStr;
            var timeSlotsDiv = document.getElementById('timeSlots');
            var serviceRadios = document.querySelectorAll('.service-radio:checked');
            if (serviceRadios.length > 0 && timeSlotsDiv) {
                var serviceId = serviceRadios[0].value;
                var barberId = document.querySelector('input[name="barber_id"]:checked') ? document.querySelector('input[name="barber_id"]:checked').value : '';
                timeSlotsDiv.innerHTML = '<div class="flex items-center gap-2 text-barz-accent"><i class="bi bi-arrow-repeat animate-spin text-2xl"></i> Loading...</div>';
                fetch('/Barz/api/get_slots.php?service_id=' + serviceId + '&date=' + dateStr + '&barber_id=' + barberId)
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.slots && data.slots.length > 0) {
                            var availableSlots = data.slots.filter(function(s) { return s.available; });
                            if (availableSlots.length === 0) {
                                timeSlotsDiv.innerHTML = '<p class="text-red-500 text-center text-sm"><i class="bi bi-exclamation-circle mr-2"></i>Tidak ada jam tersedia</p>';
                                return;
                            }
                            var html = '<div class="grid grid-cols-4 md:grid-cols-6 gap-2 w-full">';
                            availableSlots.forEach(function(slot) {
                                html += '<button type="button" class="time-slot-btn py-3 px-2 border border-barz-border rounded-lg text-sm font-bold text-barz-dark hover:border-barz-accent hover:bg-barz-accent/5 transition-all" data-time="' + slot.start + '">' + slot.start + '</button>';
                            });
                            html += '</div><input type="hidden" name="start_time" id="startTime" required>';
                            timeSlotsDiv.innerHTML = html;
                            timeSlotsDiv.classList.remove('bg-gray-50/50', 'border-dashed');
                            document.querySelectorAll('.time-slot-btn').forEach(function(btn) {
                                btn.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    document.querySelectorAll('.time-slot-btn').forEach(function(b) {
                                        b.classList.remove('bg-barz-accent', 'text-white', 'border-barz-accent');
                                    });
                                    this.classList.add('bg-barz-accent', 'text-white', 'border-barz-accent');
                                    var stInput = document.getElementById('startTime');
                                    if (stInput) stInput.value = this.dataset.time;
                                    updateBookingSummary();
                                    var submitBtn = document.getElementById('submitBtn');
                                    if (submitBtn) submitBtn.disabled = false;
                                });
                            });
                        } else {
                            timeSlotsDiv.innerHTML = '<p class="text-red-500 text-center text-sm"><i class="bi bi-exclamation-circle mr-2"></i>Tidak ada jam tersedia</p>';
                        }
                    })
                    .catch(function() {
                        timeSlotsDiv.innerHTML = '<p class="text-red-500 text-center text-sm"><i class="bi bi-exclamation-circle mr-2"></i>Error loading slots</p>';
                    });
            }
        });
    }

    function generateCalendar(gridEl, onDateClick) {
        gridEl.innerHTML = '';
        var today = new Date();
        today.setHours(0, 0, 0, 0);
        for (var i = 0; i < 30; i++) {
            var date = new Date(today);
            date.setDate(date.getDate() + i);
            var dateStr = date.toISOString().split('T')[0];
            var dayName = date.toLocaleDateString('id-ID', { weekday: 'short' });
            var dayNum = date.getDate();
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'p-2 rounded-lg border border-barz-border text-center hover:border-barz-accent transition-all text-sm';
            btn.innerHTML = '<div class="font-bold text-barz-dark">' + dayNum + '</div><div class="text-xs text-gray-500">' + dayName + '</div>';
            btn.dataset.date = dateStr;
            btn.addEventListener('click', function() {
                gridEl.querySelectorAll('button').forEach(function(b) {
                    b.classList.remove('border-barz-accent', 'bg-barz-accent/5');
                });
                this.classList.add('border-barz-accent', 'bg-barz-accent/5');
                if (onDateClick) onDateClick(this.dataset.date);
            });
            gridEl.appendChild(btn);
        }
    }

    function updateBookingSummary() {
        var summaryDiv = document.getElementById('bookingSummary');
        var serviceRadios = document.querySelectorAll('.service-radio:checked');
        if (!summaryDiv || serviceRadios.length === 0) return;
        var radio = serviceRadios[0];
        var label = radio.closest('label');
        var name = label ? (label.querySelector('h4') ? label.querySelector('h4').textContent : '') : '';
        var price = parseFloat(radio.dataset.price) || 0;
        var duration = parseInt(radio.dataset.duration) || 0;
        var dateStr = document.getElementById('bookingDate') ? document.getElementById('bookingDate').value : null;
        var activeSlot = document.querySelector('.time-slot-btn.active');
        var time = activeSlot ? activeSlot.dataset.time : null;
        var html = '<div class="space-y-3 text-sm">';
        html += '<div class="flex justify-between"><span class="text-gray-400">Layanan:</span> <span class="font-bold text-right">' + name + '</span></div>';
        html += '<div class="flex justify-between"><span class="text-gray-400">Durasi:</span> <span class="font-bold">' + duration + ' menit</span></div>';
        if (dateStr) {
            var d = new Date(dateStr + 'T00:00:00');
            var formatted = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            html += '<div class="flex justify-between"><span class="text-gray-400">Tanggal:</span> <span class="font-bold">' + formatted + '</span></div>';
        }
        if (time) {
            html += '<div class="flex justify-between"><span class="text-gray-400">Jam:</span> <span class="font-bold">' + time + '</span></div>';
        }
        html += '<div class="pt-4 mt-4 border-t border-gray-700 flex justify-between text-lg"><span class="text-gray-300">Total:</span> <span class="font-bold text-barz-accent">' + formatPrice(price) + '</span></div>';
        html += '</div>';
        summaryDiv.innerHTML = html;
    }

    function formatPrice(price) {
        return 'Rp ' + price.toLocaleString('id-ID');
    }

    function escapeHtml(text) {
        var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // ---- Auto-dismiss Alerts ----
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert:not(.alert-info)');
        alerts.forEach(function(alert) {
            if (!alert.querySelector('.btn-close')) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() { if (alert.parentNode) alert.remove(); }, 500);
            }
        });
    }, 3000);
})();
