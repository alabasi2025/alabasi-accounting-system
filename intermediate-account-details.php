<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'admin';
    $_SESSION['role'] = 'admin';
}

$pageTitle = 'تفاصيل الحساب الوسيط';

// الحصول على معرف الحساب
$accountId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($accountId === 0) {
    header('Location: intermediate-accounts-list.php');
    exit;
}

// الحصول على تفاصيل الحساب
$account = $pdo->prepare("SELECT * FROM intermediate_accounts WHERE id = ?");
$account->execute([$accountId]);
$account = $account->fetch();

if (!$account) {
    header('Location: intermediate-accounts-list.php');
    exit;
}

// الحصول على الإحصائيات
$stats = [];
try {
    $stmt = $pdo->prepare("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'transferred' THEN 1 ELSE 0 END) as transferred,
        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
        SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END) as pendingAmount,
        SUM(CASE WHEN status = 'transferred' THEN amount ELSE 0 END) as transferredAmount
    FROM intermediate_account_transactions 
    WHERE intermediateAccountId = ?");
    $stmt->execute([$accountId]);
    $stats = $stmt->fetch();
} catch (Exception $e) {
    $stats = [
        'total' => 0,
        'pending' => 0,
        'transferred' => 0,
        'rejected' => 0,
        'cancelled' => 0,
        'pendingAmount' => 0,
        'transferredAmount' => 0
    ];
}

// الحصول على العمليات مع الفلاتر
$search = isset($_GET['search']) ? $_GET['search'] : '';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$typeFilter = isset($_GET['type']) ? $_GET['type'] : '';
$transferTypeFilter = isset($_GET['transferType']) ? $_GET['transferType'] : '';

$whereConditions = ["intermediateAccountId = ?"];
$params = [$accountId];

if (!empty($search)) {
    $whereConditions[] = "(voucherNumber LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($statusFilter)) {
    $whereConditions[] = "status = ?";
    $params[] = $statusFilter;
}

if (!empty($typeFilter)) {
    $whereConditions[] = "voucherType = ?";
    $params[] = $typeFilter;
}

if (!empty($transferTypeFilter)) {
    $whereConditions[] = "transferType = ?";
    $params[] = $transferTypeFilter;
}

$whereClause = implode(' AND ', $whereConditions);

$transactions = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM intermediate_account_transactions 
        WHERE $whereClause 
        ORDER BY voucherDate DESC, id DESC");
    $stmt->execute($params);
    $transactions = $stmt->fetchAll();
} catch (Exception $e) {
    $transactions = [];
}

require_once 'includes/header.php';
?>

<div class="page-header">
    <h1>📊 تفاصيل الحساب الوسيط</h1>
    <a href="intermediate-accounts-list.php" class="btn btn-secondary">⬅️ العودة للقائمة</a>
</div>

<div class="account-info">
    <h3>معلومات الحساب</h3>
    <p><strong>رقم الحساب:</strong> <?= $account['accountNumber'] ?></p>
    <p><strong>نوع الكيان:</strong> <?= $account['entityType'] == 'unit' ? '🏛️ وحدة' : '🏢 مؤسسة' ?></p>
    <p><strong>رقم الكيان:</strong> <?= $account['entityId'] ?></p>
    <p><strong>الرصيد الحالي:</strong> <?= number_format($account['balance'], 2) ?> ريال</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">⏳</div>
        <div class="stat-content">
            <div class="stat-value"><?= $stats['pending'] ?? 0 ?></div>
            <div class="stat-label">معلق (<?= number_format($stats['pendingAmount'] ?? 0, 2) ?> ريال)</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-content">
            <div class="stat-value"><?= $stats['transferred'] ?? 0 ?></div>
            <div class="stat-label">مُرحّل (<?= number_format($stats['transferredAmount'] ?? 0, 2) ?> ريال)</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">❌</div>
        <div class="stat-content">
            <div class="stat-value"><?= $stats['rejected'] ?? 0 ?></div>
            <div class="stat-label">مرفوض</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">🚫</div>
        <div class="stat-content">
            <div class="stat-value"><?= $stats['cancelled'] ?? 0 ?></div>
            <div class="stat-label">ملغي</div>
        </div>
    </div>
</div>

<div class="filters-section">
    <h3>🔍 البحث والفلاتر</h3>
    <form method="GET" action="">
        <input type="hidden" name="id" value="<?= $accountId ?>">
        
        <div class="filter-row">
            <input type="text" name="search" placeholder="ابحث برقم السند أو الوصف..." value="<?= htmlspecialchars($search) ?>">
            
            <select name="status">
                <option value="">جميع الحالات</option>
                <option value="pending" <?= $statusFilter == 'pending' ? 'selected' : '' ?>>معلق</option>
                <option value="transferred" <?= $statusFilter == 'transferred' ? 'selected' : '' ?>>مُرحّل</option>
                <option value="rejected" <?= $statusFilter == 'rejected' ? 'selected' : '' ?>>مرفوض</option>
                <option value="cancelled" <?= $statusFilter == 'cancelled' ? 'selected' : '' ?>>ملغي</option>
            </select>
            
            <select name="type">
                <option value="">جميع الأنواع</option>
                <option value="payment" <?= $typeFilter == 'payment' ? 'selected' : '' ?>>سند صرف</option>
                <option value="receipt" <?= $typeFilter == 'receipt' ? 'selected' : '' ?>>سند قبض</option>
                <option value="journal_entry" <?= $typeFilter == 'journal_entry' ? 'selected' : '' ?>>قيد يومي</option>
            </select>
            
            <select name="transferType">
                <option value="">جميع أنواع الترحيل</option>
                <option value="unit_to_unit" <?= $transferTypeFilter == 'unit_to_unit' ? 'selected' : '' ?>>وحدة → وحدة</option>
                <option value="company_to_company" <?= $transferTypeFilter == 'company_to_company' ? 'selected' : '' ?>>مؤسسة → مؤسسة</option>
                <option value="unit_to_company" <?= $transferTypeFilter == 'unit_to_company' ? 'selected' : '' ?>>وحدة → مؤسسة</option>
                <option value="company_to_unit" <?= $transferTypeFilter == 'company_to_unit' ? 'selected' : '' ?>>مؤسسة → وحدة</option>
            </select>
            
            <button type="submit" class="btn btn-primary">🔍 بحث</button>
            <a href="?id=<?= $accountId ?>" class="btn btn-secondary">❌ إعادة تعيين</a>
        </div>
    </form>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>رقم السند</th>
                <th>التاريخ</th>
                <th>النوع</th>
                <th>الترحيل</th>
                <th>المبلغ</th>
                <th>الحالة</th>
                <th>الأولوية</th>
                <th>الوصف</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($transactions)): ?>
                <tr>
                    <td colspan="8" class="text-center">لا توجد عمليات</td>
                </tr>
            <?php else: ?>
                <?php foreach ($transactions as $trans): ?>
                    <tr>
                        <td><?= htmlspecialchars($trans['voucherNumber']) ?></td>
                        <td><?= date('Y-m-d', strtotime($trans['voucherDate'])) ?></td>
                        <td>
                            <?php
                            $typeLabels = [
                                'payment' => '💸 سند صرف',
                                'receipt' => '💰 سند قبض',
                                'journal_entry' => '📝 قيد يومي'
                            ];
                            echo $typeLabels[$trans['voucherType']] ?? $trans['voucherType'];
                            ?>
                        </td>
                        <td>
                            <?php
                            $transferLabels = [
                                'unit_to_unit' => '🏛️→🏛️ وحدة→وحدة',
                                'company_to_company' => '🏢→🏢 مؤسسة→مؤسسة',
                                'unit_to_company' => '🏛️→🏢 وحدة→مؤسسة',
                                'company_to_unit' => '🏢→🏛️ مؤسسة→وحدة'
                            ];
                            echo $transferLabels[$trans['transferType']] ?? $trans['transferType'];
                            ?>
                        </td>
                        <td><?= number_format($trans['amount'], 2) ?> ريال</td>
                        <td>
                            <?php
                            $statusBadges = [
                                'pending' => '<span class="badge badge-warning">⏳ معلق</span>',
                                'transferred' => '<span class="badge badge-success">✅ مُرحّل</span>',
                                'rejected' => '<span class="badge badge-danger">❌ مرفوض</span>',
                                'cancelled' => '<span class="badge badge-secondary">🚫 ملغي</span>'
                            ];
                            echo $statusBadges[$trans['status']] ?? $trans['status'];
                            ?>
                        </td>
                        <td>
                            <?php
                            $priorityBadges = [
                                'low' => '<span class="badge badge-info">🔵 منخفض</span>',
                                'medium' => '<span class="badge badge-warning">🟡 متوسط</span>',
                                'high' => '<span class="badge badge-danger">🔴 عالي</span>',
                                'urgent' => '<span class="badge badge-danger">🚨 عاجل</span>'
                            ];
                            echo $priorityBadges[$trans['priority']] ?? '';
                            ?>
                        </td>
                        <td><?= htmlspecialchars($trans['description']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
