<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/flash.php';
require_once __DIR__ . '/csrf.php';

$pageTitle = $pageTitle ?? APP_NAME;
$bodyClass = $bodyClass ?? '';
$teacher = current_teacher();
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css" rel="stylesheet">
    <link href="<?= h(url('public/assets/css/app.css')) ?>" rel="stylesheet">
</head>
<body class="<?= h($bodyClass) ?>">
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= h(url('public/index.php')) ?>"><?= h(APP_NAME) ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if ($teacher): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= h(url('teacher/dashboard.php')) ?>">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= h(url('teacher/activities.php')) ?>">กิจกรรม</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= h(url('teacher/groups.php')) ?>">กลุ่ม</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= h(url('teacher/products.php')) ?>">สินค้า</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= h(url('teacher/qrcodes.php')) ?>">QR Code</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= h(url('teacher/reports.php')) ?>">รายงาน</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= h(url('student/join.php')) ?>">นักเรียน</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= h(url('display/summary.php')) ?>">จอรวม</a></li>
                <?php endif; ?>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <?php if ($teacher): ?>
                    <span class="text-muted small"><?= h($teacher['full_name']) ?></span>
                    <a class="btn btn-outline-secondary btn-sm" href="<?= h(url('teacher/logout.php')) ?>">ออกจากระบบ</a>
                <?php else: ?>
                    <a class="btn btn-primary btn-sm" href="<?= h(url('teacher/login.php')) ?>">ครูเข้าสู่ระบบ</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<main class="container py-4">
    <?php foreach (consume_flash() as $message): ?>
        <div class="alert alert-<?= h($message['type']) ?> alert-dismissible fade show" role="alert">
            <?= h($message['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; ?>

