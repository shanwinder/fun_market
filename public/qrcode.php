<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../vendor/autoload.php';

use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

$data = trim((string) ($_GET['data'] ?? ''));
$size = (int) ($_GET['size'] ?? 240);
$size = max(120, min(600, $size));

if ($data === '') {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Missing QR data');
}

if (mb_strlen($data, 'UTF-8') > 1000) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    exit('QR data too long');
}

$options = new QROptions([
    'outputInterface' => QRGdImagePNG::class,
    'outputBase64' => false,
    'scale' => max(5, min(12, (int) round($size / 30))),
    'quietzoneSize' => 2,
]);

header('Content-Type: image/png');
header('Cache-Control: public, max-age=86400');

echo (new QRCode($options))->render($data);
