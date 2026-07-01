<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';

$group = require_student_group();
$token = trim($_GET['token'] ?? '');

$stmt = db()->prepare(
    "SELECT p.*
     FROM products p
     JOIN activities a ON a.id = p.activity_id
     WHERE p.qr_token = ? AND p.is_active = 1 AND a.status = 'active'"
);
$stmt->execute([$token]);
$product = $stmt->fetch();

if (!$product || (int) $product['activity_id'] !== (int) $group['activity_id']) {
    flash('warning', 'ไม่พบสินค้านี้ในกิจกรรมที่กำลังใช้งาน');
    redirect('student/home.php');
}

$pageTitle = $product['product_name'];
$bodyClass = 'student-shell';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="fm-product-detail">
            <img class="fm-product-image-lg" src="<?= h(product_image_url($product['image_path'])) ?>" alt="<?= h($product['product_name']) ?>" loading="lazy" decoding="async">
            <div class="p-4">
                <h1 class="page-title mb-2"><?= h($product['product_name']) ?></h1>
                <?php if (!empty($product['description'])): ?>
                    <p class="text-muted fs-5"><?= h($product['description']) ?></p>
                <?php endif; ?>
                <div class="fm-price-badge mb-4">
                    <i data-lucide="coins"></i>
                    <?= money($product['price']) ?>
                </div>
            <form method="post" action="<?= h(url('actions/add_to_cart.php')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= h($product['id']) ?>">
                <label class="form-label fs-5 fw-bold">จำนวน</label>
                <div class="fm-qty-selector mb-4" data-qty-selector>
                    <button class="fm-qty-btn" type="button" data-qty-action="decrement" aria-label="ลดจำนวน"><i data-lucide="minus"></i></button>
                    <input class="fm-qty-input" type="number" name="quantity" min="1" max="20" value="1" required aria-label="จำนวนสินค้า">
                    <button class="fm-qty-btn" type="button" data-qty-action="increment" aria-label="เพิ่มจำนวน"><i data-lucide="plus"></i></button>
                </div>
                <button class="btn btn-success btn-lg w-100 student-action fm-btn-icon">
                    <i data-lucide="shopping-cart"></i>
                    เพิ่มลงตะกร้า
                </button>
            </form>
            </div>
        </div>
        <div class="d-grid gap-2 mt-3">
            <a class="btn btn-outline-primary btn-lg fm-btn-icon justify-content-center" href="<?= h(url('student/cart.php')) ?>"><i data-lucide="shopping-basket"></i>ดูตะกร้า</a>
            <a class="btn btn-outline-secondary btn-lg fm-btn-icon justify-content-center" href="<?= h(url('student/scan.php')) ?>"><i data-lucide="scan-line"></i>สแกนต่อ</a>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
