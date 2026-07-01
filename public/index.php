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
<section class="fm-hero">
    <div class="fm-hero-panel p-4 p-lg-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="fm-overline mb-2">Fun Market</div>
                <h1 class="fm-hero-title mb-3">ตลาดอาหาร 5 หมู่</h1>
                <p class="lead text-muted">ร้านค้าจำลองสำหรับให้นักเรียนเลือกซื้อสินค้า วางแผนใช้งบประมาณ และชวนคุยเรื่องโภชนาการหลังจบกิจกรรม</p>
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <a class="btn btn-primary btn-lg fm-btn-icon" href="<?= h(url('student/join.php')) ?>"><i data-lucide="graduation-cap"></i>เริ่มใช้งานนักเรียน</a>
                    <a class="btn btn-outline-primary btn-lg fm-btn-icon" href="<?= h(url('teacher/login.php')) ?>"><i data-lucide="log-in"></i>ครูเข้าสู่ระบบ</a>
                    <a class="btn btn-outline-secondary btn-lg fm-btn-icon" href="<?= h(url('display/summary.php')) ?>"><i data-lucide="monitor"></i>เปิดจอรวม</a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="panel p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i data-lucide="activity" class="text-primary"></i>
                        <h2 class="h4 fw-bold mb-0">สถานะกิจกรรม</h2>
                    </div>
                    <?php if ($active): ?>
                        <p class="mb-2 text-success fw-bold">กำลังเปิดใช้งาน</p>
                        <div class="money-badge"><i data-lucide="check-circle-2"></i><?= h($active['title']) ?></div>
                    <?php else: ?>
                        <p class="text-muted mb-0">ยังไม่มีกิจกรรมที่เปิดใช้งาน ครูสามารถสร้างและเปิดกิจกรรมได้จากหลังบ้าน</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="fm-food-icons" aria-label="อาหาร 5 หมู่">
            <div class="fm-food-icon"><span>🥩</span><span class="fm-food-label">โปรตีน</span></div>
            <div class="fm-food-icon"><span>🥬</span><span class="fm-food-label">ผัก</span></div>
            <div class="fm-food-icon"><span>🍊</span><span class="fm-food-label">ผลไม้</span></div>
            <div class="fm-food-icon"><span>🍚</span><span class="fm-food-label">ข้าวแป้ง</span></div>
            <div class="fm-food-icon"><span>🥛</span><span class="fm-food-label">นม</span></div>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
