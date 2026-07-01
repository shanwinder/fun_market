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
require_once __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="panel p-4">
            <h1 class="h3 fw-bold mb-3">ครูเข้าสู่ระบบ</h1>
            <form method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">ชื่อผู้ใช้</label>
                    <input class="form-control form-control-lg" name="username" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">รหัสผ่าน</label>
                    <input class="form-control form-control-lg" type="password" name="password" required>
                </div>
                <button class="btn btn-primary btn-lg w-100">เข้าสู่ระบบ</button>
            </form>
            <p class="small text-muted mt-3 mb-0">ค่าเริ่มต้นหลังติดตั้ง: teacher / teacher123</p>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

