<?php
/**
 * لوحة التحكم الرئيسية
 * Main Dashboard
 */

require_once 'includes/db.php';
require_once 'includes/functions.php';

// التحقق من تسجيل الدخول
requireLogin();

$pageTitle = 'لوحة التحكم';

// جلب إحصائيات مفصلة
try {
    // عدد الحسابات حسب النوع
    $assetsCount = $pdo->query("SELECT COUNT(*) FROM accounts WHERE type = 'asset'")->fetchColumn();
    $liabilitiesCount = $pdo->query("SELECT COUNT(*) FROM accounts WHERE type = 'liability'")->fetchColumn();
    $revenueCount = $pdo->query("SELECT COUNT(*) FROM accounts WHERE type = 'revenue'")->fetchColumn();
    $expenseCount = $pdo->query("SELECT COUNT(*) FROM accounts WHERE type = 'expense'")->fetchColumn();
    
    // إجمالي الحسابات
    $accountsCount = $pdo->query("SELECT COUNT(*) FROM accounts")->fetchColumn();
    
    // عدد القيود
    $journalsCount = $pdo->query("SELECT COUNT(*) FROM journals")->fetchColumn();
    $postedJournals = $pdo->query("SELECT COUNT(*) FROM journals WHERE status = 'posted'")->fetchColumn();
    $draftJournals = $pdo->query("SELECT COUNT(*) FROM journals WHERE status = 'draft'")->fetchColumn();
    
    // عدد المستخدمين
    $usersCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $activeUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE isActive = 1")->fetchColumn();
    
    // عدد الفروع
    $branchesCount = $pdo->query("SELECT COUNT(*) FROM branches")->fetchColumn();
    
    // عدد الحسابات التحليلية
    $analyticalCount = $pdo->query("SELECT COUNT(*) FROM analyticalAccounts")->fetchColumn();
    $customersCount = $pdo->query("SELECT COUNT(*) FROM analyticalAccounts WHERE type = 'customer'")->fetchColumn();
    $suppliersCount = $pdo->query("SELECT COUNT(*) FROM analyticalAccounts WHERE type = 'supplier'")->fetchColumn();
    
    // آخر القيود
    $recentJournalsResult = $pdo->query("
        SELECT j.*, u.nameAr as createdByName 
        FROM journals j 
        LEFT JOIN users u ON j.createdBy = u.id 
        ORDER BY j.createdAt DESC 
        LIMIT 5
    ");
    $recentJournals = $recentJournalsResult ? $recentJournalsResult->fetchAll() : [];
    
} catch (PDOException $e) {
    $accountsCount = 0;
    $usersCount = 0;
    $journalsCount = 0;
    $branchesCount = 0;
    $assetsCount = 0;
    $liabilitiesCount = 0;
    $revenueCount = 0;
    $expenseCount = 0;
    $postedJournals = 0;
    $draftJournals = 0;
    $activeUsers = 0;
    $analyticalCount = 0;
    $customersCount = 0;
    $suppliersCount = 0;
    $recentJournals = [];
}

include 'includes/header.php';
?>

<div class="page-header">
    <p class="page-subtitle">نظرة عامة على النظام المحاسبي</p>
</div>

<?php
$message = getMessage();
if ($message):
?>
    <div class="alert alert-<?php echo $message['type']; ?>">
        <?php echo $message['message']; ?>
    </div>
<?php endif; ?>

<!-- بطاقات الإحصائيات الرئيسية -->
<div class="stats-grid">
    <div class="stat-card stat-primary">
        <div class="stat-icon">📊</div>
        <div class="stat-content">
            <h3>إجمالي الحسابات</h3>
            <div class="number"><?php echo $accountsCount; ?></div>
            <p class="stat-detail">حساب نشط</p>
        </div>
    </div>
    
    <div class="stat-card stat-success">
        <div class="stat-icon">📝</div>
        <div class="stat-content">
            <h3>القيود اليومية</h3>
            <div class="number"><?php echo $journalsCount; ?></div>
            <p class="stat-detail"><?php echo $postedJournals; ?> مرحّل | <?php echo $draftJournals; ?> مسودة</p>
        </div>
    </div>
    
    <div class="stat-card stat-info">
        <div class="stat-icon">👥</div>
        <div class="stat-content">
            <h3>المستخدمين</h3>
            <div class="number"><?php echo $usersCount; ?></div>
            <p class="stat-detail"><?php echo $activeUsers; ?> نشط</p>
        </div>
    </div>
    
    <div class="stat-card stat-warning">
        <div class="stat-icon">🏢</div>
        <div class="stat-content">
            <h3>الفروع</h3>
            <div class="number"><?php echo $branchesCount; ?></div>
            <p class="stat-detail">فرع مسجل</p>
        </div>
    </div>
</div>

<!-- إحصائيات تفصيلية -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>📈 توزيع الحسابات</h3>
            </div>
            <div class="card-body">
                <div class="account-stats">
                    <div class="account-stat-item">
                        <span class="label">الأصول</span>
                        <span class="value"><?php echo $assetsCount; ?></span>
                    </div>
                    <div class="account-stat-item">
                        <span class="label">الخصوم</span>
                        <span class="value"><?php echo $liabilitiesCount; ?></span>
                    </div>
                    <div class="account-stat-item">
                        <span class="label">الإيرادات</span>
                        <span class="value"><?php echo $revenueCount; ?></span>
                    </div>
                    <div class="account-stat-item">
                        <span class="label">المصروفات</span>
                        <span class="value"><?php echo $expenseCount; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>👥 الحسابات التحليلية</h3>
            </div>
            <div class="card-body">
                <div class="account-stats">
                    <div class="account-stat-item">
                        <span class="label">إجمالي الحسابات التحليلية</span>
                        <span class="value"><?php echo $analyticalCount; ?></span>
                    </div>
                    <div class="account-stat-item">
                        <span class="label">العملاء</span>
                        <span class="value"><?php echo $customersCount; ?></span>
                    </div>
                    <div class="account-stat-item">
                        <span class="label">الموردين</span>
                        <span class="value"><?php echo $suppliersCount; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- آخر القيود -->
<?php if (count($recentJournals) > 0): ?>
<div class="card">
    <div class="card-header">
        <h3>📋 آخر القيود اليومية</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>رقم القيد</th>
                        <th>التاريخ</th>
                        <th>البيان</th>
                        <th>المدين</th>
                        <th>الدائن</th>
                        <th>الحالة</th>
                        <th>المستخدم</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentJournals as $journal): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($journal['journalNumber']); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($journal['date'])); ?></td>
                        <td><?php echo htmlspecialchars(substr($journal['description'], 0, 50)); ?>...</td>
                        <td><?php echo number_format($journal['totalDebit'], 2); ?></td>
                        <td><?php echo number_format($journal['totalCredit'], 2); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $journal['status'] == 'posted' ? 'success' : 'warning'; ?>">
                                <?php echo $journal['status'] == 'posted' ? 'مرحّل' : 'مسودة'; ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($journal['createdByName'] ?? 'غير معروف'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
