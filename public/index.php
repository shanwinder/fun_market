<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';

$active = active_activity();
if (current_teacher()) {
    redirect('teacher/dashboard.php');
}

$pageTitle = APP_NAME;
require_once __DIR__ . '/../includes/header.php';
?>
<div class="row align-items-center g-4 py-3">
    <div class="col-lg-7">
        <h1 class="page-title mb-3">ร้านค้าจำลองสำหรับกิจกรรมอาหาร 5 หมู่</h1>
        <p class="lead text-muted">ให้นักเรียนเลือกซื้อสินค้า วางแผนใช้งบประมาณ และเก็บข้อมูลให้ครูใช้ชวนอภิปรายหลังจบกิจกรรม</p>
        <div class="d-flex flex-wrap gap-2 mt-4">
            <a class="btn btn-primary btn-lg" href="<?= h(url('student/join.php')) ?>">เริ่มใช้งานนักเรียน</a>
            <a class="btn btn-outline-primary btn-lg" href="<?= h(url('teacher/login.php')) ?>">ครูเข้าสู่ระบบ</a>
            <a class="btn btn-outline-secondary btn-lg" href="<?= h(url('display/summary.php')) ?>">เปิดจอรวม</a>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="panel p-4">
            <h2 class="h4 fw-bold">สถานะกิจกรรม</h2>
            <?php if ($active): ?>
                <p class="mb-2">กำลังเปิดใช้งาน</p>
                <div class="money-badge"><?= h($active['title']) ?></div>
            <?php else: ?>
                <p class="text-muted mb-0">ยังไม่มีกิจกรรมที่เปิดใช้งาน ครูสามารถสร้างและเปิดกิจกรรมได้จากหลังบ้าน</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

