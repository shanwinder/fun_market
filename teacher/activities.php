<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

require_teacher();
$stmt = db()->query('SELECT a.*, t.full_name FROM activities a JOIN teachers t ON t.id = a.created_by ORDER BY a.created_at DESC');
$activities = $stmt->fetchAll();

$pageTitle = 'จัดการกิจกรรม';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title mb-1">กิจกรรม</h1>
        <p class="text-muted mb-0">สร้าง เปิดใช้งาน หรือปิดกิจกรรมการเรียนรู้</p>
    </div>
    <a class="btn btn-primary" href="<?= h(url('teacher/activity_form.php')) ?>">เพิ่มกิจกรรม</a>
</div>
<div class="panel p-3">
    <div class="table-responsive">
        <table class="table align-middle" data-table>
            <thead><tr><th>ชื่อกิจกรรม</th><th>สถานะ</th><th>ผู้สร้าง</th><th>สร้างเมื่อ</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($activities as $activity): ?>
                <tr>
                    <td class="fw-semibold"><?= h($activity['title']) ?></td>
                    <td><span class="badge text-bg-<?= $activity['status'] === 'active' ? 'success' : ($activity['status'] === 'closed' ? 'secondary' : 'warning') ?>"><?= h($activity['status']) ?></span></td>
                    <td><?= h($activity['full_name']) ?></td>
                    <td><?= h($activity['created_at']) ?></td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-primary" href="<?= h(url('teacher/dashboard.php?activity_id=' . $activity['id'])) ?>">ใช้กิจกรรมนี้</a>
                        <a class="btn btn-sm btn-outline-secondary" href="<?= h(url('teacher/activity_form.php?id=' . $activity['id'])) ?>">แก้ไข</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

