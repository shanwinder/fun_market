<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';

$group = require_student_group();
$pageTitle = 'สแกน QR Code';
$bodyClass = 'student-shell';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="text-center mb-3">
            <h1 class="page-title">สแกนสินค้า</h1>
            <div class="money-badge">เงินคงเหลือ: <?= money($group['current_balance']) ?></div>
        </div>
        <div class="panel p-3">
            <div id="reader" style="width:100%;min-height:320px"></div>
            <p class="text-muted small mt-3 mb-0">ถ้ากล้องใช้ไม่ได้ สามารถใช้แอปกล้องของเครื่องสแกน QR Code ได้</p>
        </div>
        <div class="d-grid mt-3">
            <a class="btn btn-outline-secondary btn-lg" href="<?= h(url('student/home.php')) ?>">กลับหน้ากลุ่ม</a>
        </div>
    </div>
</div>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    window.addEventListener('load', () => {
        if (!window.Html5QrcodeScanner) return;
        const scanner = new Html5QrcodeScanner('reader', { fps: 10, qrbox: { width: 250, height: 250 } }, false);
        scanner.render((decodedText) => {
            if (decodedText.startsWith('http')) {
                window.location.href = decodedText;
                return;
            }
            window.location.href = <?= json_encode(BASE_URL . '/student/product.php?token=') ?> + encodeURIComponent(decodedText);
        });
    });
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

