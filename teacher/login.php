<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim($_POST['username'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    $stmt = db()->prepare('SELECT * FROM teachers WHERE username = ?');
    $stmt->execute([$username]);
    $teacher = $stmt->fetch();

    if ($teacher && password_verify($password, $teacher['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['teacher_id'] = (int) $teacher['id'];
        flash('success', 'เข้าสู่ระบบสำเร็จ');
        redirect('teacher/dashboard.php');
    }

    flash('danger', 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
}

$pageTitle = 'ครูเข้าสู่ระบบ';
$bodyClass = 'fm-login-shell';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="fm-login-page">
    <section class="fm-login-visual">
        <div class="fm-login-visual-content">
            <div class="fm-brand-mark mb-4" style="width:64px;height:64px"><i data-lucide="shopping-cart"></i></div>
            <h1 class="fm-display mb-3">ตลาดอาหาร 5 หมู่</h1>
            <p class="fs-4 mb-0">แดชบอร์ดสำหรับครู จัดกิจกรรม กลุ่ม สินค้า QR Code และรายงานในที่เดียว</p>
        </div>
    </section>
    <div class="fm-login-form-wrapper">
        <div class="fm-login-form">
            <a class="fm-navbar-brand mb-4" href="<?= h(url('public/index.php')) ?>">
                <span class="fm-brand-mark" aria-hidden="true"><i data-lucide="arrow-left"></i></span>
                <span>กลับหน้าแรก</span>
            </a>
            <div class="fm-panel p-4">
                <h2 class="h3 fw-bold mb-3">ครูเข้าสู่ระบบ</h2>
                <form method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">ชื่อผู้ใช้</label>
                        <div class="fm-input-group">
                            <i data-lucide="user" class="fm-input-icon"></i>
                            <input class="form-control form-control-lg" name="username" required autofocus>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">รหัสผ่าน</label>
                        <div class="fm-input-group">
                            <i data-lucide="lock-keyhole" class="fm-input-icon"></i>
                            <input class="form-control form-control-lg" type="password" name="password" required>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-lg w-100 fm-btn-icon justify-content-center"><i data-lucide="log-in"></i>เข้าสู่ระบบ</button>
                </form>
                <p class="small text-muted mt-3 mb-0">ค่าเริ่มต้นหลังติดตั้ง: teacher / teacher123</p>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
