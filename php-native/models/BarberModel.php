<?php
/**
 * Model barber Barz (Week 6 - MVC Manual).
 * Class ini menjadi satu-satunya tempat query SQL untuk fitur Admin > Barbers.
 */
class BarberModel
{
    /** @var mysqli */
    private $db;

    /** Constructor menerima koneksi MySQLi dari bootstrap Barz. */
    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    /** Mengambil seluruh barber, atau memfilter nama/spesialisasi. */
    public function getAll(string $keyword = ''): array
    {
        $keyword = trim($keyword);
        if ($keyword === '') {
            $result = $this->db->query(
                'SELECT id, name, specialization, experience_years, is_active
                 FROM barbers ORDER BY is_active DESC, name ASC'
            );
            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        }

        $statement = $this->db->prepare(
            'SELECT id, name, specialization, experience_years, is_active
             FROM barbers
             WHERE name LIKE ? OR COALESCE(specialization, \'\') LIKE ?
             ORDER BY is_active DESC, name ASC'
        );
        if (!$statement) {
            throw new RuntimeException('Query barber tidak dapat disiapkan.');
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
            'SELECT id, name, specialization, experience_years, is_active
             FROM barbers WHERE id = ? LIMIT 1'
        );
        if (!$statement) {
            throw new RuntimeException('Query barber tidak dapat disiapkan.');
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
            'INSERT INTO barbers (name, specialization, experience_years, is_active)
             VALUES (?, ?, ?, ?)'
        );
        if (!$statement) {
            throw new RuntimeException('Query tambah barber tidak dapat disiapkan.');
        }
        $name = $data['name'];
        $specialization = $data['specialization'];
        $experience = (int) $data['experience_years'];
        $isActive = (int) $data['is_active'];
        $statement->bind_param('ssii', $name, $specialization, $experience, $isActive);
        $statement->execute();
        $newId = (int) $this->db->insert_id;
        $statement->close();
        return $newId;
    }

    public function update(int $id, array $data): bool
    {
        $statement = $this->db->prepare(
            'UPDATE barbers
             SET name = ?, specialization = ?, experience_years = ?, is_active = ?
             WHERE id = ?'
        );
        if (!$statement) {
            throw new RuntimeException('Query edit barber tidak dapat disiapkan.');
        }
        $name = $data['name'];
        $specialization = $data['specialization'];
        $experience = (int) $data['experience_years'];
        $isActive = (int) $data['is_active'];
        $statement->bind_param('ssiii', $name, $specialization, $experience, $isActive, $id);
        $success = $statement->execute();
        $statement->close();
        return $success;
    }

    public function setActive(int $id, bool $active): bool
    {
        $activeValue = $active ? 1 : 0;
        $statement = $this->db->prepare('UPDATE barbers SET is_active = ? WHERE id = ?');
        if (!$statement) {
            throw new RuntimeException('Query status barber tidak dapat disiapkan.');
        }
        $statement->bind_param('ii', $activeValue, $id);
        $success = $statement->execute();
        $statement->close();
        return $success;
    }

    /**
     * Hapus barber hanya jika belum digunakan booking, jadwal, atau slot.
     * Jika sudah terkait data lain, nonaktifkan supaya histori dan relasi tetap aman.
     */
    public function deleteSafely(int $id): string
    {
        $statement = $this->db->prepare(
            'SELECT
                (SELECT COUNT(*) FROM bookings WHERE barber_id = ?) AS booking_count,
                (SELECT COUNT(*) FROM schedules WHERE barber_id = ?) AS schedule_count,
                (SELECT COUNT(*) FROM time_slots WHERE barber_id = ?) AS slot_count'
        );
        if (!$statement) {
            throw new RuntimeException('Pemeriksaan relasi barber gagal disiapkan.');
        }
        $statement->bind_param('iii', $id, $id, $id);
        $statement->execute();
        $usage = $statement->get_result()->fetch_assoc();
        $statement->close();

        $isUsed = ((int) ($usage['booking_count'] ?? 0) > 0)
            || ((int) ($usage['schedule_count'] ?? 0) > 0)
            || ((int) ($usage['slot_count'] ?? 0) > 0);

        if ($isUsed) {
            $this->setActive($id, false);
            return 'deactivated';
        }

        $delete = $this->db->prepare('DELETE FROM barbers WHERE id = ?');
        if (!$delete) {
            throw new RuntimeException('Query hapus barber tidak dapat disiapkan.');
        }
        $delete->bind_param('i', $id);
        $delete->execute();
        $deleted = $delete->affected_rows > 0;
        $delete->close();
        return $deleted ? 'deleted' : 'not_found';
    }
}
