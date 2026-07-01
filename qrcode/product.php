<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';

$token = trim($_GET['token'] ?? '');
redirect('student/product.php?token=' . rawurlencode($token));

