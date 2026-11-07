<main class="mx-auto max-w-7xl px-5 py-8 sm:px-7 lg:px-9">
    <div class="mb-7 flex flex-col gap-4 border-b border-barz-line pb-5 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-barz-accent">Manajemen tim</p>
            <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-barz-ink">Kelola Barber</h1>
            <p class="mt-1 text-sm text-stone-500">Atur profil, spesialisasi, pengalaman, dan status barber yang tersedia untuk booking.</p>
        </div>
        <button type="button" onclick="document.getElementById('barberModal').showModal()" class="inline-flex items-center justify-center rounded-xl bg-barz-accent px-4 py-3 text-sm font-bold text-white transition hover:bg-barz-accent-hover">+ Tambah Barber</button>
    </div>

    <?php if (!empty($successMessage)): ?>
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800"><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if (!empty($errorMessage)): ?>
        <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="mb-6 grid gap-3 sm:grid-cols-3">
        <div class="rounded-2xl border border-barz-line bg-white p-5"><p class="text-sm text-stone-500">Total barber</p><p class="mt-1 text-2xl font-extrabold text-barz-ink"><?= (int) $totalBarbers ?></p></div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5"><p class="text-sm text-emerald-700">Aktif</p><p class="mt-1 text-2xl font-extrabold text-emerald-900"><?= (int) $activeBarbers ?></p></div>
        <div class="rounded-2xl border border-barz-line bg-barz-surface p-5"><p class="text-sm text-stone-500">Nonaktif</p><p class="mt-1 text-2xl font-extrabold text-barz-ink"><?= (int) $inactiveBarbers ?></p></div>
    </div>

    <section class="overflow-hidden rounded-2xl border border-barz-line bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-barz-line p-5 sm:flex-row sm:items-center sm:justify-between">
            <div><h2 class="font-bold text-barz-ink">Daftar barber</h2><p class="mt-1 text-xs text-stone-500">Data yang sama digunakan saat pelanggan memilih barber ketika booking.</p></div>
            <form method="GET" action="barbers.php" class="flex gap-2">
                <input type="search" name="search" value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>" placeholder="Cari nama/spesialisasi" class="min-w-0 rounded-xl border border-stone-200 px-3 py-2 text-sm outline-none focus:border-barz-accent">
                <button class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-100">Cari</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left">
                <thead class="bg-barz-bg text-xs uppercase tracking-wide text-stone-500">
                    <tr><th class="px-5 py-3">Barber</th><th class="px-5 py-3">Spesialisasi</th><th class="px-5 py-3">Pengalaman</th><th class="px-5 py-3">Status</th><th class="px-5 py-3 text-right">Aksi</th></tr>
                </thead>
                <tbody class="divide-y divide-barz-line">
                    <?php if (!$barbers): ?>
                        <tr><td colspan="5" class="px-5 py-12 text-center text-sm text-stone-500">Belum ada barber yang cocok dengan pencarian.</td></tr>
                    <?php else: ?>
                        <?php foreach ($barbers as $barber): ?>
                            <tr class="hover:bg-barz-bg/60">
                                <td class="px-5 py-4"><p class="font-bold text-barz-ink"><?= htmlspecialchars($barber['name'], ENT_QUOTES, 'UTF-8') ?></p><p class="mt-0.5 text-xs text-stone-500">ID #<?= (int) $barber['id'] ?></p></td>
                                <td class="px-5 py-4 text-sm text-stone-600"><?= htmlspecialchars($barber['specialization'] ?: 'Belum ditentukan', ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-5 py-4 text-sm text-stone-600"><?= (int) $barber['experience_years'] ?> tahun</td>
                                <td class="px-5 py-4">
                                    <?php if ((int) $barber['is_active'] === 1): ?>
                                        <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800">Aktif</span>
                                    <?php else: ?>
                                        <span class="rounded-full border border-stone-200 bg-stone-100 px-2.5 py-1 text-xs font-semibold text-stone-600">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4"><div class="flex justify-end gap-2">
                                    <a href="barbers.php?edit_barber=<?= (int) $barber['id'] ?>" class="rounded-lg border border-stone-200 px-3 py-2 text-xs font-bold text-stone-700 no-underline hover:bg-stone-50">Edit</a>
                                    <form method="POST" action="barbers.php">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="action" value="toggle">
                                        <input type="hidden" name="barber_id" value="<?= (int) $barber['id'] ?>">
                                        <button class="rounded-lg border px-3 py-2 text-xs font-bold <?= (int) $barber['is_active'] === 1 ? 'border-amber-200 bg-amber-50 text-amber-800' : 'border-emerald-200 bg-emerald-50 text-emerald-800' ?>"><?= (int) $barber['is_active'] === 1 ? 'Nonaktifkan' : 'Aktifkan' ?></button>
                                    </form>
                                    <form method="POST" action="barbers.php" onsubmit="return confirm('Hapus barber ini? Jika sudah digunakan oleh jadwal atau booking, barber akan dinonaktifkan agar data lama tetap aman.');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="barber_id" value="<?= (int) $barber['id'] ?>">
                                        <button class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700">Hapus</button>
                                    </form>
                                </div></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="border-t border-barz-line bg-barz-bg px-5 py-3 text-xs text-stone-500">Barber yang sudah digunakan pada booking, jadwal, atau slot akan dinonaktifkan, bukan dihapus.</div>
    </section>
</main>

<dialog id="barberModal" class="m-auto w-[calc(100%-2rem)] max-w-xl rounded-2xl border border-barz-line bg-white p-0 text-barz-ink shadow-2xl backdrop:bg-[rgba(20,30,15,.58)]">
    <form method="POST" action="barbers.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="action" value="<?= $editingBarber ? 'update' : 'add' ?>">
        <?php if ($editingBarber): ?><input type="hidden" name="barber_id" value="<?= (int) $editingBarber['id'] ?>"><?php endif; ?>
        <div class="flex items-start justify-between border-b border-barz-line px-6 py-5"><div><h2 class="text-xl font-extrabold"><?= $editingBarber ? 'Edit barber' : 'Tambah barber' ?></h2><p class="mt-1 text-sm text-stone-500">Lengkapi informasi barber Barz.</p></div><button type="button" onclick="this.closest('dialog').close()" class="text-2xl text-stone-500" aria-label="Tutup">&times;</button></div>
        <div class="space-y-4 px-6 py-5">
            <div><label for="barberName" class="mb-1.5 block text-sm font-semibold">Nama</label><input id="barberName" type="text" name="name" maxlength="100" required value="<?= htmlspecialchars($editingBarber['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-xl border border-stone-200 px-3.5 py-3 text-sm outline-none focus:border-barz-accent"></div>
            <div><label for="barberSpecialization" class="mb-1.5 block text-sm font-semibold">Spesialisasi</label><input id="barberSpecialization" type="text" name="specialization" maxlength="255" value="<?= htmlspecialchars($editingBarber['specialization'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-xl border border-stone-200 px-3.5 py-3 text-sm outline-none focus:border-barz-accent" placeholder="Contoh: Classic haircut, fade"></div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div><label for="barberExperience" class="mb-1.5 block text-sm font-semibold">Pengalaman (tahun)</label><input id="barberExperience" type="number" name="experience_years" min="0" max="60" required value="<?= (int) ($editingBarber['experience_years'] ?? 0) ?>" class="w-full rounded-xl border border-stone-200 px-3.5 py-3 text-sm outline-none focus:border-barz-accent"></div>
                <div><label for="barberActive" class="mb-1.5 block text-sm font-semibold">Status</label><select id="barberActive" name="is_active" class="w-full rounded-xl border border-stone-200 bg-white px-3.5 py-3 text-sm outline-none focus:border-barz-accent"><option value="1" <?= !$editingBarber || (int) $editingBarber['is_active'] === 1 ? 'selected' : '' ?>>Aktif</option><option value="0" <?= $editingBarber && (int) $editingBarber['is_active'] === 0 ? 'selected' : '' ?>>Nonaktif</option></select></div>
            </div>
        </div>
        <div class="flex flex-col-reverse gap-2 border-t border-barz-line bg-barz-bg px-6 py-4 sm:flex-row sm:justify-end"><a href="barbers.php" class="rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-center text-sm font-semibold text-stone-700 no-underline">Batal</a><button class="rounded-xl bg-barz-accent px-5 py-2.5 text-sm font-bold text-white"><?= $editingBarber ? 'Simpan perubahan' : 'Simpan barber' ?></button></div>
    </form>
</dialog>
<?php if ($editingBarber): ?>
<script>document.addEventListener('DOMContentLoaded', function () { document.getElementById('barberModal').showModal(); });</script>
<?php endif; ?>
