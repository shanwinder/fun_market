<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';

$minutes = max(1, min(120, (int) ($_GET['minutes'] ?? 10)));
$pageTitle = 'จับเวลา';
$bodyClass = 'display-board';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="fm-timer-display">
    <h1 class="page-title mb-4">เวลาที่เหลือ</h1>
    <div class="fm-timer-ring mb-4">
        <div id="timer" class="fm-timer-value"><?= h(str_pad((string) $minutes, 2, '0', STR_PAD_LEFT)) ?>:00</div>
    </div>
    <div class="mt-4">
        <a class="btn btn-outline-light btn-lg no-print fm-btn-icon" href="<?= h(url('display/summary.php')) ?>"><i data-lucide="monitor"></i>กลับจอรวม</a>
    </div>
</div>
<script>
    let remaining = <?= $minutes * 60 ?>;
    const totalSeconds = remaining;
    const timer = document.getElementById('timer');
    setInterval(() => {
        remaining = Math.max(0, remaining - 1);
        const minutes = Math.floor(remaining / 60).toString().padStart(2, '0');
        const seconds = (remaining % 60).toString().padStart(2, '0');
        timer.textContent = `${minutes}:${seconds}`;
        timer.classList.toggle('warning', remaining <= Math.max(60, totalSeconds * 0.25) && remaining > 30);
        timer.classList.toggle('danger', remaining <= 30);
    }, 1000);
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
