<main class="mx-auto w-full max-w-[1600px] px-4 py-8 sm:px-6 lg:px-8 max-[768px]:pt-[76px]">
    <header class="mb-7 flex flex-col gap-5 border-b border-barz-line pb-6 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="mb-2 text-xs font-bold uppercase tracking-[0.18em] text-barz-accent">Administrasi Barz · PHP Native MVC</p>
            <h1 class="text-3xl font-extrabold tracking-tight text-barz-ink sm:text-4xl">Kelola Layanan</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-stone-600">Atur katalog layanan, harga, durasi, dan status layanan yang tersedia untuk pelanggan.</p>
        </div>
        <button type="button" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-barz-accent px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-barz-accent-hover hover:shadow-md focus:outline-none focus:ring-4 focus:ring-[#889063]/20" onclick="document.getElementById('serviceModal').showModal()">
            <svg class="h-5 w-5 fill-none stroke-current stroke-2" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke-linecap="round"/></svg>
            Tambah Layanan
        </button>
    </header>

    <?php if ($successMessage): ?>
        <div class="mb-5 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-sm text-emerald-900" role="status">
            <svg class="mt-0.5 h-5 w-5 shrink-0 fill-none stroke-current stroke-2" viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <p><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    <?php endif; ?>
    <?php if ($errorMessage): ?>
        <div class="mb-5 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-sm text-rose-900" role="alert">
            <svg class="mt-0.5 h-5 w-5 shrink-0 fill-none stroke-current stroke-2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8v4m0 4h.01" stroke-linecap="round"/></svg>
            <p><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    <?php endif; ?>

    <section class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3" aria-label="Ringkasan layanan">
        <article class="rounded-2xl border border-barz-line bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-stone-500">Total layanan</p><p class="mt-2 text-3xl font-extrabold tracking-tight text-barz-ink"><?= (int) $totalServices ?></p><p class="mt-1 text-xs text-stone-500">Seluruh katalog terdaftar</p>
        </article>
        <article class="rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-stone-500">Layanan aktif</p><p class="mt-2 text-3xl font-extrabold tracking-tight text-emerald-800"><?= (int) $activeServices ?></p><p class="mt-1 text-xs text-stone-500">Dapat dipilih pelanggan</p>
        </article>
        <article class="rounded-2xl border border-amber-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-stone-500">Layanan nonaktif</p><p class="mt-2 text-3xl font-extrabold tracking-tight text-amber-800"><?= (int) $inactiveServices ?></p><p class="mt-1 text-xs text-stone-500">Tersimpan untuk riwayat</p>
        </article>
    </section>

    <section class="overflow-hidden rounded-2xl border border-barz-line bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-barz-line bg-white p-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div><h2 class="text-lg font-bold text-barz-ink">Daftar layanan</h2><p class="mt-1 text-sm text-stone-500">Tambah, edit, cari, ubah status, atau hapus layanan.</p></div>
            <form method="GET" action="services.php" class="relative block w-full sm:max-w-sm">
                <label class="sr-only" for="serviceSearch">Cari layanan</label>
                <input type="search" id="serviceSearch" name="search" value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-xl border border-stone-200 bg-barz-bg py-2.5 px-4 text-sm text-barz-ink outline-none transition placeholder:text-stone-400 focus:border-barz-accent focus:ring-4 focus:ring-[#889063]/15" placeholder="Cari nama atau deskripsi...">
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] border-collapse text-left">
                <thead class="bg-barz-bg"><tr>
                    <th class="border-b border-barz-line px-5 py-4 text-xs font-bold uppercase tracking-wider text-stone-500">Layanan</th>
                    <th class="border-b border-barz-line px-5 py-4 text-xs font-bold uppercase tracking-wider text-stone-500">Deskripsi</th>
                    <th class="border-b border-barz-line px-5 py-4 text-xs font-bold uppercase tracking-wider text-stone-500">Harga</th>
                    <th class="border-b border-barz-line px-5 py-4 text-xs font-bold uppercase tracking-wider text-stone-500">Durasi</th>
                    <th class="border-b border-barz-line px-5 py-4 text-xs font-bold uppercase tracking-wider text-stone-500">Status</th>
                    <th class="border-b border-barz-line px-5 py-4 text-right text-xs font-bold uppercase tracking-wider text-stone-500">Aksi</th>
                </tr></thead>
                <tbody class="divide-y divide-barz-line">
                    <?php if (!$services): ?>
                        <tr><td colspan="6" class="px-6 py-14 text-center"><p class="font-semibold text-barz-ink">Belum ada layanan</p><p class="mt-1 text-sm text-stone-500">Tambahkan layanan pertama untuk mulai menerima booking.</p></td></tr>
                    <?php else: ?>
                        <?php foreach ($services as $service): ?>
                            <tr class="transition-colors odd:bg-white even:bg-[#fcfbf8] hover:bg-[#f5f4ec]">
                                <td class="px-5 py-4 align-middle"><p class="font-bold text-barz-ink"><?= htmlspecialchars($service['name'], ENT_QUOTES, 'UTF-8') ?></p><p class="mt-0.5 text-xs text-stone-500">ID layanan #<?= (int) $service['id'] ?></p></td>
                                <td class="max-w-xs px-5 py-4 text-sm leading-6 text-stone-600"><?= htmlspecialchars($service['description'] ?: 'Belum ada deskripsi', ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="whitespace-nowrap px-5 py-4 text-sm font-bold text-barz-ink"><?= formatPrice($service['price']) ?></td>
                                <td class="whitespace-nowrap px-5 py-4"><span class="inline-flex rounded-lg bg-stone-100 px-2.5 py-1.5 text-xs font-semibold text-stone-700"><?= (int) $service['duration'] ?> menit</span></td>
                                <td class="px-5 py-4">
                                    <?php if ((int) $service['is_active'] === 1): ?>
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800">Aktif</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-stone-200 bg-stone-100 px-2.5 py-1 text-xs font-semibold text-stone-600">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4"><div class="flex items-center justify-end gap-2">
                                    <a href="services.php?edit_service=<?= (int) $service['id'] ?>" class="inline-flex items-center justify-center rounded-lg border border-stone-200 bg-white px-3 py-2 text-xs font-bold text-stone-700 no-underline hover:bg-stone-50">Edit</a>
                                    <form method="POST" action="services.php">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="action" value="toggle">
                                        <input type="hidden" name="service_id" value="<?= (int) $service['id'] ?>">
                                        <button type="submit" class="inline-flex items-center justify-center rounded-lg border px-3 py-2 text-xs font-bold transition <?= (int) $service['is_active'] === 1 ? 'border-amber-200 bg-amber-50 text-amber-800 hover:bg-amber-100' : 'border-emerald-200 bg-emerald-50 text-emerald-800 hover:bg-emerald-100' ?>"><?= (int) $service['is_active'] === 1 ? 'Nonaktifkan' : 'Aktifkan' ?></button>
                                    </form>
                                    <form method="POST" action="services.php" onsubmit="return confirm('Hapus layanan ini? Jika sudah memiliki booking, layanan akan dinonaktifkan agar riwayat tetap aman.');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="service_id" value="<?= (int) $service['id'] ?>">
                                        <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-700 transition hover:border-rose-300 hover:bg-rose-100" aria-label="Hapus atau arsipkan layanan" title="Hapus / arsipkan layanan">
                                            <svg class="h-4.5 w-4.5 fill-none stroke-current stroke-2" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2m-11 0 1 14h12l1-14M10 10v6m4-6v6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                    </form>
                                </div></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="flex flex-col gap-1 border-t border-barz-line bg-barz-bg px-5 py-3 text-xs text-stone-500 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <span>Layanan yang sudah dipakai pada booking akan dinonaktifkan, bukan dihapus.</span>
            <span><?= (int) $activeServices ?> aktif dari <?= (int) $totalServices ?> layanan</span>
        </div>
    </section>

    <section class="mt-6 rounded-2xl border border-barz-line bg-barz-surface p-5">
        <h2 class="font-bold text-barz-ink">Pengelolaan layanan pada aplikasi Barz</h2>
        <p class="mt-2 text-sm leading-6 text-stone-600">Halaman ini menggunakan pola MVC manual: Controller memproses permintaan, Model menjalankan query MySQLi, dan View menampilkan data layanan. Fitur ini tetap memakai tabel <code>services</code> yang sama dengan booking Barz.</p>
    </section>
</main>

<dialog id="serviceModal" class="m-auto w-[calc(100%-2rem)] max-w-xl rounded-2xl border border-barz-line bg-white p-0 text-barz-ink shadow-2xl backdrop:bg-[rgba(20,30,15,.58)] backdrop:backdrop-blur-sm">
    <form method="POST" action="services.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="action" value="<?= $editingService ? 'update' : 'add' ?>">
        <?php if ($editingService): ?><input type="hidden" name="service_id" value="<?= (int) $editingService['id'] ?>"><?php endif; ?>
        <div class="flex items-start justify-between gap-4 border-b border-barz-line px-6 py-5">
            <div><h2 class="text-xl font-extrabold text-barz-ink"><?= $editingService ? 'Edit layanan' : 'Tambah layanan' ?></h2><p class="mt-1 text-sm text-stone-500">Masukkan rincian layanan yang akan tersedia untuk pelanggan.</p></div>
            <button type="button" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-xl text-stone-500 transition hover:bg-stone-100 hover:text-stone-800" onclick="this.closest('dialog').close()" aria-label="Tutup">&times;</button>
        </div>
        <div class="space-y-4 px-6 py-5">
            <div><label for="serviceName" class="mb-1.5 block text-sm font-semibold">Nama layanan</label><input id="serviceName" type="text" name="name" maxlength="100" value="<?= htmlspecialchars($editingService['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-xl border border-stone-200 bg-white px-3.5 py-3 text-sm outline-none transition focus:border-barz-accent focus:ring-4 focus:ring-[#889063]/15" placeholder="Contoh: Gentleman's Cut" required></div>
            <div><label for="serviceDescription" class="mb-1.5 block text-sm font-semibold">Deskripsi</label><textarea id="serviceDescription" name="description" rows="3" class="w-full resize-y rounded-xl border border-stone-200 bg-white px-3.5 py-3 text-sm outline-none transition focus:border-barz-accent focus:ring-4 focus:ring-[#889063]/15" placeholder="Jelaskan layanan secara singkat..."><?= htmlspecialchars($editingService['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea></div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div><label for="servicePrice" class="mb-1.5 block text-sm font-semibold">Harga (Rp)</label><input id="servicePrice" type="number" name="price" min="1" step="0.01" value="<?= htmlspecialchars((string) ($editingService['price'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-xl border border-stone-200 bg-white px-3.5 py-3 text-sm outline-none transition focus:border-barz-accent focus:ring-4 focus:ring-[#889063]/15" placeholder="50000" required></div>
                <div><label for="serviceDuration" class="mb-1.5 block text-sm font-semibold">Durasi (menit)</label><input id="serviceDuration" type="number" name="duration" min="1" max="480" value="<?= (int) ($editingService['duration'] ?? 30) ?>" class="w-full rounded-xl border border-stone-200 bg-white px-3.5 py-3 text-sm outline-none transition focus:border-barz-accent focus:ring-4 focus:ring-[#889063]/15" required></div>
            </div>
            <label class="flex items-center gap-2 text-sm font-medium"><input type="checkbox" name="is_active" value="1" <?= !$editingService || (int) $editingService['is_active'] === 1 ? 'checked' : '' ?> class="h-4 w-4 accent-barz-accent">Aktif dan dapat dipilih customer</label>
        </div>
        <div class="flex flex-col-reverse gap-2 border-t border-barz-line bg-barz-bg px-6 py-4 sm:flex-row sm:justify-end">
            <a href="services.php" class="rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-center text-sm font-semibold text-stone-700 no-underline transition hover:bg-stone-50">Batal</a>
            <button type="submit" class="rounded-xl bg-barz-accent px-5 py-2.5 text-sm font-bold text-white transition hover:bg-barz-accent-hover focus:outline-none focus:ring-4 focus:ring-[#889063]/20"><?= $editingService ? 'Simpan perubahan' : 'Simpan layanan' ?></button>
        </div>
    </form>
</dialog>
<?php if ($editingService): ?>
<script>document.addEventListener('DOMContentLoaded', function () { document.getElementById('serviceModal').showModal(); });</script>
<?php endif; ?>
