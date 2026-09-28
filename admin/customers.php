<?php
require_once __DIR__ . '/../includes/init.php';
requireAdmin();

$pageTitle = 'Customers - Admin';
$db = getDB();

// Get all customers
$customers = $db->query("
    SELECT id, name, email, phone, role, created_at, updated_at
    FROM users WHERE role = 'customer'
    ORDER BY created_at DESC
")->fetch_all(MYSQLI_ASSOC);

ob_start();
?>

<div class="page">
    <div class="page-top">
        <h1>Data Customer</h1>
    </div>

    <div class="card">
        <div class="card-b">
            <div class="twrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Bergabung</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $cust): ?>
                        <tr>
                            <td>#<?php echo $cust['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($cust['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($cust['email']); ?></td>
                            <td><?php echo htmlspecialchars($cust['phone']); ?></td>
                            <td><?php echo formatDate($cust['created_at']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
