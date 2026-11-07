<?php
/**
 * Controller fitur Admin > Layanan (Week 6 - MVC Manual).
 * Bertugas menangani request, validasi, flash message, dan menyiapkan data untuk View. Tidak menjalankan query SQL.
 */
class AdminServiceController
{
    private $serviceModel;

    public function __construct(ServiceModel $serviceModel)
    {
        $this->serviceModel = $serviceModel;
    }

    public function handle(): array
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $this->handlePost();
        }

        $keyword = trim($_GET['search'] ?? '');
        $services = $this->serviceModel->getAll($keyword);
        $editingService = null;
        $editId = filter_var($_GET['edit_service'] ?? '', FILTER_VALIDATE_INT);
        if ($editId && $editId > 0) {
            $editingService = $this->serviceModel->findById((int) $editId);
            if (!$editingService) {
                $_SESSION['error'] = 'Layanan yang ingin diedit tidak ditemukan.';
            }
        }

        $totalServices = count($services);
        $activeServices = 0;
        foreach ($services as $service) {
            if ((int) $service['is_active'] === 1) {
                $activeServices++;
            }
        }

        $data = [
            'services' => $services,
            'totalServices' => $totalServices,
            'activeServices' => $activeServices,
            'inactiveServices' => $totalServices - $activeServices,
            'editingService' => $editingService,
            'keyword' => $keyword,
            'csrfToken' => generateCSRFToken(),
            'successMessage' => $_SESSION['success'] ?? null,
            'errorMessage' => $_SESSION['error'] ?? null,
        ];
        unset($_SESSION['success'], $_SESSION['error']);
        return $data;
    }

    private function handlePost(): void
    {
        $token = (string) ($_POST['csrf_token'] ?? '');
        if (!verifyCSRFToken($token)) {
            http_response_code(419);
            exit('Sesi formulir kedaluwarsa. Muat ulang halaman dan coba lagi.');
        }

        $action = (string) ($_POST['action'] ?? '');
        try {
            if ($action === 'add' || $action === 'update') {
                $this->saveService($action);
            } elseif ($action === 'toggle') {
                $this->toggleService();
            } elseif ($action === 'delete') {
                $this->deleteService();
            } else {
                $_SESSION['error'] = 'Aksi layanan tidak dikenali.';
            }
        } catch (Throwable $exception) {
            error_log('[Barz Admin Service MVC] ' . $exception->getMessage());
            $_SESSION['error'] = 'Proses layanan gagal. Periksa koneksi database dan data yang dikirim.';
        }

        header('Location: services.php');
        exit;
    }

    private function saveService(string $action): void
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $price = filter_var($_POST['price'] ?? '', FILTER_VALIDATE_FLOAT);
        $duration = filter_var($_POST['duration'] ?? '', FILTER_VALIDATE_INT);
        $id = filter_var($_POST['service_id'] ?? '', FILTER_VALIDATE_INT);

        if ($name === '' || strlen($name) > 100 || $price === false || $price <= 0 ||
            $duration === false || $duration < 1 || $duration > 480) {
            $_SESSION['error'] = 'Nama, harga, dan durasi layanan harus diisi dengan benar.';
            return;
        }

        $data = [
            'name' => $name,
            'description' => $description,
            'price' => (float) $price,
            'duration' => (int) $duration,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];

        if ($action === 'update') {
            if (!$id || $id < 1 || !$this->serviceModel->findById((int) $id)) {
                $_SESSION['error'] = 'Data layanan yang akan diperbarui tidak ditemukan.';
                return;
            }
            $this->serviceModel->update((int) $id, $data);
            $_SESSION['success'] = 'Layanan berhasil diperbarui.';
            return;
        }

        $this->serviceModel->create($data);
        $_SESSION['success'] = 'Layanan berhasil ditambahkan.';
    }

    private function toggleService(): void
    {
        $id = filter_var($_POST['service_id'] ?? '', FILTER_VALIDATE_INT);
        $service = $id ? $this->serviceModel->findById((int) $id) : null;
        if (!$service) {
            $_SESSION['error'] = 'Layanan tidak ditemukan.';
            return;
        }
        $newStatus = (int) $service['is_active'] !== 1;
        $this->serviceModel->setActive((int) $id, $newStatus);
        $_SESSION['success'] = $newStatus ? 'Layanan berhasil diaktifkan.' : 'Layanan berhasil dinonaktifkan.';
    }

    private function deleteService(): void
    {
        $id = filter_var($_POST['service_id'] ?? '', FILTER_VALIDATE_INT);
        if (!$id || $id < 1 || !$this->serviceModel->findById((int) $id)) {
            $_SESSION['error'] = 'Layanan tidak ditemukan.';
            return;
        }

        $result = $this->serviceModel->deleteSafely((int) $id);
        if ($result === 'deleted') {
            $_SESSION['success'] = 'Layanan berhasil dihapus.';
        } elseif ($result === 'deactivated') {
            $_SESSION['success'] = 'Layanan memiliki riwayat booking, jadi dinonaktifkan agar histori tetap aman.';
        } else {
            $_SESSION['error'] = 'Layanan tidak ditemukan.';
        }
    }
}
