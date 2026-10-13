/*
 * Barz API integration (Module 2 and Module 6 patterns): async/await, fetch,
 * JSON request bodies, bearer JWT and HTTP 422 validation handling.
 * The legacy PHP forms/endpoints remain available if the Laravel API is stopped.
 */
(function () {
    'use strict';

    var meta = document.querySelector('meta[name="barz-api-base-url"]');
    var API_BASE = (meta && meta.content ? meta.content : 'http://127.0.0.1:8000/api').replace(/\/$/, '');
    var TOKEN_KEY = 'barz_jwt';

    async function request(path, options) {
        var response = await fetch(API_BASE + path, options || {});
        var body = {};
        try { body = await response.json(); } catch (error) { body = {}; }
        if (!response.ok) {
            var failure = new Error(body.message || 'Permintaan API gagal.');
            failure.status = response.status;
            failure.errors = body.errors || {};
            failure.payload = body;
            throw failure;
        }
        return body;
    }

    function token() {
        try { return window.sessionStorage.getItem(TOKEN_KEY); } catch (error) { return null; }
    }

    function saveToken(value) {
        try { window.sessionStorage.setItem(TOKEN_KEY, value); } catch (error) { /* Storage may be unavailable in private contexts. */ }
    }

    function clearToken() {
        try { window.sessionStorage.removeItem(TOKEN_KEY); } catch (error) { /* Ignore unavailable storage. */ }
    }

    function showFormErrors(form, errors, fallbackMessage) {
        var box = form.querySelector('[data-api-errors]');
        if (!box) {
            box = document.createElement('div');
            box.setAttribute('data-api-errors', '');
            box.setAttribute('role', 'alert');
            box.className = 'mb-4 rounded-xl border border-barz-danger bg-[#fff1ee] px-4 py-3 text-sm text-[#8b3025]';
            form.prepend(box);
        }
        box.replaceChildren();
        var list = document.createElement('ul');
        list.className = 'list-disc space-y-1 pl-5';
        var messages = [];
        Object.keys(errors || {}).forEach(function (field) {
            (Array.isArray(errors[field]) ? errors[field] : [errors[field]]).forEach(function (message) {
                messages.push(field + ': ' + message);
            });
        });
        if (!messages.length) messages.push(fallbackMessage || 'Periksa kembali data yang dimasukkan.');
        messages.forEach(function (message) {
            var item = document.createElement('li');
            item.textContent = message;
            list.appendChild(item);
        });
        box.appendChild(list);
        box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function setBusy(button, busy, originalText) {
        if (!button) return;
        button.disabled = busy;
        button.classList.toggle('opacity-60', busy);
        button.classList.toggle('cursor-wait', busy);
        if (busy) {
            button.dataset.originalText = button.textContent.trim();
            button.textContent = originalText || 'Memproses...';
        } else if (button.dataset.originalText) {
            button.textContent = button.dataset.originalText;
            delete button.dataset.originalText;
        }
    }

    // Register through Laravel first; if the API is unavailable, keep the legacy PHP register path.
    var registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', async function (event) {
            if (registerForm.dataset.nativeSubmit === '1') return;
            if (!registerForm.checkValidity()) return;
            event.preventDefault();
            var button = registerForm.querySelector('button[type="submit"]');
            setBusy(button, true, 'Mendaftarkan akun...');
            var fields = new FormData(registerForm);
            try {
                await request('/auth/register', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        name: fields.get('name'),
                        email: fields.get('email'),
                        phone: fields.get('phone'),
                        password: fields.get('password'),
                        password_confirmation: fields.get('confirm_password')
                    })
                });
                clearToken();
                window.location.href = '/Barz/index.php?area=auth&page=login&registered=1';
            } catch (error) {
                if (error.status === 422) {
                    showFormErrors(registerForm, error.errors, error.message);
                    setBusy(button, false);
                    return;
                }
                registerForm.dataset.nativeSubmit = '1';
                registerForm.submit();
            }
        });
    }

    // Login: the API issues a JWT, then the regular PHP form establishes its session.
    var loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function (event) {
            if (loginForm.dataset.nativeSubmit === '1') return;
            if (!loginForm.checkValidity()) return;
            event.preventDefault();
            var button = loginForm.querySelector('button[type="submit"]');
            setBusy(button, true, 'Menghubungkan API...');
            var payload = Object.fromEntries(new FormData(loginForm).entries());
            try {
                var data = await request('/auth/login', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email: payload.email, password: payload.password })
                });
                if (data.access_token) saveToken(data.access_token);
            } catch (error) {
                // Continue with the legacy PHP login, which remains the session authority for this site.
                if (error.status === 401) clearToken();
            }
            loginForm.dataset.nativeSubmit = '1';
            loginForm.submit();
        });
    }

    // Booking: POST to Laravel when a JWT exists. If the API is stopped, fall back to native PHP.
    var bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', async function (event) {
            var currentToken = token();
            if (!currentToken || bookingForm.dataset.nativeSubmit === '1') return;
            if (!bookingForm.checkValidity()) return;
            event.preventDefault();
            var button = bookingForm.querySelector('button[type="submit"]');
            setBusy(button, true, 'Menyimpan booking...');
            var fields = new FormData(bookingForm);
            var payload = {
                service_id: Number(fields.get('service_id')),
                booking_date: fields.get('booking_date'),
                start_time: fields.get('start_time'),
                notes: fields.get('notes') || ''
            };
            try {
                var data = await request('/bookings', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + currentToken },
                    body: JSON.stringify(payload)
                });
                if (data.data || data.id) {
                    window.location.href = '/Barz/index.php?page=my-bookings';
                    return;
                }
                showFormErrors(bookingForm, {}, 'API merespons tetapi data booking tidak ditemukan.');
                setBusy(button, false);
            } catch (error) {
                if (error.status === 422) {
                    showFormErrors(bookingForm, error.errors, error.message);
                    setBusy(button, false);
                    return;
                }
                if (error.status === 401) clearToken();
                // A network/server failure must not block an otherwise working XAMPP flow.
                bookingForm.dataset.nativeSubmit = '1';
                bookingForm.submit();
            }
        });
    }

    // Use the Laravel slots API first; preserve the existing PHP slot endpoint as a fallback.
    async function fetchSlots(serviceId, date) {
        var query = '?service_id=' + encodeURIComponent(serviceId) + '&date=' + encodeURIComponent(date);
        try {
            return await request('/slots' + query, { headers: { 'Accept': 'application/json' } });
        } catch (error) {
            var fallback = await fetch('/Barz/api/get_slots.php' + query, { headers: { 'Accept': 'application/json' } });
            if (!fallback.ok) throw new Error('Gagal memuat slot waktu.');
            return fallback.json();
        }
    }

    // Clear JWT when the existing PHP session is logged out.
    document.querySelectorAll('a[href*="/auth/logout.php"]').forEach(function (link) {
        link.addEventListener('click', clearToken);
    });

    window.BarzAPI = { request: request, fetchSlots: fetchSlots, getToken: token, clearToken: clearToken };
})();
