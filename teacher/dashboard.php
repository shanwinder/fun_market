<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

require_teacher();
$activityId = selected_activity_id();
$activity = $activityId ? activity_by_id($activityId) : null;

$stats = ['groups' => 0, 'products' => 0, 'orders_total' => 0, 'items_total' => 0];
$groups = [];
$latestOrders = [];

if ($activity) {
    $stmt = db()->prepare('SELECT COUNT(*) FROM student_groups WHERE activity_id = ?');
    $stmt->execute([$activity['id']]);
    $stats['groups'] = (int) $stmt->fetchColumn();

    $stmt = db()->prepare('SELECT COUNT(*) FROM products WHERE activity_id = ?');
    $stmt->execute([$activity['id']]);
    $stats['products'] = (int) $stmt->fetchColumn();

    $stmt = db()->prepare('SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE activity_id = ?');
    $stmt->execute([$activity['id']]);
    $stats['orders_total'] = (float) $stmt->fetchColumn();

    $stmt = db()->prepare('SELECT COALESCE(SUM(oi.quantity), 0) FROM order_items oi JOIN orders o ON o.id = oi.order_id WHERE o.activity_id = ?');
    $stmt->execute([$activity['id']]);
    $stats['items_total'] = (int) $stmt->fetchColumn();

    $stmt = db()->prepare(
        'SELECT sg.*,
            (sg.initial_budget - sg.current_balance) AS spent,
            COALESCE(SUM(oi.quantity), 0) AS item_count,
            MAX(o.created_at) AS latest_at
         FROM student_groups sg
         LEFT JOIN orders o ON o.group_id = sg.id
         LEFT JOIN order_items oi ON oi.order_id = o.id
         WHERE sg.activity_id = ?
         GROUP BY sg.id
         ORDER BY sg.group_name ASC'
    );
    $stmt->execute([$activity['id']]);
    $groups = $stmt->fetchAll();

    $stmt = db()->prepare(
        'SELECT o.*, sg.group_name
         FROM orders o
         JOIN student_groups sg ON sg.id = o.group_id
         WHERE o.activity_id = ?
         ORDER BY o.created_at DESC
         LIMIT 10'
    );
    $stmt->execute([$activity['id']]);
    $latestOrders = $stmt->fetchAll();
}

$pageTitle = 'Dashboard ครู';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between gap-3 align-items-start mb-4">
    <div>
        <h1 class="page-title mb-1">Dashboard ครู</h1>
        <p class="text-muted mb-0"><?= $activity ? h($activity['title']) : 'ยังไม่มีกิจกรรม' ?></p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-primary" href="<?= h(url('teacher/activities.php')) ?>">เลือกกิจกรรม</a>
        <a class="btn btn-primary" href="<?= h(url('teacher/qrcodes.php')) ?>">พิมพ์ QR Code</a>
    </div>
</div>

<?php if (!$activity): ?>
    <div class="alert alert-info">เริ่มต้นด้วยการสร้างกิจกรรมแรก</div>
<?php else: ?>
    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="stat-card"><div class="text-muted">จำนวนกลุ่ม</div><div class="stat-value"><?= $stats['groups'] ?></div></div></div>
        <div class="col-md-3"><div class="stat-card"><div class="text-muted">จำนวนสินค้า</div><div class="stat-value"><?= $stats['products'] ?></div></div></div>
        <div class="col-md-3"><div class="stat-card"><div class="text-muted">ยอดซื้อรวม</div><div class="stat-value"><?= money($stats['orders_total']) ?></div></div></div>
        <div class="col-md-3"><div class="stat-card"><div class="text-muted">จำนวนชิ้นที่ซื้อ</div><div class="stat-value"><?= $stats['items_total'] ?></div></div></div>
    </div>

    <div class="panel p-3 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h4 fw-bold mb-0">ภาพรวมกลุ่ม</h2>
            <a class="btn btn-sm btn-outline-primary" href="<?= h(url('teacher/groups.php')) ?>">จัดการกลุ่ม</a>
        </div>
        <div class="table-responsive">
            <table class="table align-middle" data-table>
                <thead>
                <tr>
                    <th>กลุ่ม</th>
                    <th class="text-end">เงินตั้งต้น</th>
                    <th class="text-end">ใช้ไป</th>
                    <th class="text-end">คงเหลือ</th>
                    <th class="text-end">จำนวนชิ้น</th>
                    <th>รายการล่าสุด</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($groups as $group): ?>
                    <tr>
                        <td><?= h($group['group_name']) ?></td>
                        <td class="text-end"><?= money($group['initial_budget']) ?></td>
                        <td class="text-end"><?= money($group['spent']) ?></td>
                        <td class="text-end fw-bold"><?= money($group['current_balance']) ?></td>
                        <td class="text-end"><?= (int) $group['item_count'] ?></td>
                        <td><?= h($group['latest_at'] ?: '-') ?></td>
                        <td><a class="btn btn-sm btn-outline-secondary" href="<?= h(url('teacher/group_detail.php?id=' . $group['id'])) ?>">ดู</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h4 fw-bold mb-0">รายการซื้อล่าสุด</h2>
            <a class="btn btn-sm btn-outline-primary" href="<?= h(url('teacher/live_orders.php')) ?>">ดูแบบสด</a>
        </div>
        <?php if (!$latestOrders): ?>
            <p class="text-muted mb-0">ยังไม่มีการซื้อ</p>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($latestOrders as $order): ?>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between" href="<?= h(url('teacher/group_detail.php?id=' . $order['group_id'])) ?>">
                        <span><?= h($order['group_name']) ?> ซื้อ <?= money($order['total_amount']) ?></span>
                        <span class="text-muted"><?= h($order['created_at']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

