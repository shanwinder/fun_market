<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

require_teacher();
verify_csrf();

$productId = (int) ($_POST['id'] ?? 0);
$activityId = selected_activity_id();

if ($productId <= 0 || !$activityId) {
    flash('danger', 'ไม่พบสินค้าที่ต้องการลบ');
    redirect('teacher/products.php');
}

$pdo = db();
$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ? AND activity_id = ?');
$stmt->execute([$productId, $activityId]);
$product = $stmt->fetch();

if (!$product) {
    flash('warning', 'ไม่พบสินค้านี้ในกิจกรรมที่เลือก');
    redirect('teacher/products.php');
}

$stmt = $pdo->prepare(
    'SELECT
        (SELECT COUNT(*) FROM cart_items WHERE product_id = ?) +
        (SELECT COUNT(*) FROM order_items WHERE product_id = ?) AS usage_count'
);
$stmt->execute([$productId, $productId]);
$usageCount = (int) $stmt->fetchColumn();

if ($usageCount > 0) {
    flash('warning', 'ลบสินค้าไม่ได้ เพราะมีข้อมูลตะกร้าหรือประวัติคำสั่งซื้อแล้ว กรุณาปิดขายสินค้าแทน');
    redirect('teacher/products.php');
}

try {
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ? AND activity_id = ?');
    $stmt->execute([$productId, $activityId]);

    if (!empty($product['image_path'])) {
        $imagePath = UPLOAD_DIR . '/' . basename((string) $product['image_path']);
        if (is_file($imagePath)) {
            unlink($imagePath);
        }
    }

    flash('success', 'ลบสินค้าแล้ว');
} catch (Throwable $e) {
    flash('danger', 'ลบสินค้าไม่สำเร็จ: ' . $e->getMessage());
}

redirect('teacher/products.php');
