<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

require_teacher();
$activityId = selected_activity_id();
$activity = $activityId ? activity_by_id($activityId) : null;
$groups = [];

if ($activity) {
    $stmt = db()->prepare(
        'SELECT sg.*,
            (sg.initial_budget - sg.current_balance) AS spent,
            COALESCE(SUM(oi.quantity), 0) AS item_count
         FROM student_groups sg
         LEFT JOIN orders o ON o.group_id = sg.id
         LEFT JOIN order_items oi ON oi.order_id = o.id
         WHERE sg.activity_id = ?
         GROUP BY sg.id
         ORDER BY sg.group_name ASC'
    );
    $stmt->execute([$activity['id']]);
    $groups = $stmt->fetchAll();
}

$pageTitle = 'จัดการกลุ่ม';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title mb-1">กลุ่มนักเรียน</h1>
        <p class="text-muted mb-0"><?= $activity ? h($activity['title']) : 'ยังไม่มีกิจกรรม' ?></p>
    </div>
    <a class="btn btn-primary <?= $activity ? '' : 'disabled' ?>" href="<?= h(url('teacher/group_form.php')) ?>">เพิ่มกลุ่ม</a>
</div>
<?php if (!$activity): ?>
    <div class="alert alert-info">กรุณาสร้างกิจกรรมก่อน</div>
<?php else: ?>
    <div class="panel p-3">
        <div class="table-responsive">
            <table class="table align-middle" data-table>
                <thead><tr><th>กลุ่ม</th><th>PIN</th><th class="text-end">ตั้งต้น</th><th class="text-end">ใช้ไป</th><th class="text-end">คงเหลือ</th><th>สถานะ</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($groups as $group): ?>
                    <tr>
                        <td class="fw-semibold"><?= h($group['group_name']) ?></td>
                        <td><code><?= h($group['group_pin']) ?></code></td>
                        <td class="text-end"><?= money($group['initial_budget']) ?></td>
                        <td class="text-end"><?= money($group['spent']) ?></td>
                        <td class="text-end fw-bold"><?= money($group['current_balance']) ?></td>
                        <td><?= $group['is_active'] ? '<span class="badge text-bg-success">เปิด</span>' : '<span class="badge text-bg-secondary">ปิด</span>' ?></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="<?= h(url('teacher/group_detail.php?id=' . $group['id'])) ?>">ดู</a>
                            <a class="btn btn-sm btn-outline-secondary" href="<?= h(url('teacher/group_form.php?id=' . $group['id'])) ?>">แก้ไข</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

