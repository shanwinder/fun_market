<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

require_teacher();
$activityId = selected_activity_id();
$activity = $activityId ? activity_by_id($activityId) : null;

if ($activity && isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="fun-market-report-' . $activity['id'] . '.csv"');
    echo "\xEF\xBB\xBF";
    $out = fopen('php://output', 'w');
    fputcsv($out, ['กลุ่ม', 'เงินตั้งต้น', 'ใช้ไป', 'คงเหลือ', 'จำนวนชิ้น', 'รายการล่าสุด']);
    $stmt = db()->prepare(
        'SELECT sg.group_name, sg.initial_budget, (sg.initial_budget - sg.current_balance) AS spent, sg.current_balance,
            COALESCE(SUM(oi.quantity), 0) AS item_count, MAX(o.created_at) AS latest_at
         FROM student_groups sg
         LEFT JOIN orders o ON o.group_id = sg.id
         LEFT JOIN order_items oi ON oi.order_id = o.id
         WHERE sg.activity_id = ?
         GROUP BY sg.id
         ORDER BY sg.group_name ASC'
    );
    $stmt->execute([$activity['id']]);
    foreach ($stmt->fetchAll() as $row) {
        fputcsv($out, $row);
    }
    exit;
}

$groupSummary = [];
$topProducts = [];

if ($activity) {
    $stmt = db()->prepare(
        'SELECT sg.group_name, sg.initial_budget, (sg.initial_budget - sg.current_balance) AS spent, sg.current_balance,
            COALESCE(SUM(oi.quantity), 0) AS item_count
         FROM student_groups sg
         LEFT JOIN orders o ON o.group_id = sg.id
         LEFT JOIN order_items oi ON oi.order_id = o.id
         WHERE sg.activity_id = ?
         GROUP BY sg.id
         ORDER BY sg.group_name ASC'
    );
    $stmt->execute([$activity['id']]);
    $groupSummary = $stmt->fetchAll();

    $stmt = db()->prepare(
        'SELECT oi.product_name_snapshot, SUM(oi.quantity) AS qty, SUM(oi.subtotal) AS amount
         FROM order_items oi
         JOIN orders o ON o.id = oi.order_id
         WHERE o.activity_id = ?
         GROUP BY oi.product_name_snapshot
         ORDER BY qty DESC, amount DESC
         LIMIT 10'
    );
    $stmt->execute([$activity['id']]);
    $topProducts = $stmt->fetchAll();
}

$pageTitle = 'รายงาน';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title mb-1">รายงาน</h1>
        <p class="text-muted mb-0"><?= $activity ? h($activity['title']) : 'ยังไม่มีกิจกรรม' ?></p>
    </div>
    <div class="d-flex gap-2">
        <?php if ($activity): ?>
            <a class="btn btn-outline-primary fm-btn-icon" href="<?= h(url('teacher/reports.php?export=csv')) ?>"><i data-lucide="download"></i>Export CSV</a>
        <?php endif; ?>
        <button class="btn btn-primary fm-btn-icon" onclick="window.print()"><i data-lucide="printer"></i>พิมพ์รายงาน</button>
    </div>
</div>
<?php if (!$activity): ?>
    <div class="alert alert-info">กรุณาสร้างกิจกรรมก่อน</div>
<?php else: ?>
    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="fm-chart-container h-100">
                <h2 class="h4 fw-bold mb-3">ยอดใช้เงินรายกลุ่ม</h2>
                <canvas id="spentChart" height="180"></canvas>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="panel p-3 h-100">
                <h2 class="h4 fw-bold mb-3">สินค้าที่ถูกซื้อบ่อย</h2>
                <div class="table-responsive">
                    <table class="table align-middle fm-table mb-0">
                        <thead><tr><th>สินค้า</th><th class="text-end">จำนวน</th><th class="text-end">ยอดเงิน</th></tr></thead>
                        <tbody>
                        <?php foreach ($topProducts as $product): ?>
                            <tr>
                                <td><?= h($product['product_name_snapshot']) ?></td>
                                <td class="text-end"><?= (int) $product['qty'] ?></td>
                                <td class="text-end"><?= money($product['amount']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="panel p-3">
        <h2 class="h4 fw-bold mb-3">สรุปรายกลุ่ม</h2>
        <div class="table-responsive">
            <table class="table align-middle fm-table" data-table>
                <thead><tr><th>กลุ่ม</th><th class="text-end">เงินตั้งต้น</th><th class="text-end">ใช้ไป</th><th class="text-end">คงเหลือ</th><th class="text-end">จำนวนชิ้น</th></tr></thead>
                <tbody>
                <?php foreach ($groupSummary as $group): ?>
                    <tr>
                        <td><?= h($group['group_name']) ?></td>
                        <td class="text-end"><?= money($group['initial_budget']) ?></td>
                        <td class="text-end"><?= money($group['spent']) ?></td>
                        <td class="text-end"><?= money($group['current_balance']) ?></td>
                        <td class="text-end"><?= (int) $group['item_count'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        window.addEventListener('load', () => {
            const canvas = document.getElementById('spentChart');
            if (!canvas || !window.Chart) return;
            const ctx = canvas.getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(99, 102, 241, 0.85)');
            gradient.addColorStop(1, 'rgba(99, 102, 241, 0.22)');
            new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: <?= json_encode(array_column($groupSummary, 'group_name'), JSON_UNESCAPED_UNICODE) ?>,
                    datasets: [{
                        label: 'ใช้ไป',
                        data: <?= json_encode(array_map('floatval', array_column($groupSummary, 'spent'))) ?>,
                        backgroundColor: gradient,
                        borderColor: '#6366f1',
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                        hoverBackgroundColor: '#818cf8'
                    }]
                },
                options: {
                    responsive: true,
                    animation: { duration: 1200, easing: 'easeOutQuart' },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            ticks: { font: { family: 'Noto Sans Thai', weight: 500 } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'Noto Sans Thai', weight: 600 } }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            cornerRadius: 8,
                            titleFont: { family: 'Noto Sans Thai', weight: 700 },
                            bodyFont: { family: 'Noto Sans Thai' },
                            padding: 12
                        }
                    }
                }
            });
        });
    </script>
<?php endif; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
