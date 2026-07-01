<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';

$group = require_student_group();
$stmt = db()->prepare(
    'SELECT o.*, GROUP_CONCAT(CONCAT(oi.product_name_snapshot, " x", oi.quantity) ORDER BY oi.id SEPARATOR ", ") AS items
     FROM orders o
     LEFT JOIN order_items oi ON oi.order_id = o.id
     WHERE o.group_id = ?
     GROUP BY o.id
     ORDER BY o.created_at DESC'
);
$stmt->execute([$group['id']]);
$orders = $stmt->fetchAll();

$pageTitle = 'ประวัติการซื้อ';
$bodyClass = 'student-shell';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <h1 class="page-title mb-3">ประวัติของ <?= h($group['group_name']) ?></h1>
        <div class="money-badge mb-3">เงินคงเหลือ: <?= money($group['current_balance']) ?></div>
        <?php if (!$orders): ?>
            <div class="panel p-4 text-center text-muted fs-4">ยังไม่มีประวัติการซื้อ</div>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($orders as $order): ?>
                    <div class="list-group-item p-3">
                        <div class="d-flex justify-content-between gap-3">
                            <strong><?= money($order['total_amount']) ?></strong>
                            <span class="text-muted"><?= h($order['created_at']) ?></span>
                        </div>
                        <div class="mt-2"><?= h($order['items']) ?></div>
                        <div class="text-success mt-1">เหลือ <?= money($order['balance_after']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="d-grid mt-3">
            <a class="btn btn-outline-secondary btn-lg" href="<?= h(url('student/home.php')) ?>">กลับหน้ากลุ่ม</a>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

