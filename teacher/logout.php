<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';

unset($_SESSION['teacher_id'], $_SESSION['teacher_activity_id']);
session_regenerate_id(true);
redirect('public/index.php');

