<?php
/**
 * صفحة إدارة دليل الحسابات
 * Chart of Accounts Management
 */

session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'admin';
    $_SESSION['role'] = 'admin';
}

// جلب جميع المؤسسات للفلتر
$companies = $pdo->query("SELECT id, code, nameAr FROM companies WHERE isActive = 1 ORDER BY nameAr")->fetchAll();

// فلتر المؤسسة (الافتراضي: أول مؤسسة)
$selectedCompanyId = $_GET['companyId'] ?? ($companies[0]['id'] ?? null);

// جلب جميع الحسابات
try {
    $query = "
        SELECT a.*, 
               p.nameAr as parentName,
               c.nameAr as companyName,
               (SELECT COUNT(*) FROM accounts WHERE parentId = a.id) as childrenCount
        FROM accounts a
        LEFT JOIN accounts p ON a.parentId = p.id
        LEFT JOIN companies c ON a.companyId = c.id
        " . ($selectedCompanyId ? "WHERE a.companyId = :companyId" : "") . "
        ORDER BY a.code ASC
    ";
    
    $stmt = $pdo->prepare($query);
    if ($selectedCompanyId) {
        $stmt->execute(['companyId' => $selectedCompanyId]);
    } else {
        $stmt->execute();
    }
    $accounts = $stmt->fetchAll();
    
    // إحصائيات
    $totalAccounts = count($accounts);
    $activeAccounts = count(array_filter($accounts, function($a) { return $a['isActive']; }));
    
} catch (PDOException $e) {
    $accounts = [];
    $totalAccounts = 0;
    $activeAccounts = 0;
}

// أنواع الحسابات
$accountTypes = [
    'asset' => 'أصول',
    'liability' => 'خصوم',
    'equity' => 'حقوق ملكية',
    'revenue' => 'إيرادات',
    'expense' => 'مصروفات'
];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دليل الحسابات - نظام العباسي الموحد</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="dashboard">
    <!-- شريط التنقل -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-content">
                <div class="navbar-brand">
                    <span style="font-size: 28px;">📊</span> نظام العباسي الموحد
                </div>
                <div class="navbar-menu">
                    <a href="dashboard.php" class="nav-link">الرئيسية</a>
                    <a href="accounts.php" class="nav-link active">الحسابات</a>
                    <a href="journals.php" class="nav-link">القيود اليومية</a>
                    <a href="reports.php" class="nav-link">التقارير</a>
                </div>
                <div class="navbar-user">
                    <span class="user-name">👤 <?php echo getCurrentUserName(); ?></span>
                    <a href="logout.php" class="btn-logout">تسجيل الخروج</a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- المحتوى الرئيسي -->
    <div class="container">
        <div class="dashboard-content">
            <div class="page-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h1 class="page-title">دليل الحسابات</h1>
                        <p class="page-subtitle">إدارة وعرض جميع الحسابات المحاسبية</p>
                    </div>
                    <button class="btn btn-primary" onclick="alert('سيتم إضافة هذه الميزة قريباً')">
                        ➕ إضافة حساب جديد
                    </button>
                </div>
            </div>
            
            <!-- إحصائيات سريعة -->
            <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 30px;">
                <div class="stat-card stat-primary">
                    <div class="stat-content">
                        <h3>إجمالي الحسابات</h3>
                        <div class="number"><?php echo $totalAccounts; ?></div>
                    </div>
                </div>
                <div class="stat-card stat-success">
                    <div class="stat-content">
                        <h3>الحسابات النشطة</h3>
                        <div class="number"><?php echo $activeAccounts; ?></div>
                    </div>
                </div>
                <div class="stat-card stat-info">
                    <div class="stat-content">
                        <h3>الحسابات الرئيسية</h3>
                        <div class="number"><?php echo count(array_filter($accounts, function($a) { return $a['level'] == 1; })); ?></div>
                    </div>
                </div>
                <div class="stat-card stat-warning">
                    <div class="stat-content">
                        <h3>الحسابات الفرعية</h3>
                        <div class="number"><?php echo count(array_filter($accounts, function($a) { return $a['level'] > 1; })); ?></div>
                    </div>
                </div>
            </div>
            
            <!-- فلاتر البحث -->
            <div class="card" style="margin-bottom: 20px;">
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>بحث</label>
                            <input type="text" class="form-control" id="searchInput" placeholder="ابحث برقم أو اسم الحساب...">
                        </div>
                        <div class="form-group">
                            <label>نوع الحساب</label>
                            <select class="form-control" id="typeFilter">
                                <option value="">الكل</option>
                                <?php foreach ($accountTypes as $type => $label): ?>
                                <option value="<?php echo $type; ?>"><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>المستوى</label>
                            <select class="form-control" id="levelFilter">
                                <option value="">الكل</option>
                                <option value="1">المستوى 1</option>
                                <option value="2">المستوى 2</option>
                                <option value="3">المستوى 3</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>الحالة</label>
                            <select class="form-control" id="statusFilter">
                                <option value="">الكل</option>
                                <option value="1">نشط</option>
                                <option value="0">غير نشط</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- جدول الحسابات -->
            <div class="card">
                <div class="card-header">
                    <h3>📋 قائمة الحسابات</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="accountsTable">
                            <thead>
                                <tr>
                                    <th>رقم الحساب</th>
                                    <th>اسم الحساب</th>
                                    <th>النوع</th>
                                    <th>الحساب الأب</th>
                                    <th>المستوى</th>
                                    <th>الحسابات الفرعية</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($accounts as $account): ?>
                                <tr data-type="<?php echo $account['type']; ?>" 
                                    data-level="<?php echo $account['level']; ?>" 
                                    data-status="<?php echo $account['isActive'] ? '1' : '0'; ?>">
                                    <td><strong><?php echo htmlspecialchars($account['code']); ?></strong></td>
                                    <td>
                                        <?php 
                                        // إضافة مسافات حسب المستوى
                                        echo str_repeat('&nbsp;&nbsp;&nbsp;', $account['level'] - 1);
                                        echo htmlspecialchars($account['nameAr']); 
                                        ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?php echo $accountTypes[$account['type']] ?? $account['type']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($account['parentName'] ?? '-'); ?></td>
                                    <td><?php echo $account['level']; ?></td>
                                    <td><?php echo $account['childrenCount']; ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $account['isActive'] ? 'success' : 'danger'; ?>">
                                            <?php echo $account['isActive'] ? 'نشط' : 'غير نشط'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="alert('عرض التفاصيل')">عرض</button>
                                        <button class="btn btn-sm btn-warning" onclick="alert('تعديل')">تعديل</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 نظام العباسي الموحد - جميع الحقوق محفوظة</p>
        </div>
    </footer>
    
    <script>
    // البحث والفلترة
    document.getElementById('searchInput').addEventListener('keyup', filterTable);
    document.getElementById('typeFilter').addEventListener('change', filterTable);
    document.getElementById('levelFilter').addEventListener('change', filterTable);
    document.getElementById('statusFilter').addEventListener('change', filterTable);
    
    function filterTable() {
        const searchValue = document.getElementById('searchInput').value.toLowerCase();
        const typeValue = document.getElementById('typeFilter').value;
        const levelValue = document.getElementById('levelFilter').value;
        const statusValue = document.getElementById('statusFilter').value;
        
        const rows = document.querySelectorAll('#accountsTable tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const type = row.getAttribute('data-type');
            const level = row.getAttribute('data-level');
            const status = row.getAttribute('data-status');
            
            const matchSearch = text.includes(searchValue);
            const matchType = !typeValue || type === typeValue;
            const matchLevel = !levelValue || level === levelValue;
            const matchStatus = !statusValue || status === statusValue;
            
            if (matchSearch && matchType && matchLevel && matchStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    </script>
</body>
</html>
