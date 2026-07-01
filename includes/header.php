<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/flash.php';
require_once __DIR__ . '/csrf.php';

$pageTitle = $pageTitle ?? APP_NAME;
$bodyClass = $bodyClass ?? '';
$teacher = current_teacher();
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$isActive = static function (string $path, array $aliases = []) use ($scriptName): string {
    foreach (array_merge([$path], $aliases) as $candidate) {
        if (str_ends_with($scriptName, '/' . ltrim($candidate, '/'))) {
            return ' active';
        }
    }
    return '';
};
$navItems = $teacher ? [
    ['teacher/dashboard.php', 'layout-dashboard', 'Dashboard', ['teacher/live_orders.php', 'teacher/reset_activity.php']],
    ['teacher/activities.php', 'calendar-days', 'กิจกรรม', ['teacher/activity_form.php']],
    ['teacher/groups.php', 'users', 'กลุ่ม', ['teacher/group_form.php', 'teacher/group_detail.php']],
    ['teacher/products.php', 'shopping-bag', 'สินค้า', ['teacher/product_form.php']],
    ['teacher/qrcodes.php', 'qr-code', 'QR Code', []],
    ['teacher/reports.php', 'bar-chart-3', 'รายงาน', []],
] : [
    ['student/join.php', 'graduation-cap', 'นักเรียน', ['student/home.php', 'student/scan.php', 'student/product.php', 'student/cart.php', 'student/checkout.php', 'student/history.php']],
    ['display/summary.php', 'monitor', 'จอรวม', ['display/live.php', 'display/timer.php']],
];
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Noto+Sans+Thai:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css" rel="stylesheet">
    <link href="<?= h(url('public/assets/css/app.css')) ?>" rel="stylesheet">
</head>
<body class="<?= h($bodyClass) ?>">
<a class="fm-skip-link" href="#mainContent">ข้ามไปยังเนื้อหา</a>
<nav class="fm-navbar navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="fm-navbar-brand" href="<?= h(url('public/index.php')) ?>">
            <span class="fm-brand-mark" aria-hidden="true"><i data-lucide="shopping-cart"></i></span>
            <span><?= h(APP_NAME) ?></span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="เมนู">
            <i data-lucide="menu"></i>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1">
                <?php foreach ($navItems as $item): ?>
                    <?php [$path, $icon, $label, $aliases] = $item + [3 => []]; ?>
                    <li class="nav-item">
                        <a class="fm-nav-link<?= h($isActive($path, $aliases)) ?>" href="<?= h(url($path)) ?>">
                            <i data-lucide="<?= h($icon) ?>"></i>
                            <span><?= h($label) ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <?php if ($teacher): ?>
                    <span class="fm-user-pill small"><i data-lucide="user-round"></i><?= h($teacher['full_name']) ?></span>
                    <a class="btn btn-outline-secondary btn-sm fm-btn-icon" href="<?= h(url('teacher/logout.php')) ?>"><i data-lucide="log-out"></i>ออกจากระบบ</a>
                <?php else: ?>
                    <a class="btn btn-primary btn-sm fm-btn-icon" href="<?= h(url('teacher/login.php')) ?>"><i data-lucide="log-in"></i>ครูเข้าสู่ระบบ</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<main id="mainContent" class="container fm-main">
    <?php $flashMessages = consume_flash(); ?>
    <?php if ($flashMessages): ?>
        <div class="fm-toast-stack" aria-live="polite">
        <?php foreach ($flashMessages as $message): ?>
            <?php $type = in_array($message['type'], ['success', 'danger', 'warning', 'info'], true) ? $message['type'] : 'info'; ?>
            <?php $icon = ['success' => 'check-circle-2', 'danger' => 'x-circle', 'warning' => 'triangle-alert', 'info' => 'info'][$type]; ?>
            <div class="fm-toast fm-toast-<?= h($type) ?>" role="alert">
                <i data-lucide="<?= h($icon) ?>"></i>
                <span><?= h($message['message']) ?></span>
                <button type="button" class="btn-close ms-auto" aria-label="ปิด" onclick="this.closest('.fm-toast').remove()"></button>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
