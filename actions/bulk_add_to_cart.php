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

$quantities = $_POST['qty'] ?? [];
if (!is_array($quantities)) {
    flash('warning', 'ข้อมูลรายการสินค้าไม่ถูกต้อง');
    redirect('student/products.php');
}

$selected = [];
foreach ($quantities as $productId => $quantity) {
    $productId = (int) $productId;
    if (!is_scalar($quantity)) {
        continue;
    }

    $quantity = max(0, min(20, (int) $quantity));

    if ($productId <= 0 || $quantity <= 0) {
        continue;
    }

    $selected[$productId] = (($selected[$productId] ?? 0) + $quantity);
}

if (!$selected) {
    flash('warning', 'กรุณาเลือกสินค้าอย่างน้อย 1 รายการ');
    redirect('student/products.php');
}

$placeholders = implode(',', array_fill(0, count($selected), '?'));
$params = array_merge(array_keys($selected), [(int) $group['activity_id']]);

$stmt = db()->prepare(
    "SELECT p.id
     FROM products p
     JOIN activities a ON a.id = p.activity_id
     WHERE p.id IN ($placeholders)
       AND p.activity_id = ?
       AND p.is_active = 1
       AND a.status = 'active'"
);
$stmt->execute($params);
$allowedProductIds = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));

if (!$allowedProductIds) {
    flash('warning', 'ไม่สามารถเพิ่มสินค้าที่เลือกได้');
    redirect('student/products.php');
}

$cart = get_or_create_open_cart((int) $group['activity_id'], (int) $group['id']);
$stmt = db()->prepare(
    'INSERT INTO cart_items (cart_id, product_id, quantity)
     VALUES (?, ?, ?)
     ON DUPLICATE KEY UPDATE quantity = LEAST(quantity + VALUES(quantity), 99)'
);

$addedCount = 0;
foreach ($allowedProductIds as $productId) {
    $quantity = min(20, (int) ($selected[$productId] ?? 0));
    if ($quantity <= 0) {
        continue;
    }

    $stmt->execute([(int) $cart['id'], $productId, $quantity]);
    $addedCount++;
}

if ($addedCount <= 0) {
    flash('warning', 'ไม่สามารถเพิ่มสินค้าที่เลือกได้');
    redirect('student/products.php');
}

flash('success', 'เพิ่มสินค้าลงตะกร้าแล้ว');
redirect('student/cart.php');
