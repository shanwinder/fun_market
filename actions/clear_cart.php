<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';

verify_csrf();

$group = require_student_group();
$cart = get_or_create_open_cart((int) $group['activity_id'], (int) $group['id']);
$stmt = db()->prepare('DELETE FROM cart_items WHERE cart_id = ?');
$stmt->execute([$cart['id']]);

flash('success', 'ล้างตะกร้าแล้ว');
redirect('student/cart.php');
