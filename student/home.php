<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';

$group = require_student_group();
if ($group['activity_status'] !== 'active' || !$group['is_active']) {
    unset($_SESSION['student_group_id']);
    flash('warning', 'กิจกรรมนี้ยังไม่เปิดหรือถูกปิดแล้ว');
    redirect('student/join.php');
}

$cart = get_or_create_open_cart((int) $group['activity_id'], (int) $group['id']);
$items = cart_items((int) $cart['id']);

$pageTitle = 'หน้ากลุ่ม';
$bodyClass = 'student-shell';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-7 text-center">
        <h1 class="page-title mb-2"><?= h($group['group_name']) ?></h1>
        <div class="money-badge fs-4 mb-4">เงินคงเหลือ: <?= money($group['current_balance']) ?></div>
        <div class="d-grid gap-3">
            <a class="btn btn-primary btn-lg student-action" href="<?= h(url('student/scan.php')) ?>">สแกน QR Code</a>
            <a class="btn btn-success btn-lg student-action" href="<?= h(url('student/cart.php')) ?>">ดูตะกร้า (<?= count($items) ?>)</a>
            <a class="btn btn-outline-primary btn-lg student-action" href="<?= h(url('student/history.php')) ?>">ประวัติการซื้อ</a>
            <a class="btn btn-outline-secondary btn-lg" href="<?= h(url('student/join.php')) ?>" onclick="sessionStorage.clear()">เปลี่ยนกลุ่ม</a>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

