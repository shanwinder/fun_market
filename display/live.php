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
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="page-title mb-1">ความเคลื่อนไหวล่าสุด</h1>
        <p class="text-light-emphasis mb-0">รีเฟรชทุก 8 วินาที</p>
    </div>
    <a class="btn btn-outline-light no-print fm-btn-icon" href="<?= h(url('display/summary.php')) ?>"><i data-lucide="monitor"></i>จอรวม</a>
</div>
<div class="row g-3 fm-stagger">
    <?php foreach ($orders as $order): ?>
        <div class="col-lg-6">
            <div class="display-card p-4">
                <div class="d-flex justify-content-between gap-3">
                    <h2 class="fm-display-group-name"><?= h($order['group_name']) ?></h2>
                    <span class="fs-4 fw-bold text-info"><?= money($order['total_amount']) ?></span>
                </div>
                <p class="fs-5 mb-2"><?= h($order['items']) ?></p>
                <div class="text-light-emphasis">เหลือ <?= money($order['balance_after']) ?> • <?= h($order['created_at']) ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php if (!$orders): ?>
    <div class="display-card p-5 text-center fs-3">ยังไม่มีความเคลื่อนไหว</div>
<?php endif; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
