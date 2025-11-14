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
    
    // الحسابات الوسيطة
    $intermediateCount = $pdo->query("SELECT COUNT(*) FROM intermediate_accounts")->fetchColumn();
    $intermediateBalance = $pdo->query("SELECT SUM(balance) FROM intermediate_accounts")->fetchColumn();
    
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
    $intermediateCount = 0;
    $intermediateBalance = 0;
    $recentJournals = [];
}

include 'includes/header.php';
?>

<link rel="stylesheet" href="css/professional.css">

<div class="page-wrapper">
    <!-- رأس الصفحة -->
    <div class="page-header-pro">
        <h1>
            <span>🏠</span>
            <span>لوحة التحكم</span>
        </h1>
        <p class="subtitle">نظرة عامة شاملة على نظام الأباسي المحاسبي الموحد</p>
        <div class="breadcrumb-nav">
            <span>🏠 الرئيسية</span>
            <span>›</span>
            <span>لوحة التحكم</span>
        </div>
    </div>

    <?php
    $message = getMessage();
    if ($message):
    ?>
        <div class="alert-pro alert-<?php echo $message['type']; ?>-pro">
            <span class="alert-icon">
                <?php 
                    echo $message['type'] == 'success' ? '✅' : 
                         ($message['type'] == 'danger' ? '⚠️' : 
                         ($message['type'] == 'warning' ? '⚡' : 'ℹ️'));
                ?>
            </span>
            <div><?php echo $message['message']; ?></div>
        </div>
    <?php endif; ?>

    <!-- بطاقات الإحصائيات الرئيسية -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-header">
                <h3 class="stat-card-title">إجمالي الحسابات</h3>
                <div class="stat-card-icon">📊</div>
            </div>
            <p class="stat-card-value"><?php echo number_format($accountsCount); ?></p>
            <div class="stat-card-footer">
                <a href="accounts.php" style="color: inherit; text-decoration: none;">
                    عرض دليل الحسابات ←
                </a>
            </div>
        </div>
        
        <div class="stat-card success">
            <div class="stat-card-header">
                <h3 class="stat-card-title">القيود اليومية</h3>
                <div class="stat-card-icon">📝</div>
            </div>
            <p class="stat-card-value"><?php echo number_format($journalsCount); ?></p>
            <div class="stat-card-footer">
                <?php echo $postedJournals; ?> مرحّل | <?php echo $draftJournals; ?> مسودة
            </div>
        </div>
        
        <div class="stat-card info">
            <div class="stat-card-header">
                <h3 class="stat-card-title">المستخدمين</h3>
                <div class="stat-card-icon">👥</div>
            </div>
            <p class="stat-card-value"><?php echo number_format($usersCount); ?></p>
            <div class="stat-card-footer">
                <?php echo $activeUsers; ?> مستخدم نشط
            </div>
        </div>
        
        <div class="stat-card warning">
            <div class="stat-card-header">
                <h3 class="stat-card-title">الفروع</h3>
                <div class="stat-card-icon">🏢</div>
            </div>
            <p class="stat-card-value"><?php echo number_format($branchesCount); ?></p>
            <div class="stat-card-footer">
                <a href="branches.php" style="color: inherit; text-decoration: none;">
                    إدارة الفروع ←
                </a>
            </div>
        </div>
    </div>

    <!-- الصف الثاني من الإحصائيات -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-header">
                <h3 class="stat-card-title">الأصول</h3>
                <div class="stat-card-icon">💰</div>
            </div>
            <p class="stat-card-value"><?php echo number_format($assetsCount); ?></p>
            <div class="stat-card-footer">حساب أصول</div>
        </div>
        
        <div class="stat-card danger">
            <div class="stat-card-header">
                <h3 class="stat-card-title">الخصوم</h3>
                <div class="stat-card-icon">📉</div>
            </div>
            <p class="stat-card-value"><?php echo number_format($liabilitiesCount); ?></p>
            <div class="stat-card-footer">حساب خصوم</div>
        </div>
        
        <div class="stat-card success">
            <div class="stat-card-header">
                <h3 class="stat-card-title">الإيرادات</h3>
                <div class="stat-card-icon">📈</div>
            </div>
            <p class="stat-card-value"><?php echo number_format($revenueCount); ?></p>
            <div class="stat-card-footer">حساب إيرادات</div>
        </div>
        
        <div class="stat-card warning">
            <div class="stat-card-header">
                <h3 class="stat-card-title">المصروفات</h3>
                <div class="stat-card-icon">💸</div>
            </div>
            <p class="stat-card-value"><?php echo number_format($expenseCount); ?></p>
            <div class="stat-card-footer">حساب مصروفات</div>
        </div>
    </div>

    <!-- بطاقات معلوماتية -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <!-- الحسابات التحليلية -->
        <div class="card-pro">
            <div class="card-header-pro">
                <h2>👥 الحسابات التحليلية</h2>
            </div>
            <div class="card-body-pro">
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f9fafb; border-radius: 8px;">
                        <span style="font-weight: 600; color: #374151;">إجمالي الحسابات</span>
                        <span style="font-size: 24px; font-weight: 700; color: #667eea;"><?php echo number_format($analyticalCount); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f9fafb; border-radius: 8px;">
                        <span style="font-weight: 600; color: #374151;">العملاء</span>
                        <span style="font-size: 20px; font-weight: 700; color: #10b981;"><?php echo number_format($customersCount); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f9fafb; border-radius: 8px;">
                        <span style="font-weight: 600; color: #374151;">الموردين</span>
                        <span style="font-size: 20px; font-weight: 700; color: #f59e0b;"><?php echo number_format($suppliersCount); ?></span>
                    </div>
                </div>
            </div>
            <div class="card-footer-pro">
                <a href="analytical-accounts.php" class="btn-pro btn-outline-pro btn-sm">
                    <span>عرض الكل</span>
                    <span>←</span>
                </a>
            </div>
        </div>

        <!-- الحسابات الوسيطة -->
        <div class="card-pro">
            <div class="card-header-pro">
                <h2>🔄 الحسابات الوسيطة</h2>
            </div>
            <div class="card-body-pro">
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f9fafb; border-radius: 8px;">
                        <span style="font-weight: 600; color: #374151;">عدد الحسابات</span>
                        <span style="font-size: 24px; font-weight: 700; color: #667eea;"><?php echo number_format($intermediateCount); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f9fafb; border-radius: 8px;">
                        <span style="font-weight: 600; color: #374151;">إجمالي الرصيد</span>
                        <span style="font-size: 20px; font-weight: 700; color: #10b981;"><?php echo number_format($intermediateBalance, 2); ?></span>
                    </div>
                    <div style="padding: 15px; background: #e0f2fe; border-radius: 8px; text-align: center;">
                        <span style="font-size: 14px; color: #1e40af;">نظام ذكي لإدارة الحسابات بين الوحدات</span>
                    </div>
                </div>
            </div>
            <div class="card-footer-pro">
                <a href="intermediate-accounts-list.php" class="btn-pro btn-outline-pro btn-sm">
                    <span>عرض الكل</span>
                    <span>←</span>
                </a>
            </div>
        </div>
    </div>

    <!-- آخر القيود -->
    <?php if (count($recentJournals) > 0): ?>
    <div class="table-container-pro">
        <div class="table-header-pro">
            <h3>📋 آخر القيود اليومية</h3>
            <a href="journals.php" class="btn-pro btn-primary-pro btn-sm">
                <span>عرض الكل</span>
                <span>←</span>
            </a>
        </div>
        <div class="table-responsive">
            <table class="table-pro">
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
                        <td><strong><?php echo htmlspecialchars($journal['journalNumber']); ?></strong></td>
                        <td><?php echo date('Y-m-d', strtotime($journal['date'])); ?></td>
                        <td><?php echo htmlspecialchars(substr($journal['description'], 0, 50)); ?>...</td>
                        <td style="color: #10b981; font-weight: 600;"><?php echo number_format($journal['totalDebit'], 2); ?></td>
                        <td style="color: #ef4444; font-weight: 600;"><?php echo number_format($journal['totalCredit'], 2); ?></td>
                        <td>
                            <span class="badge-pro badge-<?php echo $journal['status'] == 'posted' ? 'success' : 'warning'; ?>">
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
    <?php else: ?>
    <div class="info-box-pro">
        <div class="info-box-title">
            <span>ℹ️</span>
            <span>لا توجد قيود يومية</span>
        </div>
        <div class="info-box-content">
            <p>لم يتم إضافة أي قيود يومية بعد. ابدأ بإضافة قيد جديد من قسم القيود اليومية.</p>
        </div>
    </div>
    <?php endif; ?>

    <!-- روابط سريعة -->
    <div class="card-pro">
        <div class="card-header-pro">
            <h2>⚡ روابط سريعة</h2>
        </div>
        <div class="card-body-pro">
            <div class="btn-group">
                <a href="journals.php" class="btn-pro btn-primary-pro">
                    <span>📝</span>
                    <span>إضافة قيد يومي</span>
                </a>
                <a href="payment-vouchers.php" class="btn-pro btn-danger-pro">
                    <span>💸</span>
                    <span>سند صرف</span>
                </a>
                <a href="receipt-vouchers.php" class="btn-pro btn-success-pro">
                    <span>💰</span>
                    <span>سند قبض</span>
                </a>
                <a href="intermediate-account-add.php" class="btn-pro btn-info-pro">
                    <span>➕</span>
                    <span>حساب وسيط جديد</span>
                </a>
                <a href="reports.php" class="btn-pro btn-secondary-pro">
                    <span>📊</span>
                    <span>التقارير المالية</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
