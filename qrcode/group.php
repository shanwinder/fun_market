<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';

$token = trim($_GET['token'] ?? '');
$stmt = db()->prepare(
    "SELECT sg.*
     FROM student_groups sg
     JOIN activities a ON a.id = sg.activity_id
     WHERE sg.public_token = ? AND sg.is_active = 1 AND a.status = 'active'"
);
$stmt->execute([$token]);
$group = $stmt->fetch();

if (!$group) {
    flash('warning', 'ไม่พบกลุ่มหรือกิจกรรมถูกปิดแล้ว');
    redirect('student/join.php');
}

session_regenerate_id(true);
$_SESSION['student_group_id'] = (int) $group['id'];
redirect('student/home.php');

