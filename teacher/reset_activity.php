<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

require_teacher();
$activityId = selected_activity_id();
$activity = $activityId ? activity_by_id($activityId) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (!$activity) {
        flash('danger', 'ไม่พบกิจกรรม');
        redirect('teacher/dashboard.php');
    }

    $pdo = db();
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('SELECT id, current_balance, initial_budget FROM student_groups WHERE activity_id = ? FOR UPDATE');
        $stmt->execute([$activity['id']]);
        $groups = $stmt->fetchAll();

        $stmt = $pdo->prepare('DELETE FROM orders WHERE activity_id = ?');
        $stmt->execute([$activity['id']]);

        $stmt = $pdo->prepare('DELETE FROM carts WHERE activity_id = ?');
        $stmt->execute([$activity['id']]);

        $stmt = $pdo->prepare('DELETE wt FROM wallet_transactions wt JOIN student_groups sg ON sg.id = wt.group_id WHERE sg.activity_id = ?');
        $stmt->execute([$activity['id']]);

        foreach ($groups as $group) {
            $stmt = $pdo->prepare('UPDATE student_groups SET current_balance = ? WHERE id = ?');
            $stmt->execute([$group['initial_budget'], $group['id']]);
            $stmt = $pdo->prepare(
                "INSERT INTO wallet_transactions (group_id, transaction_type, amount_change, balance_before, balance_after, note)
                 VALUES (?, 'initial', ?, 0, ?, 'รีเซ็ตกิจกรรม')"
            );
            $stmt->execute([$group['id'], $group['initial_budget'], $group['initial_budget']]);
        }

        $pdo->commit();
        flash('success', 'รีเซ็ตกิจกรรมแล้ว');
        redirect('teacher/dashboard.php');
    } catch (Throwable $e) {
        $pdo->rollBack();
        flash('danger', 'รีเซ็ตไม่สำเร็จ: ' . $e->getMessage());
    }
}

$pageTitle = 'รีเซ็ตกิจกรรม';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="panel p-4 fm-warning-panel">
            <div class="d-flex align-items-start gap-3 mb-3">
                <i data-lucide="triangle-alert" class="text-danger" style="width:44px;height:44px"></i>
                <div>
                    <h1 class="h3 fw-bold mb-1">รีเซ็ตกิจกรรม</h1>
                    <p class="text-muted mb-0"><?= $activity ? h($activity['title']) : 'ยังไม่มีกิจกรรมที่เลือก' ?></p>
                </div>
            </div>
            <p class="text-muted">ระบบจะลบคำสั่งซื้อ ตะกร้า และประวัติเงินของกิจกรรมนี้ แล้วตั้งเงินคงเหลือของทุกกลุ่มกลับไปเท่ากับเงินตั้งต้น</p>
            <form method="post">
                <?= csrf_field() ?>
                <button class="btn btn-danger fm-btn-icon"><i data-lucide="rotate-ccw"></i>ยืนยันรีเซ็ต</button>
                <a class="btn btn-outline-secondary fm-btn-icon" href="<?= h(url('teacher/dashboard.php')) ?>"><i data-lucide="x"></i>ยกเลิก</a>
            </form>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
