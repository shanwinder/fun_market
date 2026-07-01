<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';

verify_csrf();
$group = require_student_group();
$pdo = db();

$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare(
        "SELECT sg.*, a.status AS activity_status
         FROM student_groups sg
         JOIN activities a ON a.id = sg.activity_id
         WHERE sg.id = ? FOR UPDATE"
    );
    $stmt->execute([$group['id']]);
    $lockedGroup = $stmt->fetch();
    if (!$lockedGroup || $lockedGroup['activity_status'] !== 'active') {
        throw new RuntimeException('กิจกรรมนี้สิ้นสุดแล้ว ไม่สามารถซื้อสินค้าได้');
    }

    $stmt = $pdo->prepare("SELECT * FROM carts WHERE activity_id = ? AND group_id = ? AND status = 'open' ORDER BY id DESC LIMIT 1 FOR UPDATE");
    $stmt->execute([$lockedGroup['activity_id'], $lockedGroup['id']]);
    $cart = $stmt->fetch();
    if (!$cart) {
        throw new RuntimeException('ยังไม่มีสินค้าในตะกร้า');
    }

    $stmt = $pdo->prepare(
        "SELECT ci.product_id, ci.quantity, p.product_name, p.price
         FROM cart_items ci
         JOIN products p ON p.id = ci.product_id
         WHERE ci.cart_id = ? AND p.activity_id = ? AND p.is_active = 1"
    );
    $stmt->execute([$cart['id'], $lockedGroup['activity_id']]);
    $items = $stmt->fetchAll();
    if (!$items) {
        throw new RuntimeException('ยังไม่มีสินค้าในตะกร้า');
    }

    $total = 0.0;
    foreach ($items as $item) {
        $total += (float) $item['price'] * (int) $item['quantity'];
    }

    $balanceBefore = (float) $lockedGroup['current_balance'];
    if ($total > $balanceBefore) {
        throw new RuntimeException('เงินของกลุ่มไม่เพียงพอ กรุณากลับไปปรับรายการในตะกร้า');
    }

    $balanceAfter = $balanceBefore - $total;
    $stmt = $pdo->prepare('INSERT INTO orders (activity_id, group_id, total_amount, balance_before, balance_after) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$lockedGroup['activity_id'], $lockedGroup['id'], $total, $balanceBefore, $balanceAfter]);
    $orderId = (int) $pdo->lastInsertId();

    $stmt = $pdo->prepare(
        'INSERT INTO order_items (order_id, product_id, product_name_snapshot, unit_price, quantity, subtotal)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    foreach ($items as $item) {
        $subtotal = (float) $item['price'] * (int) $item['quantity'];
        $stmt->execute([$orderId, $item['product_id'], $item['product_name'], $item['price'], $item['quantity'], $subtotal]);
    }

    $stmt = $pdo->prepare('UPDATE student_groups SET current_balance = ? WHERE id = ?');
    $stmt->execute([$balanceAfter, $lockedGroup['id']]);

    $stmt = $pdo->prepare(
        "INSERT INTO wallet_transactions (group_id, order_id, transaction_type, amount_change, balance_before, balance_after, note)
         VALUES (?, ?, 'purchase', ?, ?, ?, 'ซื้อสินค้า')"
    );
    $stmt->execute([$lockedGroup['id'], $orderId, -$total, $balanceBefore, $balanceAfter]);

    $stmt = $pdo->prepare("UPDATE carts SET status = 'checked_out' WHERE id = ?");
    $stmt->execute([$cart['id']]);

    $pdo->commit();
    $_SESSION['student_group_id'] = (int) $lockedGroup['id'];
    flash('success', 'ซื้อสำเร็จ');
    redirect('student/checkout.php?order_id=' . $orderId);
} catch (Throwable $e) {
    $pdo->rollBack();
    flash('warning', $e->getMessage());
    redirect('student/cart.php');
}

