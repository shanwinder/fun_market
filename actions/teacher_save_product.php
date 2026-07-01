<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

require_teacher();
verify_csrf();

$id = (int) ($_POST['id'] ?? 0);
$activityId = (int) ($_POST['activity_id'] ?? 0);
$name = trim($_POST['product_name'] ?? '');
$description = trim($_POST['description'] ?? '');
$price = max(0, (float) ($_POST['price'] ?? 0));
$sortOrder = (int) ($_POST['sort_order'] ?? 0);
$teacherNote = trim($_POST['teacher_note'] ?? '');
$isActive = isset($_POST['is_active']) ? 1 : 0;

if ($activityId <= 0 || $name === '') {
    flash('danger', 'ข้อมูลสินค้าไม่ครบถ้วน');
    redirect('teacher/products.php');
}

$imagePath = null;
if (!empty($_FILES['image']['name'])) {
    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        flash('danger', 'อัปโหลดรูปไม่สำเร็จ');
        redirect('teacher/products.php');
    }
    if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
        flash('danger', 'ไฟล์รูปต้องไม่เกิน 2MB');
        redirect('teacher/products.php');
    }

    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
        flash('danger', 'อนุญาตเฉพาะไฟล์ jpg, jpeg, png, webp');
        redirect('teacher/products.php');
    }

    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0775, true);
    }

    $filename = random_token(12) . '.' . $ext;
    $target = UPLOAD_DIR . '/' . $filename;
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        flash('danger', 'บันทึกรูปไม่สำเร็จ');
        redirect('teacher/products.php');
    }
    $imagePath = $filename;
}

$pdo = db();
try {
    if ($id > 0) {
        if ($imagePath) {
            $stmt = $pdo->prepare('UPDATE products SET product_name = ?, description = ?, price = ?, image_path = ?, is_active = ?, sort_order = ?, teacher_note = ? WHERE id = ?');
            $stmt->execute([$name, $description, $price, $imagePath, $isActive, $sortOrder, $teacherNote, $id]);
        } else {
            $stmt = $pdo->prepare('UPDATE products SET product_name = ?, description = ?, price = ?, is_active = ?, sort_order = ?, teacher_note = ? WHERE id = ?');
            $stmt->execute([$name, $description, $price, $isActive, $sortOrder, $teacherNote, $id]);
        }
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO products (activity_id, product_name, description, price, image_path, qr_token, is_active, sort_order, teacher_note)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$activityId, $name, $description, $price, $imagePath, random_token(), $isActive, $sortOrder, $teacherNote]);
    }

    flash('success', 'บันทึกสินค้าแล้ว');
} catch (Throwable $e) {
    flash('danger', 'บันทึกสินค้าไม่สำเร็จ: ' . $e->getMessage());
}

redirect('teacher/products.php');

