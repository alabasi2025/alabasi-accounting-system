<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'نظام الأباسي المحاسبي الموحد'; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="dashboard">
    <!-- القائمة الجانبية -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>📊 نظام الأباسي</h2>
            <p style="font-size: 12px; opacity: 0.8; margin: 5px 0 0 0;">النظام المحاسبي الموحد</p>
        </div>
        
        <nav class="sidebar-menu">
            <!-- الصفحة الرئيسية -->
            <a href="dashboard.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <span class="icon">🏠</span>
                <span class="text">لوحة التحكم</span>
            </a>
            
            <div class="menu-divider"></div>
            
            <!-- قسم دليل الحسابات -->
            <div class="menu-section">📊 دليل الحسابات</div>
            
            <a href="accounts.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'accounts.php' ? 'active' : ''; ?>">
                <span class="icon">📋</span>
                <span class="text">دليل الحسابات</span>
            </a>
            
            <a href="accounts-manage.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'accounts-manage.php' ? 'active' : ''; ?>">
                <span class="icon">⚙️</span>
                <span class="text">إدارة الحسابات</span>
            </a>
            
            <a href="analytical-accounts.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'analytical-accounts.php' ? 'active' : ''; ?>">
                <span class="icon">🔍</span>
                <span class="text">الحسابات التحليلية</span>
            </a>
            
            <div class="menu-divider"></div>
            
            <!-- قسم القيود والسندات -->
            <div class="menu-section">📝 القيود والسندات</div>
            
            <a href="journals.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'journals.php' ? 'active' : ''; ?>">
                <span class="icon">📝</span>
                <span class="text">القيود اليومية</span>
            </a>
            
            <a href="payment-vouchers.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'payment-vouchers.php' ? 'active' : ''; ?>">
                <span class="icon">💸</span>
                <span class="text">سندات الصرف</span>
            </a>
            
            <a href="receipt-vouchers.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'receipt-vouchers.php' ? 'active' : ''; ?>">
                <span class="icon">💰</span>
                <span class="text">سندات القبض</span>
            </a>
            
            <div class="menu-divider"></div>
            
            <!-- قسم الحسابات الوسيطة -->
            <div class="menu-section">🔄 الحسابات الوسيطة</div>
            
            <a href="intermediate-accounts-list.php" class="menu-item <?php echo in_array(basename($_SERVER['PHP_SELF']), ['intermediate-accounts-list.php', 'intermediate-account-details.php']) ? 'active' : ''; ?>">
                <span class="icon">📋</span>
                <span class="text">قائمة الحسابات</span>
            </a>
            
            <a href="intermediate-account-add.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'intermediate-account-add.php' ? 'active' : ''; ?>">
                <span class="icon">➕</span>
                <span class="text">إضافة حساب وسيط</span>
            </a>
            
            <a href="intermediate-accounts.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'intermediate-accounts.php' ? 'active' : ''; ?>">
                <span class="icon">🔗</span>
                <span class="text">ربط الحسابات</span>
            </a>
            
            <div class="menu-divider"></div>
            
            <!-- قسم المخزون -->
            <div class="menu-section">📦 المخزون</div>
            
            <a href="inventory/items.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'items.php' ? 'active' : ''; ?>">
                <span class="icon">📦</span>
                <span class="text">إدارة الأصناف</span>
            </a>
            
            <a href="inventory/stock-movements.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'stock-movements.php' ? 'active' : ''; ?>">
                <span class="icon">🔄</span>
                <span class="text">حركات المخزون</span>
            </a>
            
            <a href="inventory/inventory-balance.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'inventory-balance.php' ? 'active' : ''; ?>">
                <span class="icon">📊</span>
                <span class="text">رصيد المخزون</span>
            </a>
            
            <div class="menu-divider"></div>
            
            <!-- قسم الهيكل التنظيمي -->
            <div class="menu-section">🏢 الهيكل التنظيمي</div>
            
            <a href="units.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'units.php' ? 'active' : ''; ?>">
                <span class="icon">🏛️</span>
                <span class="text">الوحدات المحاسبية</span>
            </a>
            
            <a href="companies.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'companies.php' ? 'active' : ''; ?>">
                <span class="icon">🏢</span>
                <span class="text">المؤسسات</span>
            </a>
            
            <a href="branches.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'branches.php' ? 'active' : ''; ?>">
                <span class="icon">🏪</span>
                <span class="text">الفروع</span>
            </a>
            
            <a href="warehouses.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'warehouses.php' ? 'active' : ''; ?>">
                <span class="icon">🏬</span>
                <span class="text">المخازن</span>
            </a>
            
            <div class="menu-divider"></div>
            
            <!-- قسم التقارير -->
            <div class="menu-section">📈 التقارير</div>
            
            <a href="reports.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
                <span class="icon">📊</span>
                <span class="text">التقارير المالية</span>
            </a>
            
            <div class="menu-divider"></div>
            
            <!-- قسم الإدارة -->
            <div class="menu-section">⚙️ الإدارة</div>
            
            <a href="users.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                <span class="icon">👥</span>
                <span class="text">المستخدمين</span>
            </a>
            
            <a href="accounting-cycles.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'accounting-cycles.php' ? 'active' : ''; ?>">
                <span class="icon">📅</span>
                <span class="text">الدورات المحاسبية</span>
            </a>
            
            <a href="backup-manager.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'backup-manager.php' ? 'active' : ''; ?>">
                <span class="icon">💾</span>
                <span class="text">النسخ الاحتياطي</span>
            </a>
            
            <a href="settings.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                <span class="icon">⚙️</span>
                <span class="text">الإعدادات</span>
            </a>
        </nav>
        
        <div class="sidebar-footer">
            <a href="logout.php" class="menu-item logout-btn">
                <span class="icon">🚪</span>
                <span class="text">تسجيل الخروج</span>
            </a>
        </div>
    </div>
    
    <!-- المحتوى الرئيسي -->
    <div class="main-content">
        <!-- شريط التنقل العلوي -->
        <nav class="topbar">
            <div class="topbar-content">
                <button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>
                <div class="topbar-title">
                    <h1><?php echo $pageTitle ?? 'لوحة التحكم'; ?></h1>
                </div>
                <div class="topbar-user">
                    <span class="user-name">👤 <?php echo getCurrentUserName(); ?></span>
                </div>
            </div>
        </nav>
        
        <!-- محتوى الصفحة -->
        <div class="page-content">
