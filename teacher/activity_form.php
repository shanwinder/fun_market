<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

require_teacher();
$activity = null;
if (!empty($_GET['id'])) {
    $activity = activity_by_id((int) $_GET['id']);
}

$pageTitle = $activity ? 'แก้ไขกิจกรรม' : 'เพิ่มกิจกรรม';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="fm-form-section">
            <h1 class="h3 fw-bold mb-3"><i data-lucide="calendar-days" class="me-2"></i><?= h($pageTitle) ?></h1>
            <form method="post" action="<?= h(url('actions/teacher_save_activity.php')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= h($activity['id'] ?? '') ?>">
                <div class="mb-3">
                    <label class="form-label">ชื่อกิจกรรม</label>
                    <input class="form-control" name="title" value="<?= h($activity['title'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">รายละเอียด</label>
                    <textarea class="form-control" name="description" rows="4"><?= h($activity['description'] ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">สถานะ</label>
                    <select class="form-select" name="status">
                        <?php foreach (['draft' => 'ร่าง', 'active' => 'กำลังใช้งาน', 'closed' => 'ปิดกิจกรรม'] as $value => $label): ?>
                            <option value="<?= h($value) ?>" <?= (($activity['status'] ?? 'draft') === $value) ? 'selected' : '' ?>><?= h($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">ถ้าเลือก “กำลังใช้งาน” ระบบจะปิด active ของกิจกรรมอื่นอัตโนมัติ</div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary fm-btn-icon"><i data-lucide="save"></i>บันทึก</button>
                    <a class="btn btn-outline-secondary fm-btn-icon" href="<?= h(url('teacher/activities.php')) ?>"><i data-lucide="arrow-left"></i>กลับ</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
