<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

require_teacher();
$id = (int) ($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT sg.*, a.title AS activity_title FROM student_groups sg JOIN activities a ON a.id = sg.activity_id WHERE sg.id = ?');
$stmt->execute([$id]);
$group = $stmt->fetch();
if (!$group) {
    flash('danger', 'ไม่พบกลุ่ม');
    redirect('teacher/groups.php');
}

$stmt = db()->prepare(
    'SELECT o.*, GROUP_CONCAT(CONCAT(oi.product_name_snapshot, " x", oi.quantity, " = ", FORMAT(oi.subtotal, 2), " บาท") ORDER BY oi.id SEPARATOR "\n") AS items
     FROM orders o
     LEFT JOIN order_items oi ON oi.order_id = o.id
     WHERE o.group_id = ?
     GROUP BY o.id
     ORDER BY o.created_at DESC'
);
$stmt->execute([$group['id']]);
$orders = $stmt->fetchAll();

$stmt = db()->prepare(
    'SELECT wt.*
     FROM wallet_transactions wt
     WHERE wt.group_id = ?
     ORDER BY wt.created_at DESC, wt.id DESC'
);
$stmt->execute([$group['id']]);
$transactions = $stmt->fetchAll();

$pageTitle = 'รายละเอียดกลุ่ม';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title mb-1"><?= h($group['group_name']) ?></h1>
        <p class="text-muted mb-0"><?= h($group['activity_title']) ?></p>
    </div>
    <a class="btn btn-outline-secondary fm-btn-icon" href="<?= h(url('teacher/groups.php')) ?>"><i data-lucide="arrow-left"></i>กลับ</a>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="stat-card"><div class="fm-stat-label">เงินตั้งต้น</div><div class="stat-value"><?= money($group['initial_budget']) ?></div></div></div>
    <div class="col-md-4"><div class="stat-card fm-stat-card-orders"><div class="fm-stat-label">ใช้ไป</div><div class="stat-value"><?= money((float) $group['initial_budget'] - (float) $group['current_balance']) ?></div></div></div>
    <div class="col-md-4"><div class="stat-card fm-stat-card-items"><div class="fm-stat-label">คงเหลือ</div><div class="stat-value"><?= money($group['current_balance']) ?></div></div></div>
</div>
<div class="panel p-3 mb-4">
    <h2 class="h4 fw-bold mb-3">รายการที่ซื้อ</h2>
    <div class="table-responsive">
        <table class="table align-middle fm-table" data-table>
            <thead><tr><th>เวลา</th><th>รายการ</th><th class="text-end">ยอดซื้อ</th><th class="text-end">หลังซื้อ</th></tr></thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= h($order['created_at']) ?></td>
                    <td><?= nl2br(h($order['items'])) ?></td>
                    <td class="text-end"><?= money($order['total_amount']) ?></td>
                    <td class="text-end fw-bold"><?= money($order['balance_after']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="panel p-3">
    <h2 class="h4 fw-bold mb-3">ประวัติเงิน</h2>
    <div class="table-responsive">
        <table class="table align-middle fm-table">
            <thead><tr><th>เวลา</th><th>ประเภท</th><th class="text-end">เปลี่ยนแปลง</th><th class="text-end">ก่อน</th><th class="text-end">หลัง</th><th>หมายเหตุ</th></tr></thead>
            <tbody>
            <?php foreach ($transactions as $tx): ?>
                <tr>
                    <td><?= h($tx['created_at']) ?></td>
                    <td><?= h($tx['transaction_type']) ?></td>
                    <td class="text-end"><?= money($tx['amount_change']) ?></td>
                    <td class="text-end"><?= money($tx['balance_before']) ?></td>
                    <td class="text-end"><?= money($tx['balance_after']) ?></td>
                    <td><?= h($tx['note']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
