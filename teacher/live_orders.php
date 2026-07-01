<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

require_teacher();
header('Refresh: 8');
$activityId = selected_activity_id();
$activity = $activityId ? activity_by_id($activityId) : null;
$orders = [];

if ($activity) {
    $stmt = db()->prepare(
        'SELECT o.*, sg.group_name,
            GROUP_CONCAT(CONCAT(oi.product_name_snapshot, " x", oi.quantity) ORDER BY oi.id SEPARATOR ", ") AS items
         FROM orders o
         JOIN student_groups sg ON sg.id = o.group_id
         JOIN order_items oi ON oi.order_id = o.id
         WHERE o.activity_id = ?
         GROUP BY o.id
         ORDER BY o.created_at DESC
         LIMIT 100'
    );
    $stmt->execute([$activity['id']]);
    $orders = $stmt->fetchAll();
}

$pageTitle = 'รายการซื้อแบบสด';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title mb-1">รายการซื้อแบบสด</h1>
        <p class="text-muted mb-0">รีเฟรชอัตโนมัติทุก 8 วินาที</p>
    </div>
    <a class="btn btn-outline-secondary" href="<?= h(url('teacher/dashboard.php')) ?>">กลับ Dashboard</a>
</div>
<div class="panel p-3">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>เวลา</th><th>กลุ่ม</th><th>รายการ</th><th class="text-end">ยอดซื้อ</th><th class="text-end">คงเหลือ</th></tr></thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= h($order['created_at']) ?></td>
                    <td class="fw-semibold"><?= h($order['group_name']) ?></td>
                    <td><?= h($order['items']) ?></td>
                    <td class="text-end"><?= money($order['total_amount']) ?></td>
                    <td class="text-end fw-bold"><?= money($order['balance_after']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
