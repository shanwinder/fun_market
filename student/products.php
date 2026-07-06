<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';

$group = require_student_group();
if ($group['activity_status'] !== 'active' || !$group['is_active']) {
    unset($_SESSION['student_group_id']);
    flash('warning', 'กิจกรรมนี้ยังไม่เปิดหรือถูกปิดแล้ว');
    redirect('student/join.php');
}

$cart = get_or_create_open_cart((int) $group['activity_id'], (int) $group['id']);
$items = cart_items((int) $cart['id']);
$cartItemCount = count($items);

$stmt = db()->prepare(
    'SELECT id, product_name, description, price, image_path, sort_order
     FROM products
     WHERE activity_id = ?
       AND is_active = 1
     ORDER BY sort_order ASC, product_name ASC, id ASC'
);
$stmt->execute([(int) $group['activity_id']]);
$products = $stmt->fetchAll();
sort_products_by_thai_name($products);

$pageTitle = 'เลือกซื้อสินค้า';
$bodyClass = 'student-shell';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <div class="fm-overline mb-1">รายการสินค้า</div>
        <h1 class="page-title mb-1">เลือกซื้อสินค้า</h1>
        <p class="text-muted mb-0">
            <?= h($group['group_name']) ?> · เงินคงเหลือ <?= money($group['current_balance']) ?>
        </p>
    </div>

    <a class="btn btn-outline-primary btn-lg fm-btn-icon" href="<?= h(url('student/cart.php')) ?>">
        <i data-lucide="shopping-cart"></i>
        ตะกร้า
        <span class="badge text-bg-primary ms-1"><?= h((string) $cartItemCount) ?></span>
    </a>
</div>

<?php if (!$products): ?>
    <div class="fm-panel p-5 text-center">
        <i data-lucide="package-x" class="text-primary mb-3" style="width:56px;height:56px"></i>
        <p class="fs-4 text-muted mb-3">ยังไม่มีสินค้าเปิดขาย</p>
        <div class="d-grid gap-2 col-md-8 mx-auto">
            <a class="btn btn-outline-primary btn-lg fm-btn-icon justify-content-center" href="<?= h(url('student/scan.php')) ?>">
                <i data-lucide="scan-line"></i>
                สแกน QR Code
            </a>
            <a class="btn btn-outline-secondary btn-lg fm-btn-icon justify-content-center" href="<?= h(url('student/home.php')) ?>">
                <i data-lucide="arrow-left"></i>
                กลับหน้ากลุ่ม
            </a>
        </div>
    </div>
<?php else: ?>
    <form method="post" action="<?= h(url('actions/bulk_add_to_cart.php')) ?>">
        <?= csrf_field() ?>

        <div class="row g-3 fm-stagger fm-product-grid">
            <?php foreach ($products as $product): ?>
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="fm-product-card h-100">
                        <img
                            class="fm-product-card-image"
                            src="<?= h(product_image_url($product['image_path'])) ?>"
                            alt="<?= h($product['product_name']) ?>"
                            loading="lazy"
                            decoding="async"
                        >

                        <div class="fm-product-card-body">
                            <h2 class="h4 fw-bold mb-2"><?= h($product['product_name']) ?></h2>

                            <?php if (!empty($product['description'])): ?>
                                <p class="text-muted mb-3"><?= h($product['description']) ?></p>
                            <?php endif; ?>

                            <div class="fm-product-card-footer">
                                <div class="fm-price-badge">
                                    <i data-lucide="coins"></i>
                                    <?= money($product['price']) ?>
                                </div>

                                <label class="form-label fw-bold mb-2" for="qty<?= h($product['id']) ?>">จำนวน</label>
                                <div class="fm-qty-selector" data-qty-selector>
                                    <button class="fm-qty-btn" type="button" data-qty-action="decrement" aria-label="ลดจำนวน <?= h($product['product_name']) ?>">
                                        <i data-lucide="minus"></i>
                                    </button>
                                    <input
                                        id="qty<?= h($product['id']) ?>"
                                        class="fm-qty-input"
                                        type="number"
                                        name="qty[<?= h($product['id']) ?>]"
                                        min="0"
                                        max="20"
                                        value="0"
                                        inputmode="numeric"
                                        aria-label="จำนวนสินค้า <?= h($product['product_name']) ?>"
                                    >
                                    <button class="fm-qty-btn" type="button" data-qty-action="increment" aria-label="เพิ่มจำนวน <?= h($product['product_name']) ?>">
                                        <i data-lucide="plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="fm-catalog-actions mt-4">
            <button class="btn btn-success btn-lg student-action fm-btn-icon">
                <i data-lucide="shopping-cart"></i>
                เพิ่มรายการทั้งหมดลงตะกร้า
            </button>
            <a class="btn btn-primary btn-lg fm-btn-icon justify-content-center" href="<?= h(url('student/cart.php')) ?>">
                <i data-lucide="shopping-basket"></i>
                ไปที่ตะกร้า
            </a>
            <a class="btn btn-outline-primary btn-lg fm-btn-icon justify-content-center" href="<?= h(url('student/scan.php')) ?>">
                <i data-lucide="scan-line"></i>
                สแกน QR Code
            </a>
            <a class="btn btn-outline-secondary btn-lg fm-btn-icon justify-content-center" href="<?= h(url('student/home.php')) ?>">
                <i data-lucide="arrow-left"></i>
                กลับหน้ากลุ่ม
            </a>
        </div>
    </form>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
