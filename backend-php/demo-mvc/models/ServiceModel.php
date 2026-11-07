<?php

declare(strict_types=1);

namespace Barz\ManualMvc\Models;

use mysqli;

/**
 * Model untuk membaca dan mengelola data layanan dari tabel services.
 * Class ini menggunakan MySQLi Native tanpa framework atau ORM.
 */
final class ServiceModel
{
    private mysqli $connection;

    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    /** @return array<int, array<string, mixed>> */
    public function getAll(string $keyword = ''): array
    {
        $keyword = trim($keyword);
        $statement = $this->connection->prepare(
            'SELECT id, name, description, price, duration, is_active
             FROM services
             WHERE name LIKE ? OR description LIKE ?
             ORDER BY is_active DESC, name ASC'
        );

        $search = '%' . $keyword . '%';
        $statement->bind_param('ss', $search, $search);
        $statement->execute();
        $services = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
        $statement->close();

        return $services;
    }

    /** @return array<string, mixed>|null */
    public function findById(int $id): ?array
    {
        $statement = $this->connection->prepare(
            'SELECT id, name, description, price, duration, is_active
             FROM services WHERE id = ? LIMIT 1'
        );
        $statement->bind_param('i', $id);
        $statement->execute();
        $service = $statement->get_result()->fetch_assoc() ?: null;
        $statement->close();

        return $service;
    }

    public function create(array $data): int
    {
        $statement = $this->connection->prepare(
            'INSERT INTO services (name, description, price, duration, is_active)
             VALUES (?, ?, ?, ?, ?)'
        );
        $name = $data['name'];
        $description = $data['description'];
        $price = $data['price'];
        $duration = $data['duration'];
        $isActive = $data['is_active'];

        $statement->bind_param('ssdii', $name, $description, $price, $duration, $isActive);
        $statement->execute();
        $newId = (int) $this->connection->insert_id;
        $statement->close();

        return $newId;
    }

    public function update(int $id, array $data): bool
    {
        $statement = $this->connection->prepare(
            'UPDATE services
             SET name = ?, description = ?, price = ?, duration = ?, is_active = ?
             WHERE id = ?'
        );
        $name = $data['name'];
        $description = $data['description'];
        $price = $data['price'];
        $duration = $data['duration'];
        $isActive = $data['is_active'];

        $statement->bind_param('ssdiii', $name, $description, $price, $duration, $isActive, $id);
        $success = $statement->execute();
        $statement->close();

        return $success;
    }

    /**
     * Hapus permanen bila belum pernah dipakai booking. Jika sudah dipakai,
     * nonaktifkan agar histori dan foreign key booking tetap aman.
     *
     * @return 'deleted'|'deactivated'
     */
    public function deleteSafely(int $id): string
    {
        $check = $this->connection->prepare(
            'SELECT COUNT(*) AS total FROM bookings WHERE service_id = ?'
        );
        $check->bind_param('i', $id);
        $check->execute();
        $result = $check->get_result()->fetch_assoc();
        $check->close();

        if ((int) ($result['total'] ?? 0) > 0) {
            $this->setActive($id, false);
            return 'deactivated';
        }

        $statement = $this->connection->prepare('DELETE FROM services WHERE id = ?');
        $statement->bind_param('i', $id);
        $statement->execute();
        $statement->close();

        return 'deleted';
    }

    public function setActive(int $id, bool $isActive): bool
    {
        $activeValue = $isActive ? 1 : 0;
        $statement = $this->connection->prepare(
            'UPDATE services SET is_active = ? WHERE id = ?'
        );
        $statement->bind_param('ii', $activeValue, $id);
        $success = $statement->execute();
        $statement->close();

        return $success;
    }
}
