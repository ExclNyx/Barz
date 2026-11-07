<?php

declare(strict_types=1);

namespace Barz\ManualMvc\Controllers;

use Barz\ManualMvc\Models\ServiceModel;
use Throwable;

/**
 * Controller mengatur request, validasi input, pemanggilan Model, dan pemilihan View.
 */
final class ServiceController
{
    private ServiceModel $serviceModel;
    private string $viewDirectory;

    public function __construct(ServiceModel $serviceModel)
    {
        $this->serviceModel = $serviceModel;
        $this->viewDirectory = dirname(__DIR__) . '/views';
    }

    public function handle(): void
    {
        $action = (string) ($_GET['action'] ?? 'index');

        switch ($action) {
            case 'edit':
                $this->edit();
                return;
            case 'save':
                $this->save();
                return;
            case 'toggle':
                $this->toggleActive();
                return;
            case 'delete':
                $this->delete();
                return;
            case 'index':
            default:
                $this->index();
        }
    }

    private function index(): void
    {
        $keyword = trim((string) ($_GET['q'] ?? ''));
        $this->render('services/index.php', [
            'pageTitle' => 'Demo MVC Manual - Kelola Layanan',
            'services' => $this->serviceModel->getAll($keyword),
            'keyword' => $keyword,
            'editingService' => null,
            'flash' => $this->pullFlash(),
            'csrfToken' => \generateCSRFToken(),
            'formError' => '',
        ]);
    }

    private function edit(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $service = $id ? $this->serviceModel->findById((int) $id) : null;

        if (!$service) {
            $this->flash('error', 'Layanan yang ingin diedit tidak ditemukan.');
            $this->redirect();
        }

        $this->render('services/index.php', [
            'pageTitle' => 'Edit Layanan - Demo MVC Manual',
            'services' => $this->serviceModel->getAll(),
            'keyword' => '',
            'editingService' => $service,
            'flash' => $this->pullFlash(),
            'csrfToken' => \generateCSRFToken(),
            'formError' => '',
        ]);
    }

    private function save(): void
    {
        $this->requirePost();
        $this->verifyCsrf();

        $rawId = trim((string) ($_POST['id'] ?? ''));
        $id = $rawId === '' ? null : filter_var($rawId, FILTER_VALIDATE_INT);
        $name = trim((string) ($_POST['name'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $priceInput = trim((string) ($_POST['price'] ?? ''));
        $durationInput = trim((string) ($_POST['duration'] ?? ''));
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        $errors = [];
        if ($name === '' || (function_exists('mb_strlen') ? mb_strlen($name) : strlen($name)) > 100) {
            $errors[] = 'Nama layanan wajib diisi dan maksimal 100 karakter.';
        }
        if (!is_numeric($priceInput) || (float) $priceInput < 0 || (float) $priceInput > 99999999.99) {
            $errors[] = 'Harga harus berupa angka antara 0 sampai 99.999.999,99.';
        }
        if (filter_var($durationInput, FILTER_VALIDATE_INT) === false || (int) $durationInput < 5 || (int) $durationInput > 480) {
            $errors[] = 'Durasi harus berupa bilangan bulat antara 5 sampai 480 menit.';
        }
        if ($rawId !== '' && ($id === false || $id < 1)) {
            $errors[] = 'ID layanan tidak valid.';
        } elseif ($id !== null && !$this->serviceModel->findById((int) $id)) {
            $errors[] = 'Data layanan yang akan diperbarui tidak ditemukan.';
        }

        if ($errors) {
            $this->flash('error', implode(' ', $errors));
            $this->redirect();
        }

        $data = [
            'name' => $name,
            'description' => $description,
            'price' => (float) $priceInput,
            'duration' => (int) $durationInput,
            'is_active' => $isActive,
        ];

        try {
            if ($id !== null) {
                $this->serviceModel->update((int) $id, $data);
                $message = 'Layanan berhasil diperbarui.';
            } else {
                $this->serviceModel->create($data);
                $message = 'Layanan baru berhasil ditambahkan.';
            }

            $this->flash('success', $message);
        } catch (Throwable $exception) {
            error_log('[Barz Manual MVC] ' . $exception->getMessage());
            $this->flash('error', 'Perubahan layanan gagal disimpan. Periksa koneksi database.');
        }

        $this->redirect();
    }

    private function delete(): void
    {
        $this->requirePost();
        $this->verifyCsrf();

        $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
        $service = $id ? $this->serviceModel->findById((int) $id) : null;

        if (!$service) {
            $this->flash('error', 'Layanan tidak ditemukan.');
            $this->redirect();
        }

        try {
            $result = $this->serviceModel->deleteSafely((int) $id);
            $message = $result === 'deleted'
                ? 'Layanan berhasil dihapus.'
                : 'Layanan sudah memiliki riwayat booking, jadi dinonaktifkan agar histori tetap aman.';
            $this->flash('success', $message);
        } catch (Throwable $exception) {
            error_log('[Barz Manual MVC] ' . $exception->getMessage());
            $this->flash('error', 'Layanan gagal dihapus. Periksa relasi database.');
        }

        $this->redirect();
    }

    private function toggleActive(): void
    {
        $this->requirePost();
        $this->verifyCsrf();

        $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
        $service = $id ? $this->serviceModel->findById((int) $id) : null;

        if (!$service) {
            $this->flash('error', 'Layanan tidak ditemukan.');
            $this->redirect();
        }

        try {
            $newStatus = !(bool) $service['is_active'];
            $this->serviceModel->setActive((int) $id, $newStatus);
            $this->flash('success', $newStatus ? 'Layanan diaktifkan.' : 'Layanan dinonaktifkan.');
        } catch (Throwable $exception) {
            error_log('[Barz Manual MVC] ' . $exception->getMessage());
            $this->flash('error', 'Status layanan gagal diperbarui.');
        }

        $this->redirect();
    }

    private function render(string $view, array $data): void
    {
        extract($data, EXTR_SKIP);
        require $this->viewDirectory . '/' . $view;
    }

    private function requirePost(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            header('Allow: POST');
            exit('Method Not Allowed');
        }
    }

    private function verifyCsrf(): void
    {
        $token = (string) ($_POST['csrf_token'] ?? '');
        if (!\verifyCSRFToken($token)) {
            http_response_code(419);
            exit('Sesi formulir telah kedaluwarsa. Muat ulang halaman lalu coba lagi.');
        }
    }

    private function flash(string $type, string $message): void
    {
        $_SESSION['manual_mvc_flash'] = ['type' => $type, 'message' => $message];
    }

    private function pullFlash(): ?array
    {
        $flash = $_SESSION['manual_mvc_flash'] ?? null;
        unset($_SESSION['manual_mvc_flash']);
        return is_array($flash) ? $flash : null;
    }

    private function redirect(): void
    {
        header('Location: /Barz/backend-php/demo-mvc/public/index.php');
        exit;
    }
}
