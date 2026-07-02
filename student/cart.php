<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';

$group = require_student_group();
$cart = get_or_create_open_cart((int) $group['activity_id'], (int) $group['id']);
$items = cart_items((int) $cart['id']);
$total = cart_total($items);
$after = (float) $group['current_balance'] - $total;

$pageTitle = 'ตะกร้าสินค้า';
$bodyClass = 'student-shell';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <div class="fm-overline mb-1">ตะกร้าสินค้า</div>
                <h1 class="page-title mb-0">ตะกร้าของ <?= h($group['group_name']) ?></h1>
            </div>
            <span class="money-badge"><i data-lucide="wallet"></i><?= money($group['current_balance']) ?></span>
        </div>
        <?php if (!$items): ?>
            <div class="fm-panel p-5 text-center">
                <i data-lucide="shopping-basket" class="text-primary mb-3" style="width:56px;height:56px"></i>
                <p class="fs-4 text-muted">ยังไม่มีสินค้าในตะกร้า</p>
                <a class="btn btn-primary btn-lg student-action fm-btn-icon" href="<?= h(url('student/scan.php')) ?>"><i data-lucide="scan-line"></i>สแกนสินค้า</a>
            </div>
        <?php else: ?>
            <form method="post" action="<?= h(url('actions/update_cart.php')) ?>" class="fm-panel mb-3">
                <?= csrf_field() ?>
                <?php foreach ($items as $item): ?>
                    <div class="fm-cart-item">
                        <img class="fm-cart-item-img" src="<?= h(product_image_url($item['image_path'])) ?>" alt="" loading="lazy" decoding="async">
                        <div>
                            <div class="fw-bold fs-5"><?= h($item['product_name']) ?></div>
                            <div class="text-muted"><?= money($item['price']) ?> ต่อชิ้น</div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <input class="form-control form-control-lg text-center" style="width:92px" type="number" min="0" max="99" name="qty[<?= h($item['id']) ?>]" value="<?= h($item['quantity']) ?>" aria-label="จำนวน <?= h($item['product_name']) ?>">
                            <div class="fw-bold fm-cart-line-total"><?= money((float) $item['price'] * (int) $item['quantity']) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="p-3">
                    <button class="btn btn-outline-primary btn-lg fm-btn-icon"><i data-lucide="refresh-cw"></i>ปรับจำนวน</button>
                </div>
            </form>
            <div class="d-grid mb-3">
                <a class="btn btn-primary btn-lg student-action fm-btn-icon" href="<?= h(url('student/scan.php')) ?>"><i data-lucide="scan-line"></i>สแกนสินค้าต่อ</a>
            </div>
            <form method="post" action="<?= h(url('actions/clear_cart.php')) ?>" class="mb-3">
                <?= csrf_field() ?>
                <button class="btn btn-outline-danger btn-lg w-100 fm-btn-icon justify-content-center"><i data-lucide="trash-2"></i>ล้างตะกร้า</button>
            </form>
            <div class="fm-cart-summary">
                <div class="fm-cart-total-row fs-4"><span>รวมทั้งหมด</span><strong><?= money($total) ?></strong></div>
                <div class="fm-cart-total-row"><span>เงินคงเหลือปัจจุบัน</span><strong><?= money($group['current_balance']) ?></strong></div>
                <div class="fm-cart-total-row highlight"><span>หลังซื้อจะเหลือ</span><strong class="<?= $after < 0 ? 'text-danger' : 'text-success' ?>"><?= money($after) ?></strong></div>
                <form method="post" action="<?= h(url('actions/checkout.php')) ?>" class="d-grid mt-4">
                    <?= csrf_field() ?>
                    <button class="btn btn-success btn-lg student-action fm-btn-icon" <?= $after < 0 ? 'disabled' : '' ?>><i data-lucide="check-circle-2"></i>ยืนยันซื้อ</button>
                </form>
                <?php if ($after < 0): ?>
                    <div class="alert alert-warning mt-3 mb-0">เงินของกลุ่มไม่เพียงพอ กรุณาปรับรายการในตะกร้า</div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
