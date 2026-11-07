/*
 * Barz API Integration
 * Laravel REST API + JWT dengan PHP Native sebagai fallback.
 *
 * Fitur:
 * 1. Menghubungkan frontend dengan Laravel API.
 * 2. Login dan register menggunakan Laravel API.
 * 3. Menyimpan JWT di sessionStorage.
 * 4. Mengambil jam booking dari Laravel.
 * 5. Mengambil jam booking dari PHP jika Laravel gagal
 *    atau tidak memberikan jam yang tersedia.
 * 6. Membuat booking melalui Laravel dengan fallback PHP.
 * 7. Logout Laravel dan PHP.
 */

(function () {
  "use strict";

  // =====================================================
  // 1. KONFIGURASI API
  // =====================================================

  var meta = document.querySelector('meta[name="barz-api-base-url"]');

  var API_BASE = (
    meta && meta.content ? meta.content : "http://127.0.0.1:8000/api"
  ).replace(/\/$/, "");

  var TOKEN_KEY = "barz_jwt";

  // =====================================================
  // 2. FUNGSI REQUEST LARAVEL API
  // =====================================================

  async function request(path, options) {
    var response = await fetch(API_BASE + path, options || {});

    var body = {};

    try {
      body = await response.json();
    } catch (error) {
      body = {};
    }

    if (!response.ok) {
      var failure = new Error(body.message || "Permintaan API gagal.");

      failure.status = response.status;
      failure.errors = body.errors || {};
      failure.payload = body;

      throw failure;
    }

    return body;
  }

  // =====================================================
  // 3. MANAJEMEN JWT TOKEN
  // =====================================================

  function token() {
    try {
      return window.sessionStorage.getItem(TOKEN_KEY);
    } catch (error) {
      return null;
    }
  }

  function saveToken(value) {
    try {
      window.sessionStorage.setItem(TOKEN_KEY, value);
    } catch (error) {
      // Storage tidak tersedia.
    }
  }

  function clearToken() {
    try {
      window.sessionStorage.removeItem(TOKEN_KEY);
    } catch (error) {
      // Abaikan jika storage tidak tersedia.
    }
  }

  // =====================================================
  // 4. MENAMPILKAN ERROR FORM
  // =====================================================

  function showFormErrors(form, errors, fallbackMessage) {
    var box = form.querySelector("[data-api-errors]");

    if (!box) {
      box = document.createElement("div");

      box.setAttribute("data-api-errors", "");
      box.setAttribute("role", "alert");

      box.className =
        "mb-4 rounded-xl border border-barz-danger " +
        "bg-[#fff1ee] px-4 py-3 text-sm text-[#8b3025]";

      form.prepend(box);
    }

    box.replaceChildren();

    var list = document.createElement("ul");

    list.className = "list-disc space-y-1 pl-5";

    var messages = [];

    Object.keys(errors || {}).forEach(function (field) {
      var fieldErrors = Array.isArray(errors[field])
        ? errors[field]
        : [errors[field]];

      fieldErrors.forEach(function (message) {
        messages.push(field + ": " + message);
      });
    });

    if (!messages.length) {
      messages.push(fallbackMessage || "Periksa kembali data yang dimasukkan.");
    }

    messages.forEach(function (message) {
      var item = document.createElement("li");

      item.textContent = message;

      list.appendChild(item);
    });

    box.appendChild(list);

    box.scrollIntoView({
      behavior: "smooth",
      block: "nearest",
    });
  }

  // =====================================================
  // 5. STATUS LOADING BUTTON
  // =====================================================

  function setBusy(button, busy, originalText) {
    if (!button) return;

    button.disabled = busy;

    button.classList.toggle("opacity-60", busy);

    button.classList.toggle("cursor-wait", busy);

    if (busy) {
      button.dataset.originalText = button.textContent.trim();

      button.textContent = originalText || "Memproses...";
    } else if (button.dataset.originalText) {
      button.textContent = button.dataset.originalText;

      delete button.dataset.originalText;
    }
  }

  // =====================================================
  // 6. REGISTER
  // =====================================================

  var registerForm = document.getElementById("registerForm");

  if (registerForm) {
    registerForm.addEventListener("submit", async function (event) {
      if (registerForm.dataset.nativeSubmit === "1") {
        return;
      }

      if (!registerForm.checkValidity()) {
        return;
      }

      event.preventDefault();

      var button = registerForm.querySelector('button[type="submit"]');

      setBusy(button, true, "Mendaftarkan akun...");

      var fields = new FormData(registerForm);

      try {
        await request("/auth/register", {
          method: "POST",

          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
          },

          body: JSON.stringify({
            name: fields.get("name"),

            email: fields.get("email"),

            phone: fields.get("phone"),

            password: fields.get("password"),

            password_confirmation: fields.get("confirm_password"),
          }),
        });

        clearToken();

        window.location.href =
          "/Barz/index.php?area=auth&page=login&registered=1";
      } catch (error) {
        if (error.status === 422) {
          showFormErrors(registerForm, error.errors, error.message);

          setBusy(button, false);

          return;
        }

        registerForm.dataset.nativeSubmit = "1";

        registerForm.submit();
      }
    });
  }

  // =====================================================
  // 7. LOGIN
  // =====================================================

  var loginForm = document.getElementById("loginForm");

  if (loginForm) {
    loginForm.addEventListener("submit", async function (event) {
      if (loginForm.dataset.nativeSubmit === "1") {
        return;
      }

      if (!loginForm.checkValidity()) {
        return;
      }

      event.preventDefault();

      var button = loginForm.querySelector('button[type="submit"]');

      setBusy(button, true, "Menghubungkan API...");

      var payload = Object.fromEntries(new FormData(loginForm).entries());

      try {
        var data = await request("/auth/login", {
          method: "POST",

          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
          },

          body: JSON.stringify({
            email: payload.email,

            password: payload.password,
          }),
        });

        if (data.access_token) {
          saveToken(data.access_token);
        }
      } catch (error) {
        // Login PHP Native tetap menjadi fallback.
        if (error.status === 401) {
          clearToken();
        }
      }

      loginForm.dataset.nativeSubmit = "1";

      loginForm.submit();
    });
  }

  // =====================================================
  // 8. BOOKING CUSTOMER
  // =====================================================

  var bookingForm = document.getElementById("bookingForm");

  if (bookingForm) {
    bookingForm.addEventListener("submit", async function (event) {
      var currentToken = token();

      // Jika tidak ada JWT, gunakan PHP Native.
      if (!currentToken || bookingForm.dataset.nativeSubmit === "1") {
        return;
      }

      if (!bookingForm.checkValidity()) {
        return;
      }

      event.preventDefault();

      var button = bookingForm.querySelector('button[type="submit"]');

      setBusy(button, true, "Menyimpan booking...");

      var fields = new FormData(bookingForm);

      var payload = {
        service_id: Number(fields.get("service_id")),

        booking_date: fields.get("booking_date"),

        start_time: fields.get("start_time"),

        notes: fields.get("notes") || "",
      };

      try {
        // Coba simpan melalui Laravel.
        var data = await request("/bookings", {
          method: "POST",

          headers: {
            Accept: "application/json",

            "Content-Type": "application/json",

            Authorization: "Bearer " + currentToken,
          },

          body: JSON.stringify(payload),
        });

        if (data.data || data.id) {
          window.location.href = "/Barz/index.php?page=my-bookings";

          return;
        }

        showFormErrors(
          bookingForm,
          {},
          "API merespons tetapi data booking tidak ditemukan.",
        );

        setBusy(button, false);
      } catch (error) {
        // -------------------------------------------------
        // Jika Laravel menolak slot karena tidak tersedia,
        // coba lagi melalui proses PHP Native.
        // -------------------------------------------------

        if (error.status === 422) {
          var startTimeErrors = error.errors && error.errors.start_time;

          var messages = Array.isArray(startTimeErrors)
            ? startTimeErrors
            : [startTimeErrors || ""];

          var slotUnavailable = messages.some(function (message) {
            return /slot.*tidak tersedia/i.test(String(message));
          });

          if (slotUnavailable) {
            bookingForm.dataset.nativeSubmit = "1";

            bookingForm.submit();

            return;
          }

          showFormErrors(bookingForm, error.errors, error.message);

          setBusy(button, false);

          return;
        }

        // -------------------------------------------------
        // Jika API tidak bisa dihubungi atau server error,
        // kembalikan proses booking ke PHP Native.
        // -------------------------------------------------

        if (error.status === 401) {
          clearToken();
        }

        bookingForm.dataset.nativeSubmit = "1";

        bookingForm.submit();
      }
    });
  }

  // =====================================================
  // 9. MENGAMBIL JAM BOOKING
  // =====================================================

  async function fetchSlots(serviceId, date) {
    var query =
      "?service_id=" +
      encodeURIComponent(serviceId) +
      "&date=" +
      encodeURIComponent(date);

    try {
      // Pertama, coba ambil jam dari Laravel.
      var data = await request("/slots" + query, {
        headers: {
          Accept: "application/json",
        },
      });

      var laravelSlots = Array.isArray(data.slots) ? data.slots : [];

      // Gunakan Laravel jika ada jam yang tersedia.
      if (
        laravelSlots.some(function (slot) {
          return slot.available === true;
        })
      ) {
        return data;
      }
    } catch (error) {
      // Laravel gagal, lanjut ke PHP Native.
    }

    // -------------------------------------------------
    // FALLBACK PHP NATIVE
    // -------------------------------------------------

    var fallback = await fetch("/Barz/api/get_slots.php" + query, {
      headers: {
        Accept: "application/json",
      },
    });

    if (!fallback.ok) {
      throw new Error("Gagal memuat slot waktu dari PHP.");
    }

    var fallbackData = await fallback.json();

    if (!Array.isArray(fallbackData.slots)) {
      throw new Error("Data slot PHP tidak valid.");
    }

    return fallbackData;
  }

  // =====================================================
  // 10. LOGOUT
  // =====================================================

  document
    .querySelectorAll('a[href*="/auth/logout.php"]')
    .forEach(function (link) {
      link.addEventListener("click", async function (event) {
        var currentToken = token();

        if (!currentToken || link.dataset.logoutStarted === "1") {
          clearToken();

          return;
        }

        event.preventDefault();

        link.dataset.logoutStarted = "1";

        try {
          await Promise.race([
            request("/auth/logout", {
              method: "POST",

              headers: {
                Accept: "application/json",

                Authorization: "Bearer " + currentToken,
              },
            }),

            new Promise(function (resolve) {
              window.setTimeout(resolve, 1500);
            }),
          ]);
        } catch (error) {
          // Logout PHP tetap berjalan meskipun Laravel mati.
        }

        clearToken();

        window.location.assign(link.href);
      });
    });

  // =====================================================
  // 11. PUBLIC API GLOBAL
  // =====================================================

  window.BarzAPI = {
    request: request,

    fetchSlots: fetchSlots,

    getToken: token,

    clearToken: clearToken,
  };
})();
