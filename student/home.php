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
$spent = max(0, (float) $group['initial_budget'] - (float) $group['current_balance']);
$budget = max(0.0, (float) $group['initial_budget']);
$spentPct = $budget > 0 ? min(100, ($spent / $budget) * 100) : 0;

$pageTitle = 'หน้ากลุ่ม';
$bodyClass = 'student-shell';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-7 text-center">
        <div class="fm-overline mb-2">กลุ่มของฉัน</div>
        <h1 class="page-title mb-4"><?= h($group['group_name']) ?></h1>

        <div class="fm-wallet-card mb-4" role="region" aria-label="เงินคงเหลือของกลุ่ม">
            <div class="fm-wallet-label"><i data-lucide="wallet"></i>เงินคงเหลือ</div>
            <div class="fm-wallet-amount"><?= money($group['current_balance']) ?></div>
            <div class="fm-wallet-progress" role="progressbar" aria-valuenow="<?= h((string) round($spentPct, 2)) ?>" aria-valuemin="0" aria-valuemax="100" aria-label="ใช้เงินไปแล้ว <?= h(money($spent)) ?> จาก <?= h(money($budget)) ?>">
                <div class="fm-wallet-progress-bar" style="width: <?= h((string) $spentPct) ?>%"></div>
            </div>
            <div class="d-flex justify-content-between mt-2 small" style="opacity:.92">
                <span>ใช้ไป <?= money($spent) ?></span>
                <span>จาก <?= money($budget) ?></span>
            </div>
        </div>

        <div class="d-grid gap-3 fm-stagger">
            <a class="fm-student-btn fm-student-btn-scan" href="<?= h(url('student/scan.php')) ?>">
                <i data-lucide="scan-line"></i>
                สแกน QR Code
            </a>
            <a class="fm-student-btn fm-student-btn-products" href="<?= h(url('student/products.php')) ?>">
                <i data-lucide="store"></i>
                เลือกซื้อสินค้า
            </a>
            <a class="fm-student-btn fm-student-btn-cart" href="<?= h(url('student/cart.php')) ?>">
                <i data-lucide="shopping-cart"></i>
                ดูตะกร้า
                <span class="fm-cart-badge"><?= count($items) ?></span>
            </a>
            <a class="fm-student-btn fm-student-btn-history" href="<?= h(url('student/history.php')) ?>">
                <i data-lucide="clock"></i>
                ประวัติการซื้อ
            </a>
            <a class="btn btn-outline-secondary btn-lg fm-btn-icon justify-content-center" href="<?= h(url('student/join.php')) ?>" onclick="sessionStorage.clear()">
                <i data-lucide="refresh-cw"></i>
                เปลี่ยนกลุ่ม
            </a>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
