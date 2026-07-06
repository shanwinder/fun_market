<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';

verify_csrf();
$group = require_student_group();
if ($group['activity_status'] !== 'active' || !$group['is_active']) {
    unset($_SESSION['student_group_id']);
    flash('warning', 'กิจกรรมนี้ยังไม่เปิดหรือถูกปิดแล้ว');
    redirect('student/join.php');
}

$returnTo = (string) ($_POST['return_to'] ?? 'student/cart.php');
$allowedReturns = [
    'student/cart.php',
    'student/products.php',
    'student/scan.php',
];
if (!in_array($returnTo, $allowedReturns, true)) {
    $returnTo = 'student/cart.php';
}

$productId = (int) ($_POST['product_id'] ?? 0);
$quantity = max(1, min(20, (int) ($_POST['quantity'] ?? 1)));

$stmt = db()->prepare(
    "SELECT p.*
     FROM products p
     JOIN activities a ON a.id = p.activity_id
     WHERE p.id = ? AND p.activity_id = ? AND p.is_active = 1 AND a.status = 'active'"
);
$stmt->execute([$productId, $group['activity_id']]);
$product = $stmt->fetch();
if (!$product) {
    flash('warning', 'ไม่สามารถเพิ่มสินค้านี้ได้');
    redirect($returnTo);
}

$cart = get_or_create_open_cart((int) $group['activity_id'], (int) $group['id']);
$stmt = db()->prepare(
    'INSERT INTO cart_items (cart_id, product_id, quantity)
     VALUES (?, ?, ?)
     ON DUPLICATE KEY UPDATE quantity = LEAST(quantity + VALUES(quantity), 99)'
);
$stmt->execute([$cart['id'], $productId, $quantity]);

flash('success', 'เพิ่มลงตะกร้าแล้ว');
redirect($returnTo);
