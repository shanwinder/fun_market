<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

require_teacher();
$activityId = selected_activity_id();
$activity = $activityId ? activity_by_id($activityId) : null;
$product = null;

if (!empty($_GET['id'])) {
    $stmt = db()->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([(int) $_GET['id']]);
    $product = $stmt->fetch();
}

$pageTitle = $product ? 'แก้ไขสินค้า' : 'เพิ่มสินค้า';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="fm-form-section">
            <h1 class="h3 fw-bold mb-3"><i data-lucide="shopping-bag" class="me-2"></i><?= h($pageTitle) ?></h1>
            <?php if (!$activity): ?>
                <div class="alert alert-info">กรุณาสร้างกิจกรรมก่อน</div>
            <?php else: ?>
                <form method="post" action="<?= h(url('actions/teacher_save_product.php')) ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= h($product['id'] ?? '') ?>">
                    <input type="hidden" name="activity_id" value="<?= h($product['activity_id'] ?? $activity['id']) ?>">
                    <div class="mb-3">
                        <label class="form-label">ชื่อสินค้า</label>
                        <input class="form-control" name="product_name" value="<?= h($product['product_name'] ?? '') ?>" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">ราคา</label>
                            <input class="form-control" type="number" step="0.01" min="0" name="price" value="<?= h($product['price'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ลำดับการแสดงผล</label>
                            <input class="form-control" type="number" name="sort_order" value="<?= h($product['sort_order'] ?? '0') ?>">
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label class="form-label">รายละเอียดสั้น ๆ</label>
                        <textarea class="form-control" name="description" rows="2"><?= h($product['description'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">รูปสินค้า</label>
                        <div class="fm-image-upload">
                            <div class="d-flex align-items-center gap-3">
                                <i data-lucide="image-up" class="text-primary" style="width:36px;height:36px"></i>
                                <div class="flex-grow-1">
                                    <input class="form-control" type="file" name="image" accept=".jpg,.jpeg,.png,.webp" data-image-preview="productPreview">
                                    <div class="form-text">รองรับ JPG, PNG, WEBP</div>
                                </div>
                            </div>
                            <img id="productPreview" class="fm-image-preview mt-3" src="<?= h(!empty($product['image_path']) ? product_image_url($product['image_path']) : '') ?>" alt="" <?= empty($product['image_path']) ? 'hidden' : '' ?>>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">บันทึกสำหรับครู</label>
                        <textarea class="form-control" name="teacher_note" rows="2"><?= h($product['teacher_note'] ?? '') ?></textarea>
                        <div class="form-text">ข้อมูลนี้แสดงเฉพาะฝั่งครู ไม่แสดงในหน้านักเรียน</div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" <?= (($product['is_active'] ?? 1) ? 'checked' : '') ?>>
                        <label class="form-check-label">เปิดขาย</label>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button class="btn btn-primary fm-btn-icon"><i data-lucide="save"></i>บันทึก</button>
                        <a class="btn btn-outline-secondary fm-btn-icon" href="<?= h(url('teacher/products.php')) ?>"><i data-lucide="arrow-left"></i>กลับ</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
