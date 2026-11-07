<?php
/**
 * Model layanan Barz (Week 6 - MVC Manual).
 * Seluruh operasi database fitur Admin > Layanan berada di class ini; View tidak menjalankan query SQL.
 * Koneksi menggunakan getDB() dari includes/init.php (MySQLi yang sudah dipakai Barz).
 */
class ServiceModel
{
    private $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    public function getAll(string $keyword = ''): array
    {
        $keyword = trim($keyword);
        if ($keyword === '') {
            $result = $this->db->query(
                'SELECT id, name, description, price, duration, is_active
                 FROM services ORDER BY is_active DESC, name ASC'
            );
            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        }

        $statement = $this->db->prepare(
            'SELECT id, name, description, price, duration, is_active
             FROM services
             WHERE name LIKE ? OR COALESCE(description, \'\') LIKE ?
             ORDER BY is_active DESC, name ASC'
        );
        if (!$statement) {
            throw new RuntimeException('Query layanan tidak dapat disiapkan.');
        }

        $search = '%' . $keyword . '%';
        $statement->bind_param('ss', $search, $search);
        $statement->execute();
        $rows = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
        $statement->close();
        return $rows;
    }

    public function findById(int $id): ?array
    {
        $statement = $this->db->prepare(
            'SELECT id, name, description, price, duration, is_active
             FROM services WHERE id = ? LIMIT 1'
        );
        if (!$statement) {
            throw new RuntimeException('Query layanan tidak dapat disiapkan.');
        }
        $statement->bind_param('i', $id);
        $statement->execute();
        $row = $statement->get_result()->fetch_assoc();
        $statement->close();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO services (name, description, price, duration, is_active)
             VALUES (?, ?, ?, ?, ?)'
        );
        if (!$statement) {
            throw new RuntimeException('Query tambah layanan tidak dapat disiapkan.');
        }

        $name = $data['name'];
        $description = $data['description'];
        $price = (float) $data['price'];
        $duration = (int) $data['duration'];
        $isActive = (int) $data['is_active'];
        $statement->bind_param('ssdii', $name, $description, $price, $duration, $isActive);
        $statement->execute();
        $newId = (int) $this->db->insert_id;
        $statement->close();
        return $newId;
    }

    public function update(int $id, array $data): bool
    {
        $statement = $this->db->prepare(
            'UPDATE services
             SET name = ?, description = ?, price = ?, duration = ?, is_active = ?
             WHERE id = ?'
        );
        if (!$statement) {
            throw new RuntimeException('Query edit layanan tidak dapat disiapkan.');
        }

        $name = $data['name'];
        $description = $data['description'];
        $price = (float) $data['price'];
        $duration = (int) $data['duration'];
        $isActive = (int) $data['is_active'];
        $statement->bind_param('ssdiii', $name, $description, $price, $duration, $isActive, $id);
        $success = $statement->execute();
        $statement->close();
        return $success;
    }

    public function setActive(int $id, bool $active): bool
    {
        $activeValue = $active ? 1 : 0;
        $statement = $this->db->prepare('UPDATE services SET is_active = ? WHERE id = ?');
        if (!$statement) {
            throw new RuntimeException('Query status layanan tidak dapat disiapkan.');
        }
        $statement->bind_param('ii', $activeValue, $id);
        $success = $statement->execute();
        $statement->close();
        return $success;
    }

    /** Hapus permanen bila belum ada booking; jika sudah, nonaktifkan agar histori aman. */
    public function deleteSafely(int $id): string
    {
        $check = $this->db->prepare('SELECT COUNT(*) AS total FROM bookings WHERE service_id = ?');
        if (!$check) {
            throw new RuntimeException('Pemeriksaan riwayat booking gagal disiapkan.');
        }
        $check->bind_param('i', $id);
        $check->execute();
        $row = $check->get_result()->fetch_assoc();
        $check->close();

        if ((int) ($row['total'] ?? 0) > 0) {
            $this->setActive($id, false);
            return 'deactivated';
        }

        $statement = $this->db->prepare('DELETE FROM services WHERE id = ?');
        if (!$statement) {
            throw new RuntimeException('Query hapus layanan tidak dapat disiapkan.');
        }
        $statement->bind_param('i', $id);
        $statement->execute();
        $deleted = $statement->affected_rows > 0;
        $statement->close();
        return $deleted ? 'deleted' : 'not_found';
    }
}
