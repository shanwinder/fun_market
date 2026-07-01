<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';

$group = require_student_group();
$orderId = (int) ($_GET['order_id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM orders WHERE id = ? AND group_id = ?');
$stmt->execute([$orderId, $group['id']]);
$order = $stmt->fetch();

if (!$order) {
    redirect('student/home.php');
}

$pageTitle = 'ซื้อสำเร็จ';
$bodyClass = 'student-shell';
require_once __DIR__ . '/../includes/header.php';
?>
<div data-confetti></div>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="fm-panel fm-success-screen">
            <div class="fm-success-icon" aria-hidden="true"><i data-lucide="check"></i></div>
            <h1 class="page-title text-success mb-3">ซื้อสำเร็จ</h1>
            <div class="fs-4 mb-2">ยอดซื้อทั้งหมด: <strong><?= money($order['total_amount']) ?></strong></div>
            <div class="fs-4 mb-4">เงินคงเหลือ: <strong><?= money($order['balance_after']) ?></strong></div>
            <div class="d-grid gap-3">
                <a class="btn btn-primary btn-lg student-action fm-btn-icon" href="<?= h(url('student/scan.php')) ?>"><i data-lucide="scan-line"></i>สแกนสินค้าต่อ</a>
                <a class="btn btn-outline-primary btn-lg student-action fm-btn-icon" href="<?= h(url('student/history.php')) ?>"><i data-lucide="clock"></i>ดูประวัติการซื้อ</a>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
