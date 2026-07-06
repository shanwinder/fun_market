<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/flash.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function h(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function money(float|string|int $amount): string
{
    return number_format((float) $amount, 2) . ' บาท';
}

function url(string $path = ''): string
{
    return rtrim(BASE_PATH, '/') . '/' . ltrim($path, '/');
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function current_teacher(): ?array
{
    if (empty($_SESSION['teacher_id'])) {
        return null;
    }

    $stmt = db()->prepare('SELECT id, username, full_name FROM teachers WHERE id = ?');
    $stmt->execute([$_SESSION['teacher_id']]);
    $teacher = $stmt->fetch();

    return $teacher ?: null;
}

function active_activity(): ?array
{
    $stmt = db()->query("SELECT * FROM activities WHERE status = 'active' ORDER BY id DESC LIMIT 1");
    $activity = $stmt->fetch();

    return $activity ?: null;
}

function activity_by_id(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM activities WHERE id = ?');
    $stmt->execute([$id]);
    $activity = $stmt->fetch();

    return $activity ?: null;
}

function random_token(int $bytes = 16): string
{
    return bin2hex(random_bytes($bytes));
}

function product_image_url(?string $path): string
{
    if ($path) {
        return UPLOAD_URL . '/' . rawurlencode(basename($path));
    }

    return url('public/assets/images/placeholder-food.svg');
}

function selected_activity_id(): ?int
{
    if (!empty($_GET['activity_id'])) {
        $_SESSION['teacher_activity_id'] = (int) $_GET['activity_id'];
    }

    if (!empty($_SESSION['teacher_activity_id'])) {
        return (int) $_SESSION['teacher_activity_id'];
    }

    $active = active_activity();
    if ($active) {
        $_SESSION['teacher_activity_id'] = (int) $active['id'];
        return (int) $active['id'];
    }

    $stmt = db()->query('SELECT id FROM activities ORDER BY id DESC LIMIT 1');
    $activity = $stmt->fetch();
    if ($activity) {
        $_SESSION['teacher_activity_id'] = (int) $activity['id'];
        return (int) $activity['id'];
    }

    return null;
}

function get_or_create_open_cart(int $activityId, int $groupId): array
{
    $stmt = db()->prepare("SELECT * FROM carts WHERE activity_id = ? AND group_id = ? AND status = 'open' ORDER BY id DESC LIMIT 1");
    $stmt->execute([$activityId, $groupId]);
    $cart = $stmt->fetch();
    if ($cart) {
        return $cart;
    }

    $stmt = db()->prepare("INSERT INTO carts (activity_id, group_id, status) VALUES (?, ?, 'open')");
    $stmt->execute([$activityId, $groupId]);

    $stmt = db()->prepare('SELECT * FROM carts WHERE id = ?');
    $stmt->execute([(int) db()->lastInsertId()]);

    return $stmt->fetch();
}

function cart_items(int $cartId): array
{
    $stmt = db()->prepare(
        'SELECT ci.*, p.activity_id AS product_activity_id, p.product_name, p.price, p.image_path, p.is_active
         FROM cart_items ci
         JOIN products p ON p.id = ci.product_id
         WHERE ci.cart_id = ?
         ORDER BY ci.id ASC'
    );
    $stmt->execute([$cartId]);

    return $stmt->fetchAll();
}

function cart_total(array $items): float
{
    $total = 0.0;
    foreach ($items as $item) {
        $total += (float) $item['price'] * (int) $item['quantity'];
    }

    return $total;
}

function student_group(): ?array
{
    if (empty($_SESSION['student_group_id'])) {
        return null;
    }

    $stmt = db()->prepare(
        "SELECT sg.*, a.title AS activity_title, a.status AS activity_status
         FROM student_groups sg
         JOIN activities a ON a.id = sg.activity_id
         WHERE sg.id = ?"
    );
    $stmt->execute([(int) $_SESSION['student_group_id']]);
    $group = $stmt->fetch();

    return $group ?: null;
}

function require_student_group(): array
{
    $group = student_group();
    if (!$group) {
        $_SESSION['student_next'] = $_SERVER['REQUEST_URI'] ?? url('student/home.php');
        redirect('student/join.php');
    }

    return $group;
}

function qr_image_url(string $targetUrl, int $size = 240): string
{
    return url('public/qrcode.php?size=' . (int) $size . '&data=' . rawurlencode($targetUrl));
}

function sort_products_by_thai_name(array &$products): void
{
    static $collator = null;

    if ($collator === null && class_exists(Collator::class)) {
        $collator = new Collator('th_TH');
        $collator->setAttribute(Collator::NUMERIC_COLLATION, Collator::ON);
    }

    usort($products, static function (array $left, array $right) use (&$collator): int {
        $leftName = trim((string) ($left['product_name'] ?? ''));
        $rightName = trim((string) ($right['product_name'] ?? ''));

        if ($collator instanceof Collator) {
            $nameOrder = $collator->compare($leftName, $rightName);
            if ($nameOrder !== false && $nameOrder !== 0) {
                return $nameOrder;
            }
        } else {
            $nameOrder = strnatcasecmp($leftName, $rightName);
            if ($nameOrder !== 0) {
                return $nameOrder;
            }
        }

        return ((int) ($left['sort_order'] ?? 0) <=> (int) ($right['sort_order'] ?? 0))
            ?: ((int) ($left['id'] ?? 0) <=> (int) ($right['id'] ?? 0));
    });
}
