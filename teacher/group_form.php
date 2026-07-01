<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

require_teacher();
$activityId = selected_activity_id();
$activity = $activityId ? activity_by_id($activityId) : null;
$group = null;

if (!empty($_GET['id'])) {
    $stmt = db()->prepare('SELECT * FROM student_groups WHERE id = ?');
    $stmt->execute([(int) $_GET['id']]);
    $group = $stmt->fetch();
}

$pageTitle = $group ? 'แก้ไขกลุ่ม' : 'เพิ่มกลุ่ม';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="panel p-4">
            <h1 class="h3 fw-bold mb-3"><?= h($pageTitle) ?></h1>
            <?php if (!$activity): ?>
                <div class="alert alert-info">กรุณาสร้างกิจกรรมก่อน</div>
            <?php else: ?>
                <form method="post" action="<?= h(url('actions/teacher_save_group.php')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= h($group['id'] ?? '') ?>">
                    <input type="hidden" name="activity_id" value="<?= h($group['activity_id'] ?? $activity['id']) ?>">
                    <div class="mb-3">
                        <label class="form-label">ชื่อกลุ่ม</label>
                        <input class="form-control" name="group_name" value="<?= h($group['group_name'] ?? '') ?>" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">PIN กลุ่ม</label>
                            <input class="form-control" name="group_pin" value="<?= h($group['group_pin'] ?? random_int(1000, 9999)) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">เงินตั้งต้น</label>
                            <input class="form-control" type="number" step="0.01" min="0" name="initial_budget" value="<?= h($group['initial_budget'] ?? '100.00') ?>" required>
                        </div>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" <?= (($group['is_active'] ?? 1) ? 'checked' : '') ?>>
                        <label class="form-check-label">เปิดให้นักเรียนเลือกกลุ่มนี้</label>
                    </div>
                    <?php if ($group): ?>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="reset_balance" value="1" id="resetBalance">
                            <label class="form-check-label" for="resetBalance">ตั้งเงินคงเหลือให้เท่ากับเงินตั้งต้นใหม่</label>
                        </div>
                    <?php endif; ?>
                    <div class="d-flex gap-2 mt-4">
                        <button class="btn btn-primary">บันทึก</button>
                        <a class="btn btn-outline-secondary" href="<?= h(url('teacher/groups.php')) ?>">กลับ</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

