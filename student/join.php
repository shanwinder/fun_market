<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/csrf.php';

$activity = active_activity();
$groups = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $pin = trim($_POST['group_pin'] ?? '');
    $groupId = (int) ($_POST['group_id'] ?? 0);

    $stmt = db()->prepare(
        "SELECT sg.*
         FROM student_groups sg
         JOIN activities a ON a.id = sg.activity_id
         WHERE sg.id = ? AND sg.group_pin = ? AND sg.is_active = 1 AND a.status = 'active'"
    );
    $stmt->execute([$groupId, $pin]);
    $group = $stmt->fetch();

    if ($group) {
        session_regenerate_id(true);
        $_SESSION['student_group_id'] = (int) $group['id'];
        $next = $_SESSION['student_next'] ?? url('student/home.php');
        unset($_SESSION['student_next']);
        header('Location: ' . $next);
        exit;
    }

    flash('danger', 'PIN ไม่ถูกต้อง กรุณาลองใหม่');
}

if ($activity) {
    $stmt = db()->prepare("SELECT * FROM student_groups WHERE activity_id = ? AND is_active = 1 ORDER BY group_name ASC");
    $stmt->execute([$activity['id']]);
    $groups = $stmt->fetchAll();
}

$pageTitle = 'เลือกกลุ่ม';
$bodyClass = 'student-shell';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="text-center mb-4">
            <h1 class="page-title">เลือกกลุ่มของฉัน</h1>
            <?php if ($activity): ?><p class="text-muted mb-0"><?= h($activity['title']) ?></p><?php endif; ?>
        </div>
        <?php if (!$activity): ?>
            <div class="alert alert-info text-center">ยังไม่มีกิจกรรมที่เปิดใช้งาน</div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($groups as $group): ?>
                    <div class="col-md-6">
                        <form class="group-button p-3 h-100" method="post">
                            <?= csrf_field() ?>
                            <input type="hidden" name="group_id" value="<?= h($group['id']) ?>">
                            <h2 class="h3 fw-bold"><?= h($group['group_name']) ?></h2>
                            <label class="form-label mt-2">ใส่ PIN กลุ่ม</label>
                            <input class="form-control form-control-lg text-center" name="group_pin" inputmode="numeric" autocomplete="off" required>
                            <button class="btn btn-primary btn-lg w-100 mt-3 student-action">เข้ากลุ่ม</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

