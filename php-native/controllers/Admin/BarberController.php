<?php
/**
 * Controller Admin > Barbers (Week 6 - MVC Manual).
 * Memproses request dan validasi; query database tetap menjadi tugas BarberModel.
 */
class AdminBarberController
{
    /** @var BarberModel */
    private $barberModel;

    public function __construct(BarberModel $barberModel)
    {
        $this->barberModel = $barberModel;
    }

    public function handle(): array
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $this->handlePost();
        }

        $keyword = trim((string) ($_GET['search'] ?? ''));
        $barbers = $this->barberModel->getAll($keyword);
        $editingBarber = null;
        $editId = filter_var($_GET['edit_barber'] ?? '', FILTER_VALIDATE_INT);
        if ($editId && $editId > 0) {
            $editingBarber = $this->barberModel->findById((int) $editId);
            if (!$editingBarber) {
                $_SESSION['error'] = 'Barber yang ingin diedit tidak ditemukan.';
            }
        }

        $activeCount = 0;
        foreach ($barbers as $barber) {
            if ((int) $barber['is_active'] === 1) {
                $activeCount++;
            }
        }

        $data = [
            'barbers' => $barbers,
            'keyword' => $keyword,
            'editingBarber' => $editingBarber,
            'totalBarbers' => count($barbers),
            'activeBarbers' => $activeCount,
            'inactiveBarbers' => count($barbers) - $activeCount,
            'csrfToken' => generateCSRFToken(),
            'successMessage' => $_SESSION['success'] ?? null,
            'errorMessage' => $_SESSION['error'] ?? null,
        ];
        unset($_SESSION['success'], $_SESSION['error']);
        return $data;
    }

    private function handlePost(): void
    {
        if (!verifyCSRFToken((string) ($_POST['csrf_token'] ?? ''))) {
            http_response_code(419);
            exit('Sesi formulir kedaluwarsa. Muat ulang halaman dan coba lagi.');
        }

        $action = (string) ($_POST['action'] ?? '');
        try {
            if ($action === 'add' || $action === 'update') {
                $this->saveBarber($action);
            } elseif ($action === 'toggle') {
                $this->toggleBarber();
            } elseif ($action === 'delete') {
                $this->deleteBarber();
            } else {
                $_SESSION['error'] = 'Aksi barber tidak dikenali.';
            }
        } catch (Throwable $exception) {
            error_log('[Barz Admin Barber MVC] ' . $exception->getMessage());
            $_SESSION['error'] = 'Proses barber gagal. Periksa koneksi database dan data yang dikirim.';
        }

        header('Location: barbers.php');
        exit;
    }

    private function saveBarber(string $action): void
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        $specialization = trim((string) ($_POST['specialization'] ?? ''));
        $experience = filter_var($_POST['experience_years'] ?? 0, FILTER_VALIDATE_INT);
        $id = filter_var($_POST['barber_id'] ?? '', FILTER_VALIDATE_INT);
        $activeValue = (string) ($_POST['is_active'] ?? '1');

        if ($name === '' || strlen($name) > 100 || strlen($specialization) > 255
            || $experience === false || $experience < 0 || $experience > 60
            || !in_array($activeValue, ['0', '1'], true)) {
            $_SESSION['error'] = 'Nama, spesialisasi, pengalaman, atau status barber tidak valid.';
            return;
        }

        $data = [
            'name' => $name,
            'specialization' => $specialization,
            'experience_years' => (int) $experience,
            'is_active' => (int) $activeValue,
        ];

        if ($action === 'update') {
            if (!$id || $id < 1 || !$this->barberModel->findById((int) $id)) {
                $_SESSION['error'] = 'Data barber yang akan diperbarui tidak ditemukan.';
                return;
            }
            $this->barberModel->update((int) $id, $data);
            $_SESSION['success'] = 'Data barber berhasil diperbarui.';
            return;
        }

        $this->barberModel->create($data);
        $_SESSION['success'] = 'Barber berhasil ditambahkan.';
    }

    private function toggleBarber(): void
    {
        $id = filter_var($_POST['barber_id'] ?? '', FILTER_VALIDATE_INT);
        $barber = $id ? $this->barberModel->findById((int) $id) : null;
        if (!$barber) {
            $_SESSION['error'] = 'Barber tidak ditemukan.';
            return;
        }
        $newStatus = (int) $barber['is_active'] !== 1;
        $this->barberModel->setActive((int) $id, $newStatus);
        $_SESSION['success'] = $newStatus ? 'Barber berhasil diaktifkan.' : 'Barber berhasil dinonaktifkan.';
    }

    private function deleteBarber(): void
    {
        $id = filter_var($_POST['barber_id'] ?? '', FILTER_VALIDATE_INT);
        if (!$id || $id < 1 || !$this->barberModel->findById((int) $id)) {
            $_SESSION['error'] = 'Barber tidak ditemukan.';
            return;
        }
        $result = $this->barberModel->deleteSafely((int) $id);
        if ($result === 'deleted') {
            $_SESSION['success'] = 'Barber berhasil dihapus.';
        } elseif ($result === 'deactivated') {
            $_SESSION['success'] = 'Barber sudah memiliki jadwal atau riwayat booking, jadi dinonaktifkan agar data tetap aman.';
        } else {
            $_SESSION['error'] = 'Barber tidak ditemukan.';
        }
    }
}
