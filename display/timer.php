<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';

$minutes = max(1, min(120, (int) ($_GET['minutes'] ?? 10)));
$pageTitle = 'จับเวลา';
$bodyClass = 'display-board';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="text-center py-5">
    <h1 class="page-title mb-4">เวลาที่เหลือ</h1>
    <div id="timer" class="display-1 fw-bold"><?= h((string) $minutes) ?>:00</div>
    <div class="mt-4">
        <a class="btn btn-outline-light btn-lg no-print" href="<?= h(url('display/summary.php')) ?>">กลับจอรวม</a>
    </div>
</div>
<script>
    let remaining = <?= $minutes * 60 ?>;
    const timer = document.getElementById('timer');
    setInterval(() => {
        remaining = Math.max(0, remaining - 1);
        const minutes = Math.floor(remaining / 60).toString().padStart(2, '0');
        const seconds = (remaining % 60).toString().padStart(2, '0');
        timer.textContent = `${minutes}:${seconds}`;
    }, 1000);
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

