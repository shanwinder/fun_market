<?php

declare(strict_types=1);

date_default_timezone_set('Asia/Bangkok');

define('APP_NAME', 'ตลาดอาหาร 5 หมู่ ป.3');
define('APP_ROOT', dirname(__DIR__));

$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$scriptDir = rtrim(dirname($scriptName), '/');
$basePath = preg_replace('#/(public|teacher|student|display|qrcode|actions)$#', '', $scriptDir) ?: '';
if ($basePath === '/' || $basePath === '.') {
    $basePath = '';
}

define('BASE_PATH', getenv('APP_BASE_PATH') ?: $basePath);
define('BASE_URL', getenv('APP_BASE_URL') ?: base_url_from_request());
define('UPLOAD_DIR', APP_ROOT . '/public/uploads/products');
define('UPLOAD_URL', BASE_PATH . '/public/uploads/products');

function base_url_from_request(): string
{
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? '') === '443');
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $path = rtrim(BASE_PATH ?? '', '/');

    return $scheme . '://' . $host . $path;
}
