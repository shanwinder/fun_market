<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';

header('Refresh: 8');
$activity = active_activity();
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
         LIMIT 12'
    );
    $stmt->execute([$activity['id']]);
    $orders = $stmt->fetchAll();
}

$pageTitle = 'ความเคลื่อนไหวล่าสุด';
$bodyClass = 'display-board';
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="page-title mb-4">ความเคลื่อนไหวล่าสุด</h1>
<div class="row g-3">
    <?php foreach ($orders as $order): ?>
        <div class="col-lg-6">
            <div class="display-card p-4">
                <div class="d-flex justify-content-between gap-3">
                    <h2 class="h3 fw-bold"><?= h($order['group_name']) ?></h2>
                    <span class="fs-5"><?= money($order['total_amount']) ?></span>
                </div>
                <p class="fs-5 mb-2"><?= h($order['items']) ?></p>
                <div class="text-light-emphasis">เหลือ <?= money($order['balance_after']) ?> • <?= h($order['created_at']) ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
