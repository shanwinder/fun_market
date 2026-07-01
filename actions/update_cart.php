<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';

verify_csrf();
$group = require_student_group();
$cart = get_or_create_open_cart((int) $group['activity_id'], (int) $group['id']);
$quantities = $_POST['qty'] ?? [];

foreach ($quantities as $itemId => $qty) {
    $itemId = (int) $itemId;
    $qty = max(0, min(99, (int) $qty));
    if ($qty === 0) {
        $stmt = db()->prepare('DELETE FROM cart_items WHERE id = ? AND cart_id = ?');
        $stmt->execute([$itemId, $cart['id']]);
    } else {
        $stmt = db()->prepare('UPDATE cart_items SET quantity = ? WHERE id = ? AND cart_id = ?');
        $stmt->execute([$qty, $itemId, $cart['id']]);
    }
}

flash('success', 'ปรับตะกร้าแล้ว');
redirect('student/cart.php');

