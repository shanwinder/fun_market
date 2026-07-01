<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

require_teacher();
$activityId = selected_activity_id();
$activity = $activityId ? activity_by_id($activityId) : null;
$products = [];

if ($activity) {
    $stmt = db()->prepare('SELECT * FROM products WHERE activity_id = ? ORDER BY product_name ASC, sort_order ASC, id ASC');
    $stmt->execute([$activity['id']]);
    $products = $stmt->fetchAll();
    sort_products_by_thai_name($products);
}

$pageTitle = 'จัดการสินค้า';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title mb-1">สินค้า</h1>
        <p class="text-muted mb-0"><?= $activity ? h($activity['title']) : 'ยังไม่มีกิจกรรม' ?></p>
    </div>
    <a class="btn btn-primary fm-btn-icon <?= $activity ? '' : 'disabled' ?>" href="<?= h(url('teacher/product_form.php')) ?>"><i data-lucide="plus-circle"></i>เพิ่มสินค้า</a>
</div>
<?php if (!$activity): ?>
    <div class="alert alert-info">กรุณาสร้างกิจกรรมก่อน</div>
<?php else: ?>
    <div class="panel p-3">
        <div class="table-responsive">
            <table class="table align-middle fm-table" data-table data-table-preserve-order="1">
                <thead><tr><th>รูป</th><th>สินค้า</th><th class="text-end">ราคา</th><th>สถานะ</th><th>QR Token</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><img src="<?= h(product_image_url($product['image_path'])) ?>" alt="" loading="lazy" decoding="async" style="width:72px;height:54px;object-fit:cover;border-radius:8px"></td>
                        <td class="fw-semibold"><?= h($product['product_name']) ?></td>
                        <td class="text-end"><?= money($product['price']) ?></td>
                        <td><?= $product['is_active'] ? '<span class="badge text-bg-success">เปิด</span>' : '<span class="badge text-bg-secondary">ปิด</span>' ?></td>
                        <td><code><?= h($product['qr_token']) ?></code></td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-primary fm-btn-icon" href="<?= h(url('teacher/product_preview.php?id=' . $product['id'])) ?>"><i data-lucide="eye"></i>ดูหน้าสินค้า</a>
                            <a class="btn btn-sm btn-outline-secondary fm-btn-icon" href="<?= h(url('teacher/product_form.php?id=' . $product['id'])) ?>"><i data-lucide="pencil"></i>แก้ไข</a>
                            <form class="d-inline" method="post" action="<?= h(url('actions/teacher_delete_product.php')) ?>" onsubmit="return confirm('ยืนยันลบสินค้านี้หรือไม่?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= h($product['id']) ?>">
                                <button class="btn btn-sm btn-outline-danger fm-btn-icon" type="submit"><i data-lucide="trash-2"></i>ลบ</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
