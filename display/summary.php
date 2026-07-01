<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';

header('Refresh: 10');
$activity = active_activity();
$groups = [];
$totalOrders = 0;

if ($activity) {
    $stmt = db()->prepare(
        'SELECT sg.group_name, sg.initial_budget, sg.current_balance,
            (sg.initial_budget - sg.current_balance) AS spent,
            COUNT(DISTINCT o.id) AS order_count,
            COALESCE(SUM(oi.quantity), 0) AS item_count
         FROM student_groups sg
         LEFT JOIN orders o ON o.group_id = sg.id
         LEFT JOIN order_items oi ON oi.order_id = o.id
         WHERE sg.activity_id = ? AND sg.is_active = 1
         GROUP BY sg.id
         ORDER BY sg.group_name ASC'
    );
    $stmt->execute([$activity['id']]);
    $groups = $stmt->fetchAll();

    $stmt = db()->prepare('SELECT COUNT(*) FROM orders WHERE activity_id = ?');
    $stmt->execute([$activity['id']]);
    $totalOrders = (int) $stmt->fetchColumn();
}

$pageTitle = 'จอรวม';
$bodyClass = 'display-board';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title mb-1"><?= $activity ? h($activity['title']) : 'ยังไม่มีกิจกรรม' ?></h1>
        <p class="text-light-emphasis mb-0">ภาพรวมทุกกลุ่ม รีเฟรชทุก 10 วินาที</p>
    </div>
    <div class="display-card p-3 text-center">
        <div class="text-light-emphasis">รายการซื้อทั้งหมด</div>
        <div class="stat-value"><?= $totalOrders ?></div>
    </div>
</div>
<?php if (!$activity): ?>
    <div class="display-card p-4 text-center fs-3">รอครูเปิดกิจกรรม</div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($groups as $group): ?>
            <div class="col-md-6 col-xl-4">
                <div class="display-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <h2 class="h2 fw-bold mb-3"><?= h($group['group_name']) ?></h2>
                        <span class="badge text-bg-info fs-6"><?= (int) $group['item_count'] ?> ชิ้น</span>
                    </div>
                    <div class="fs-5 text-light-emphasis">เงินคงเหลือ</div>
                    <div class="display-5 fw-bold"><?= money($group['current_balance']) ?></div>
                    <div class="progress mt-3" style="height: 14px">
                        <?php $percent = ((float) $group['initial_budget'] > 0) ? max(0, min(100, ((float) $group['spent'] / (float) $group['initial_budget']) * 100)) : 0; ?>
                        <div class="progress-bar" style="width: <?= h((string) $percent) ?>%"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2 text-light-emphasis">
                        <span>ใช้ไป <?= money($group['spent']) ?></span>
                        <span><?= (int) $group['order_count'] ?> ครั้ง</span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
