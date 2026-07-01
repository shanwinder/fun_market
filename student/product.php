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
        <div class="product-card p-3">
            <img class="product-image mb-3" src="<?= h(product_image_url($product['image_path'])) ?>" alt="<?= h($product['product_name']) ?>">
            <h1 class="page-title mb-2"><?= h($product['product_name']) ?></h1>
            <?php if (!empty($product['description'])): ?>
                <p class="text-muted"><?= h($product['description']) ?></p>
            <?php endif; ?>
            <div class="fs-2 fw-bold mb-3"><?= money($product['price']) ?></div>
            <form method="post" action="<?= h(url('actions/add_to_cart.php')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= h($product['id']) ?>">
                <label class="form-label fs-5 fw-bold">จำนวน</label>
                <div class="input-group input-group-lg mb-3">
                    <button class="btn btn-outline-secondary" type="button" onclick="this.parentElement.querySelector('input').stepDown()">-</button>
                    <input class="form-control text-center" type="number" name="quantity" min="1" max="20" value="1" required>
                    <button class="btn btn-outline-secondary" type="button" onclick="this.parentElement.querySelector('input').stepUp()">+</button>
                </div>
                <button class="btn btn-success btn-lg w-100 student-action">เพิ่มลงตะกร้า</button>
            </form>
        </div>
        <div class="d-grid gap-2 mt-3">
            <a class="btn btn-outline-primary btn-lg" href="<?= h(url('student/cart.php')) ?>">ดูตะกร้า</a>
            <a class="btn btn-outline-secondary btn-lg" href="<?= h(url('student/scan.php')) ?>">สแกนต่อ</a>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

