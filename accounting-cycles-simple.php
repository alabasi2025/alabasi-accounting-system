<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'admin';
    $_SESSION['role'] = 'admin';
}

$pageTitle = 'الدورات المحاسبية';

// الحصول على إحصائيات الدورات
$stats = [];
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM account_cycles");
    $stats['total'] = $stmt->fetch()['total'] ?? 0;
    
    $stmt = $pdo->query("SELECT COUNT(*) as open FROM account_cycles WHERE status = 'open'");
    $stats['open'] = $stmt->fetch()['open'] ?? 0;
    
    $stmt = $pdo->query("SELECT COUNT(*) as closed FROM account_cycles WHERE status = 'closed'");
    $stats['closed'] = $stmt->fetch()['closed'] ?? 0;
} catch (Exception $e) {
    $stats = ['total' => 0, 'open' => 0, 'closed' => 0];
}

// الحصول على قائمة الدورات
$cycles = [];
try {
    $stmt = $pdo->query("SELECT * FROM account_cycles ORDER BY startDate DESC");
    $cycles = $stmt->fetchAll();
} catch (Exception $e) {
    $cycles = [];
}

require_once 'includes/header.php';
?>

<div class="page-header">
    <h1>📅 الدورات المحاسبية</h1>
    <button class="btn btn-primary" onclick="window.location.href='accounting-cycles-add.php'">
        ➕ إضافة دورة جديدة
    </button>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">📊</div>
        <div class="stat-content">
            <div class="stat-value"><?= $stats['total'] ?></div>
            <div class="stat-label">إجمالي الدورات</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">🟢</div>
        <div class="stat-content">
            <div class="stat-value"><?= $stats['open'] ?></div>
            <div class="stat-label">دورات مفتوحة</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">🔴</div>
        <div class="stat-content">
            <div class="stat-value"><?= $stats['closed'] ?></div>
            <div class="stat-label">دورات مقفلة</div>
        </div>
    </div>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>اسم الدورة</th>
                <th>تاريخ البداية</th>
                <th>تاريخ النهاية</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($cycles)): ?>
                <tr>
                    <td colspan="6" class="text-center">لا توجد دورات محاسبية</td>
                </tr>
            <?php else: ?>
                <?php foreach ($cycles as $cycle): ?>
                    <tr>
                        <td><?= $cycle['id'] ?></td>
                        <td><?= htmlspecialchars($cycle['nameAr']) ?></td>
                        <td><?= date('Y-m-d', strtotime($cycle['startDate'])) ?></td>
                        <td><?= date('Y-m-d', strtotime($cycle['endDate'])) ?></td>
                        <td>
                            <?php if ($cycle['status'] == 'open'): ?>
                                <span class="badge badge-success">مفتوحة</span>
                            <?php else: ?>
                                <span class="badge badge-danger">مقفلة</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="alert('عرض التفاصيل قريباً')">
                                👁️ عرض
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
