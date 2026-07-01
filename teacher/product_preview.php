<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

require_teacher();
$activityId = selected_activity_id();
$productId = (int) ($_GET['id'] ?? 0);

$stmt = db()->prepare(
    'SELECT p.*, a.title AS activity_title
     FROM products p
     JOIN activities a ON a.id = p.activity_id
     WHERE p.id = ? AND p.activity_id = ?'
);
$stmt->execute([$productId, $activityId]);
$product = $stmt->fetch();

if (!$product) {
    flash('warning', 'ไม่พบสินค้านี้ในกิจกรรมที่เลือก');
    redirect('teacher/products.php');
}

$productTarget = BASE_URL . '/student/product.php?token=' . rawurlencode($product['qr_token']);
$pageTitle = 'ดูหน้าสินค้า';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title mb-1">ดูหน้าสินค้า</h1>
        <p class="text-muted mb-0"><?= h($product['activity_title']) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary fm-btn-icon" href="<?= h(url('teacher/products.php')) ?>"><i data-lucide="arrow-left"></i>กลับ</a>
        <a class="btn btn-primary fm-btn-icon" href="<?= h(url('teacher/product_form.php?id=' . $product['id'])) ?>"><i data-lucide="pencil"></i>แก้ไข</a>
    </div>
</div>

<div class="row justify-content-center g-4">
    <div class="col-lg-7">
        <div class="fm-product-detail">
            <img class="fm-product-image-lg" src="<?= h(product_image_url($product['image_path'])) ?>" alt="<?= h($product['product_name']) ?>" loading="lazy" decoding="async">
            <div class="p-4">
                <div class="mb-3">
                    <?= $product['is_active'] ? '<span class="badge text-bg-success">เปิดขาย</span>' : '<span class="badge text-bg-secondary">ปิดขาย</span>' ?>
                </div>
                <h2 class="page-title mb-2"><?= h($product['product_name']) ?></h2>
                <?php if (!empty($product['description'])): ?>
                    <p class="text-muted fs-5"><?= h($product['description']) ?></p>
                <?php endif; ?>
                <div class="fm-price-badge mb-4">
                    <i data-lucide="coins"></i>
                    <?= money($product['price']) ?>
                </div>
                <dl class="row mb-0">
                    <dt class="col-sm-4">QR Token</dt>
                    <dd class="col-sm-8"><code><?= h($product['qr_token']) ?></code></dd>
                    <dt class="col-sm-4">ลำดับ</dt>
                    <dd class="col-sm-8"><?= h($product['sort_order']) ?></dd>
                    <?php if (!empty($product['teacher_note'])): ?>
                        <dt class="col-sm-4">บันทึกครู</dt>
                        <dd class="col-sm-8"><?= nl2br(h($product['teacher_note'])) ?></dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="fm-qr-card panel p-4 h-100 text-center">
            <h2 class="h4 fw-bold mb-3">QR Code สินค้า</h2>
            <img class="fm-qr-image mb-3" src="<?= h(qr_image_url($productTarget, 280)) ?>" alt="QR Code" width="240" height="240">
            <div class="small text-muted text-break"><?= h($productTarget) ?></div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
