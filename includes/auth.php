<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/flash.php';

function require_teacher(): array
{
    $teacher = current_teacher();
    if (!$teacher) {
        flash('warning', 'กรุณาเข้าสู่ระบบครูก่อน');
        redirect('teacher/login.php');
    }

    return $teacher;
}

