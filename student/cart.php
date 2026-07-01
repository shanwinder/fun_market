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
        <h1 class="page-title mb-3">ตะกร้าของ <?= h($group['group_name']) ?></h1>
        <?php if (!$items): ?>
            <div class="panel p-4 text-center">
                <p class="fs-4 text-muted">ยังไม่มีสินค้าในตะกร้า</p>
                <a class="btn btn-primary btn-lg student-action" href="<?= h(url('student/scan.php')) ?>">สแกนสินค้า</a>
            </div>
        <?php else: ?>
            <form method="post" action="<?= h(url('actions/update_cart.php')) ?>" class="panel p-3 mb-3">
                <?= csrf_field() ?>
                <?php foreach ($items as $item): ?>
                    <div class="row align-items-center g-2 border-bottom py-3">
                        <div class="col-3 col-md-2"><img src="<?= h(product_image_url($item['image_path'])) ?>" alt="" style="width:100%;aspect-ratio:1;object-fit:cover;border-radius:8px"></div>
                        <div class="col-9 col-md-5">
                            <div class="fw-bold fs-5"><?= h($item['product_name']) ?></div>
                            <div class="text-muted"><?= money($item['price']) ?> ต่อชิ้น</div>
                        </div>
                        <div class="col-7 col-md-3">
                            <input class="form-control form-control-lg text-center" type="number" min="0" max="99" name="qty[<?= h($item['id']) ?>]" value="<?= h($item['quantity']) ?>">
                        </div>
                        <div class="col-5 col-md-2 text-end fw-bold"><?= money((float) $item['price'] * (int) $item['quantity']) ?></div>
                    </div>
                <?php endforeach; ?>
                <div class="d-flex flex-wrap justify-content-between align-items-center pt-3 gap-2">
                    <button class="btn btn-outline-primary btn-lg">ปรับจำนวน</button>
                </div>
            </form>
            <form method="post" action="<?= h(url('actions/clear_cart.php')) ?>" class="mb-3">
                <?= csrf_field() ?>
                <button class="btn btn-outline-danger btn-lg w-100">ล้างตะกร้า</button>
            </form>
            <div class="panel p-4">
                <div class="d-flex justify-content-between fs-4 mb-2"><span>รวมทั้งหมด</span><strong><?= money($total) ?></strong></div>
                <div class="d-flex justify-content-between"><span>เงินคงเหลือปัจจุบัน</span><strong><?= money($group['current_balance']) ?></strong></div>
                <div class="d-flex justify-content-between fs-5 mt-2"><span>หลังซื้อจะเหลือ</span><strong class="<?= $after < 0 ? 'text-danger' : 'text-success' ?>"><?= money($after) ?></strong></div>
                <form method="post" action="<?= h(url('actions/checkout.php')) ?>" class="d-grid mt-4">
                    <?= csrf_field() ?>
                    <button class="btn btn-success btn-lg student-action" <?= $after < 0 ? 'disabled' : '' ?>>ยืนยันซื้อ</button>
                </form>
                <?php if ($after < 0): ?>
                    <div class="alert alert-warning mt-3 mb-0">เงินของกลุ่มไม่เพียงพอ กรุณาปรับรายการในตะกร้า</div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
