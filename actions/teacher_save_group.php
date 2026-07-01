<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

require_teacher();
verify_csrf();

$id = (int) ($_POST['id'] ?? 0);
$activityId = (int) ($_POST['activity_id'] ?? 0);
$groupName = trim($_POST['group_name'] ?? '');
$groupPin = trim($_POST['group_pin'] ?? '');
$initialBudget = max(0, (float) ($_POST['initial_budget'] ?? 0));
$isActive = isset($_POST['is_active']) ? 1 : 0;
$resetBalance = isset($_POST['reset_balance']);

if ($activityId <= 0 || $groupName === '' || $groupPin === '') {
    flash('danger', 'ข้อมูลกลุ่มไม่ครบถ้วน');
    redirect('teacher/groups.php');
}

$pdo = db();
$pdo->beginTransaction();
try {
    if ($id > 0) {
        $stmt = $pdo->prepare('SELECT * FROM student_groups WHERE id = ? FOR UPDATE');
        $stmt->execute([$id]);
        $group = $stmt->fetch();
        if (!$group) {
            throw new RuntimeException('ไม่พบกลุ่ม');
        }

        $currentBalance = $resetBalance ? $initialBudget : (float) $group['current_balance'];
        $stmt = $pdo->prepare('UPDATE student_groups SET group_name = ?, group_pin = ?, initial_budget = ?, current_balance = ?, is_active = ? WHERE id = ?');
        $stmt->execute([$groupName, $groupPin, $initialBudget, $currentBalance, $isActive, $id]);

        if ($resetBalance) {
            $stmt = $pdo->prepare(
                "INSERT INTO wallet_transactions (group_id, transaction_type, amount_change, balance_before, balance_after, note)
                 VALUES (?, 'adjust', ?, ?, ?, 'ครูรีเซ็ตเงินคงเหลือ')"
            );
            $stmt->execute([$id, $currentBalance - (float) $group['current_balance'], $group['current_balance'], $currentBalance]);
        }
    } else {
        $token = random_token();
        $stmt = $pdo->prepare(
            'INSERT INTO student_groups (activity_id, group_name, group_pin, initial_budget, current_balance, public_token, is_active)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$activityId, $groupName, $groupPin, $initialBudget, $initialBudget, $token, $isActive]);
        $id = (int) $pdo->lastInsertId();

        $stmt = $pdo->prepare(
            "INSERT INTO wallet_transactions (group_id, transaction_type, amount_change, balance_before, balance_after, note)
             VALUES (?, 'initial', ?, 0, ?, 'เงินตั้งต้น')"
        );
        $stmt->execute([$id, $initialBudget, $initialBudget]);
    }

    $pdo->commit();
    flash('success', 'บันทึกกลุ่มแล้ว');
} catch (Throwable $e) {
    $pdo->rollBack();
    flash('danger', 'บันทึกกลุ่มไม่สำเร็จ: ' . $e->getMessage());
}

redirect('teacher/groups.php');

