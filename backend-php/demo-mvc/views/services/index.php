<?php
/** @var array<int, array<string, mixed>> $services */
/** @var array<string, mixed>|null $editingService */
/** @var array<string, string>|null $flash */
$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$formService = $editingService ?? [
    'id' => '',
    'name' => '',
    'description' => '',
    'price' => '',
    'duration' => 30,
    'is_active' => 1,
];
$isEditing = $editingService !== null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="barz-api-base-url" content="http://127.0.0.1:8000/api">
    <title><?= $escape($pageTitle) ?></title>
    <link rel="stylesheet" href="/Barz/assets/css/tailwind.css">
</head>
<body class="min-h-screen bg-barz-bg text-barz-ink antialiased">
    <header class="border-b border-barz-line bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-barz-accent">Barz Barbershop</p>
                <h1 class="mt-1 text-xl font-bold">Demo OOP + MVC Manual</h1>
            </div>
            <a href="/Barz/admin/index.php" class="rounded-xl border border-barz-line px-4 py-2 text-sm font-semibold text-barz-ink no-underline hover:bg-barz-bg">Kembali ke Admin</a>
        </div>
    </header>

    <main class="mx-auto grid max-w-7xl gap-6 px-5 py-8 lg:grid-cols-[0.9fr_1.5fr]">
        <section class="h-fit rounded-2xl border border-barz-line bg-white p-6 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-barz-accent"><?= $isEditing ? 'Update data' : 'Create data' ?></p>
            <h2 class="mt-2 text-xl font-bold"><?= $isEditing ? 'Edit Layanan' : 'Tambah Layanan' ?></h2>
            <p class="mt-2 text-sm leading-6 text-barz-muted">Form ini diproses oleh Controller, disimpan melalui Model, lalu ditampilkan kembali oleh View.</p>

            <?php if ($flash): ?>
                <div class="mt-5 rounded-xl border px-4 py-3 text-sm <?= ($flash['type'] ?? '') === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-rose-200 bg-rose-50 text-rose-800' ?>" role="status">
                    <?= $escape($flash['message'] ?? '') ?>
                </div>
            <?php endif; ?>

            <form method="post" action="index.php?action=save" class="mt-5 space-y-4">
                <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
                <input type="hidden" name="id" value="<?= $escape($formService['id']) ?>">

                <label class="block">
                    <span class="mb-1.5 block text-sm font-semibold">Nama layanan</span>
                    <input name="name" type="text" maxlength="100" required value="<?= $escape($formService['name']) ?>" class="w-full rounded-xl border border-barz-line bg-white px-3.5 py-3 text-sm outline-none focus:border-barz-accent">
                </label>

                <label class="block">
                    <span class="mb-1.5 block text-sm font-semibold">Deskripsi</span>
                    <textarea name="description" rows="3" class="w-full rounded-xl border border-barz-line bg-white px-3.5 py-3 text-sm outline-none focus:border-barz-accent"><?= $escape($formService['description']) ?></textarea>
                </label>

                <div class="grid grid-cols-2 gap-3">
                    <label class="block">
                        <span class="mb-1.5 block text-sm font-semibold">Harga (Rp)</span>
                        <input name="price" type="number" min="0" step="500" required value="<?= $escape($formService['price']) ?>" class="w-full rounded-xl border border-barz-line bg-white px-3.5 py-3 text-sm outline-none focus:border-barz-accent">
                    </label>
                    <label class="block">
                        <span class="mb-1.5 block text-sm font-semibold">Durasi (menit)</span>
                        <input name="duration" type="number" min="5" max="480" step="5" required value="<?= $escape($formService['duration']) ?>" class="w-full rounded-xl border border-barz-line bg-white px-3.5 py-3 text-sm outline-none focus:border-barz-accent">
                    </label>
                </div>

                <label class="flex items-center gap-2 text-sm font-medium">
                    <input type="checkbox" name="is_active" value="1" <?= (bool) $formService['is_active'] ? 'checked' : '' ?> class="h-4 w-4 accent-barz-accent">
                    Aktif dan dapat dipilih customer
                </label>

                <div class="flex flex-wrap gap-2 pt-2">
                    <button type="submit" class="rounded-xl bg-barz-accent px-5 py-3 text-sm font-bold text-white hover:bg-barz-accent-hover">
                        <?= $isEditing ? 'Simpan Perubahan' : 'Tambah Layanan' ?>
                    </button>
                    <?php if ($isEditing): ?>
                        <a href="index.php" class="rounded-xl border border-barz-line px-5 py-3 text-sm font-semibold text-barz-ink no-underline">Batal</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <section class="rounded-2xl border border-barz-line bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-barz-accent">Read data</p>
                    <h2 class="mt-2 text-xl font-bold">Daftar Layanan</h2>
                    <p class="mt-1 text-sm text-barz-muted">Data diambil langsung dari tabel <code>services</code> pada database barz_booking.</p>
                </div>
                <form method="get" class="flex w-full gap-2 sm:w-auto">
                    <input type="text" name="q" value="<?= $escape($keyword) ?>" placeholder="Cari layanan..." class="min-w-0 flex-1 rounded-xl border border-barz-line px-3 py-2.5 text-sm outline-none focus:border-barz-accent sm:w-52">
                    <button class="rounded-xl border border-barz-line px-4 py-2.5 text-sm font-semibold hover:bg-barz-bg">Cari</button>
                </form>
            </div>

            <div class="mt-5 overflow-x-auto">
                <table class="w-full min-w-[620px] border-collapse text-left text-sm">
                    <thead>
                        <tr class="border-b border-barz-line text-xs uppercase tracking-wide text-barz-muted">
                            <th class="px-3 py-3">Layanan</th>
                            <th class="px-3 py-3">Harga</th>
                            <th class="px-3 py-3">Durasi</th>
                            <th class="px-3 py-3">Status</th>
                            <th class="px-3 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$services): ?>
                            <tr><td colspan="5" class="px-3 py-8 text-center text-barz-muted">Belum ada layanan yang cocok dengan pencarian.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($services as $service): ?>
                            <tr class="border-b border-barz-line/70 align-top last:border-0">
                                <td class="px-3 py-4">
                                    <p class="font-bold"><?= $escape($service['name']) ?></p>
                                    <p class="mt-1 max-w-sm text-xs leading-5 text-barz-muted"><?= $escape($service['description']) ?></p>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4">Rp <?= number_format((float) $service['price'], 0, ',', '.') ?></td>
                                <td class="whitespace-nowrap px-3 py-4"><?= (int) $service['duration'] ?> menit</td>
                                <td class="px-3 py-4">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-bold <?= (bool) $service['is_active'] ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-100 text-slate-600' ?>">
                                        <?= (bool) $service['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                    </span>
                                </td>
                                <td class="px-3 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="index.php?action=edit&amp;id=<?= (int) $service['id'] ?>" class="rounded-lg border border-barz-line px-3 py-1.5 text-xs font-semibold text-barz-ink no-underline hover:bg-barz-bg">Edit</a>
                                        <form method="post" action="index.php?action=toggle" onsubmit="return confirm('Ubah status layanan ini?')">
                                            <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
                                            <input type="hidden" name="id" value="<?= (int) $service['id'] ?>">
                                            <button class="rounded-lg border border-barz-line px-3 py-1.5 text-xs font-semibold hover:bg-barz-bg" type="submit">
                                                <?= (bool) $service['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                                            </button>
                                        </form>
                                        <form method="post" action="index.php?action=delete" onsubmit="return confirm('Hapus layanan ini? Jika sudah memiliki booking, layanan hanya akan dinonaktifkan.')">
                                            <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
                                            <input type="hidden" name="id" value="<?= (int) $service['id'] ?>">
                                            <button class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50" type="submit">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-2xl border border-barz-line bg-barz-surface p-5 lg:col-span-2">
            <h2 class="font-bold">Alur MVC Manual pada halaman ini</h2>
            <div class="mt-3 grid gap-3 md:grid-cols-3">
                <div class="rounded-xl bg-white p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-barz-accent">Model</p>
                    <p class="mt-1 font-semibold">models/ServiceModel.php</p>
                    <p class="mt-1 text-sm text-barz-muted">Menjalankan query SELECT, INSERT, dan UPDATE ke MySQLi.</p>
                </div>
                <div class="rounded-xl bg-white p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-barz-accent">Controller</p>
                    <p class="mt-1 font-semibold">controllers/ServiceController.php</p>
                    <p class="mt-1 text-sm text-barz-muted">Menerima request, memvalidasi input, memanggil Model, lalu memilih View.</p>
                </div>
                <div class="rounded-xl bg-white p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-barz-accent">View</p>
                    <p class="mt-1 font-semibold">views/services/index.php</p>
                    <p class="mt-1 text-sm text-barz-muted">Menampilkan form dan tabel layanan menggunakan HTML serta Tailwind CSS.</p>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
