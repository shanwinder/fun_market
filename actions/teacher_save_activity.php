<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

$teacher = require_teacher();
verify_csrf();

$id = (int) ($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$status = $_POST['status'] ?? 'draft';

if ($title === '' || !in_array($status, ['draft', 'active', 'closed'], true)) {
    flash('danger', 'ข้อมูลกิจกรรมไม่ครบถ้วน');
    redirect('teacher/activities.php');
}

$pdo = db();
$pdo->beginTransaction();
try {
    if ($status === 'active') {
        $pdo->exec("UPDATE activities SET status = 'draft' WHERE status = 'active'");
    }

    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE activities SET title = ?, description = ?, status = ? WHERE id = ?');
        $stmt->execute([$title, $description, $status, $id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO activities (title, description, status, created_by) VALUES (?, ?, ?, ?)');
        $stmt->execute([$title, $description, $status, $teacher['id']]);
        $id = (int) $pdo->lastInsertId();
    }

    $_SESSION['teacher_activity_id'] = $id;
    $pdo->commit();
    flash('success', 'บันทึกกิจกรรมแล้ว');
} catch (Throwable $e) {
    $pdo->rollBack();
    flash('danger', 'บันทึกกิจกรรมไม่สำเร็จ: ' . $e->getMessage());
}

redirect('teacher/activities.php');

