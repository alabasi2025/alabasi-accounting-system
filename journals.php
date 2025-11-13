<?php
/**
 * صفحة إدارة القيود اليومية
 * Journal Entries Management
 */

require_once 'includes/db.php';
require_once 'includes/functions.php';

// التحقق من تسجيل الدخول
requireLogin();

// جلب جميع القيود
try {
    $journals = $pdo->query("
        SELECT j.*, u.nameAr as createdByName
        FROM journals j
        LEFT JOIN users u ON j.createdBy = u.id
        ORDER BY j.date DESC, j.createdAt DESC
    ")->fetchAll();
    
    // إحصائيات
    $totalJournals = count($journals);
    $postedJournals = count(array_filter($journals, function($j) { return $j['status'] == 'posted'; }));
    $draftJournals = count(array_filter($journals, function($j) { return $j['status'] == 'draft'; }));
    
    // إجمالي المبالغ
    $totalDebit = array_sum(array_column($journals, 'totalDebit'));
    $totalCredit = array_sum(array_column($journals, 'totalCredit'));
    
} catch (PDOException $e) {
    $journals = [];
    $totalJournals = 0;
    $postedJournals = 0;
    $draftJournals = 0;
    $totalDebit = 0;
    $totalCredit = 0;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>القيود اليومية - نظام العباسي الموحد</title>
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
                    <a href="accounts.php" class="nav-link">الحسابات</a>
                    <a href="journals.php" class="nav-link active">القيود اليومية</a>
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
                        <h1 class="page-title">القيود اليومية</h1>
                        <p class="page-subtitle">إدارة وعرض جميع القيود المحاسبية</p>
                    </div>
                    <button class="btn btn-primary" onclick="alert('سيتم إضافة هذه الميزة قريباً')">
                        ➕ إضافة قيد جديد
                    </button>
                </div>
            </div>
            
            <!-- إحصائيات سريعة -->
            <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 30px;">
                <div class="stat-card stat-primary">
                    <div class="stat-content">
                        <h3>إجمالي القيود</h3>
                        <div class="number"><?php echo $totalJournals; ?></div>
                    </div>
                </div>
                <div class="stat-card stat-success">
                    <div class="stat-content">
                        <h3>القيود المرحّلة</h3>
                        <div class="number"><?php echo $postedJournals; ?></div>
                    </div>
                </div>
                <div class="stat-card stat-warning">
                    <div class="stat-content">
                        <h3>المسودات</h3>
                        <div class="number"><?php echo $draftJournals; ?></div>
                    </div>
                </div>
                <div class="stat-card stat-info">
                    <div class="stat-content">
                        <h3>إجمالي المدين</h3>
                        <div class="number" style="font-size: 20px;"><?php echo number_format($totalDebit, 2); ?></div>
                    </div>
                </div>
            </div>
            
            <!-- فلاتر البحث -->
            <div class="card" style="margin-bottom: 20px;">
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>بحث</label>
                            <input type="text" class="form-control" id="searchInput" placeholder="ابحث برقم القيد أو البيان...">
                        </div>
                        <div class="form-group">
                            <label>الحالة</label>
                            <select class="form-control" id="statusFilter">
                                <option value="">الكل</option>
                                <option value="posted">مرحّل</option>
                                <option value="draft">مسودة</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>من تاريخ</label>
                            <input type="date" class="form-control" id="dateFrom">
                        </div>
                        <div class="form-group">
                            <label>إلى تاريخ</label>
                            <input type="date" class="form-control" id="dateTo">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- جدول القيود -->
            <div class="card">
                <div class="card-header">
                    <h3>📋 قائمة القيود اليومية</h3>
                </div>
                <div class="card-body">
                    <?php if (count($journals) > 0): ?>
                    <div class="table-responsive">
                        <table class="table" id="journalsTable">
                            <thead>
                                <tr>
                                    <th>رقم القيد</th>
                                    <th>التاريخ</th>
                                    <th>البيان</th>
                                    <th>المدين</th>
                                    <th>الدائن</th>
                                    <th>الحالة</th>
                                    <th>المستخدم</th>
                                    <th>تاريخ الإنشاء</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($journals as $journal): ?>
                                <tr data-status="<?php echo $journal['status']; ?>" 
                                    data-date="<?php echo $journal['date']; ?>">
                                    <td><strong><?php echo htmlspecialchars($journal['journalNumber']); ?></strong></td>
                                    <td><?php echo date('Y-m-d', strtotime($journal['date'])); ?></td>
                                    <td><?php echo htmlspecialchars(substr($journal['description'], 0, 60)); ?><?php echo strlen($journal['description']) > 60 ? '...' : ''; ?></td>
                                    <td><strong><?php echo number_format($journal['totalDebit'], 2); ?></strong></td>
                                    <td><strong><?php echo number_format($journal['totalCredit'], 2); ?></strong></td>
                                    <td>
                                        <span class="badge badge-<?php echo $journal['status'] == 'posted' ? 'success' : 'warning'; ?>">
                                            <?php echo $journal['status'] == 'posted' ? 'مرحّل' : 'مسودة'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($journal['createdByName'] ?? 'غير معروف'); ?></td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($journal['createdAt'])); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="alert('عرض التفاصيل')">عرض</button>
                                        <?php if ($journal['status'] == 'draft'): ?>
                                        <button class="btn btn-sm btn-warning" onclick="alert('تعديل')">تعديل</button>
                                        <button class="btn btn-sm btn-success" onclick="alert('ترحيل')">ترحيل</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div style="text-align: center; padding: 40px; color: #999;">
                        <div style="font-size: 48px; margin-bottom: 20px;">📝</div>
                        <h3>لا توجد قيود بعد</h3>
                        <p>ابدأ بإضافة قيد جديد</p>
                        <button class="btn btn-primary" style="margin-top: 20px;" onclick="alert('سيتم إضافة هذه الميزة قريباً')">
                            ➕ إضافة قيد جديد
                        </button>
                    </div>
                    <?php endif; ?>
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
    document.getElementById('statusFilter').addEventListener('change', filterTable);
    document.getElementById('dateFrom').addEventListener('change', filterTable);
    document.getElementById('dateTo').addEventListener('change', filterTable);
    
    function filterTable() {
        const searchValue = document.getElementById('searchInput').value.toLowerCase();
        const statusValue = document.getElementById('statusFilter').value;
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;
        
        const rows = document.querySelectorAll('#journalsTable tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const status = row.getAttribute('data-status');
            const date = row.getAttribute('data-date');
            
            const matchSearch = text.includes(searchValue);
            const matchStatus = !statusValue || status === statusValue;
            const matchDateFrom = !dateFrom || date >= dateFrom;
            const matchDateTo = !dateTo || date <= dateTo;
            
            if (matchSearch && matchStatus && matchDateFrom && matchDateTo) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    </script>
</body>
</html>
