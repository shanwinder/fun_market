<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

require_teacher();
$activityId = selected_activity_id();
$activity = $activityId ? activity_by_id($activityId) : null;
$products = [];
$groups = [];

if ($activity) {
    $stmt = db()->prepare("SELECT * FROM products WHERE activity_id = ? AND is_active = 1 ORDER BY sort_order ASC, product_name ASC");
    $stmt->execute([$activity['id']]);
    $products = $stmt->fetchAll();

    $stmt = db()->prepare("SELECT * FROM student_groups WHERE activity_id = ? AND is_active = 1 ORDER BY group_name ASC");
    $stmt->execute([$activity['id']]);
    $groups = $stmt->fetchAll();
}

$pageTitle = 'พิมพ์ QR Code';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <div>
        <h1 class="page-title mb-1">QR Code</h1>
        <p class="text-muted mb-0"><?= $activity ? h($activity['title']) : 'ยังไม่มีกิจกรรม' ?></p>
    </div>
    <button class="btn btn-primary fm-btn-icon" onclick="window.print()"><i data-lucide="printer"></i>พิมพ์</button>
</div>
<?php if (!$activity): ?>
    <div class="alert alert-info">กรุณาสร้างกิจกรรมก่อน</div>
<?php else: ?>
    <h2 class="h4 fw-bold mb-3">บัตรสินค้า</h2>
    <div class="row g-3 mb-5">
        <?php foreach ($products as $product): ?>
            <?php $target = BASE_URL . '/student/product.php?token=' . rawurlencode($product['qr_token']); ?>
            <div class="col-sm-6 col-lg-4">
                <div class="fm-qr-card panel p-3 h-100 text-center">
                    <img src="<?= h(product_image_url($product['image_path'])) ?>" class="product-image mb-3" alt="" loading="lazy" decoding="async">
                    <h3 class="h4 fw-bold"><?= h($product['product_name']) ?></h3>
                    <div class="fs-5 mb-3"><?= money($product['price']) ?></div>
                    <img class="fm-qr-image" src="<?= h(qr_image_url($target, 260)) ?>" alt="QR Code" width="220" height="220">
                    <div class="small text-muted text-break mt-2"><?= h($target) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <h2 class="h4 fw-bold mb-3">QR เข้ากลุ่ม</h2>
    <div class="row g-3">
        <?php foreach ($groups as $group): ?>
            <?php $target = BASE_URL . '/qrcode/group.php?token=' . rawurlencode($group['public_token']); ?>
            <div class="col-sm-6 col-lg-3">
                <div class="fm-qr-card panel p-3 h-100 text-center">
                    <h3 class="h4 fw-bold"><?= h($group['group_name']) ?></h3>
                    <div class="mb-2">PIN: <code><?= h($group['group_pin']) ?></code></div>
                    <img class="fm-qr-image" src="<?= h(qr_image_url($target, 220)) ?>" alt="QR Code" width="190" height="190">
                    <div class="small text-muted text-break mt-2"><?= h($target) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
