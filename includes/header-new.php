<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'نظام الأباسي المحاسبي الموحد'; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/collapsible-sidebar.css">
</head>
<body class="dashboard">
    <!-- زر toggle للقائمة الجانبية -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <span class="toggle-icon">☰</span>
    </button>

    <!-- القائمة الجانبية -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2><span class="icon-only">📊</span><span class="full-text">نظام الأباسي</span></h2>
            <p class="full-text" style="font-size: 12px; opacity: 0.8; margin: 5px 0 0 0;">النظام المحاسبي الموحد</p>
        </div>
        
        <nav class="sidebar-menu">
            <!-- الصفحة الرئيسية -->
            <a href="dashboard.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <span class="icon">🏠</span>
                <span class="text">لوحة التحكم</span>
            </a>
            
            <div class="menu-divider"></div>
            
            <!-- قسم دليل الحسابات -->
            <div class="menu-group">
                <div class="menu-section" onclick="toggleSubmenu(this)">
                    <span class="icon">📊</span>
                    <span class="text">دليل الحسابات</span>
                    <span class="arrow full-text">▼</span>
                </div>
                <div class="submenu">
                    <a href="accounts.php" class="menu-item submenu-item <?php echo basename($_SERVER['PHP_SELF']) == 'accounts.php' ? 'active' : ''; ?>">
                        <span class="icon">📋</span>
                        <span class="text">دليل الحسابات</span>
                    </a>
                    
                    <a href="accounts-manage.php" class="menu-item submenu-item <?php echo basename($_SERVER['PHP_SELF']) == 'accounts-manage.php' ? 'active' : ''; ?>">
                        <span class="icon">⚙️</span>
                        <span class="text">إدارة الحسابات</span>
                    </a>
                    
                    <a href="analytical-accounts.php" class="menu-item submenu-item <?php echo basename($_SERVER['PHP_SELF']) == 'analytical-accounts.php' ? 'active' : ''; ?>">
                        <span class="icon">🔍</span>
                        <span class="text">الحسابات التحليلية</span>
                    </a>
                </div>
            </div>
            
            <div class="menu-divider"></div>
            
            <!-- قسم القيود والسندات -->
            <div class="menu-group">
                <div class="menu-section" onclick="toggleSubmenu(this)">
                    <span class="icon">📝</span>
                    <span class="text">القيود والسندات</span>
                    <span class="arrow full-text">▼</span>
                </div>
                <div class="submenu">
                    <a href="journals.php" class="menu-item submenu-item <?php echo basename($_SERVER['PHP_SELF']) == 'journals.php' ? 'active' : ''; ?>">
                        <span class="icon">📝</span>
                        <span class="text">القيود اليومية</span>
                    </a>
                    
                    <a href="payment-vouchers.php" class="menu-item submenu-item <?php echo basename($_SERVER['PHP_SELF']) == 'payment-vouchers.php' ? 'active' : ''; ?>">
                        <span class="icon">💸</span>
                        <span class="text">سندات الصرف</span>
                    </a>
                    
                    <a href="receipt-vouchers.php" class="menu-item submenu-item <?php echo basename($_SERVER['PHP_SELF']) == 'receipt-vouchers.php' ? 'active' : ''; ?>">
                        <span class="icon">💰</span>
                        <span class="text">سندات القبض</span>
                    </a>
                </div>
            </div>
            
            <div class="menu-divider"></div>
            
            <!-- قسم الحسابات الوسيطة -->
            <div class="menu-group">
                <div class="menu-section" onclick="toggleSubmenu(this)">
                    <span class="icon">🔄</span>
                    <span class="text">الحسابات الوسيطة</span>
                    <span class="arrow full-text">▼</span>
                </div>
                <div class="submenu">
                    <a href="intermediate-accounts-list.php" class="menu-item submenu-item <?php echo in_array(basename($_SERVER['PHP_SELF']), ['intermediate-accounts-list.php', 'intermediate-account-details.php']) ? 'active' : ''; ?>">
                        <span class="icon">📋</span>
                        <span class="text">قائمة الحسابات</span>
                    </a>
                    
                    <a href="intermediate-account-add.php" class="menu-item submenu-item <?php echo basename($_SERVER['PHP_SELF']) == 'intermediate-account-add.php' ? 'active' : ''; ?>">
                        <span class="icon">➕</span>
                        <span class="text">إضافة حساب وسيط</span>
                    </a>
                    
                    <a href="intermediate-accounts.php" class="menu-item submenu-item <?php echo basename($_SERVER['PHP_SELF']) == 'intermediate-accounts.php' ? 'active' : ''; ?>">
                        <span class="icon">🔗</span>
                        <span class="text">ربط الحسابات</span>
                    </a>
                </div>
            </div>
            
            <div class="menu-divider"></div>
            
            <!-- قسم المخزون -->
            <div class="menu-group">
                <div class="menu-section" onclick="toggleSubmenu(this)">
                    <span class="icon">📦</span>
                    <span class="text">المخزون</span>
                    <span class="arrow full-text">▼</span>
                </div>
                <div class="submenu">
                    <a href="inventory/items.php" class="menu-item submenu-item <?php echo basename($_SERVER['PHP_SELF']) == 'items.php' ? 'active' : ''; ?>">
                        <span class="icon">📦</span>
                        <span class="text">إدارة الأصناف</span>
                    </a>
                    
                    <a href="inventory/stock-movements.php" class="menu-item submenu-item <?php echo basename($_SERVER['PHP_SELF']) == 'stock-movements.php' ? 'active' : ''; ?>">
                        <span class="icon">🔄</span>
                        <span class="text">حركات المخزون</span>
                    </a>
                    
                    <a href="inventory/inventory-balance.php" class="menu-item submenu-item <?php echo basename($_SERVER['PHP_SELF']) == 'inventory-balance.php' ? 'active' : ''; ?>">
                        <span class="icon">📊</span>
                        <span class="text">رصيد المخزون</span>
                    </a>
                </div>
            </div>
            
            <div class="menu-divider"></div>
            
            <!-- قسم الهيكل التنظيمي -->
            <div class="menu-group">
                <div class="menu-section" onclick="toggleSubmenu(this)">
                    <span class="icon">🏢</span>
                    <span class="text">الهيكل التنظيمي</span>
                    <span class="arrow full-text">▼</span>
                </div>
                <div class="submenu">
                    <a href="units.php" class="menu-item submenu-item <?php echo basename($_SERVER['PHP_SELF']) == 'units.php' ? 'active' : ''; ?>">
                        <span class="icon">🏛️</span>
                        <span class="text">الوحدات المحاسبية</span>
                    </a>
                    
                    <a href="companies.php" class="menu-item submenu-item <?php echo basename($_SERVER['PHP_SELF']) == 'companies.php' ? 'active' : ''; ?>">
                        <span class="icon">🏢</span>
                        <span class="text">المؤسسات</span>
                    </a>
                    
                    <a href="branches.php" class="menu-item submenu-item <?php echo basename($_SERVER['PHP_SELF']) == 'branches.php' ? 'active' : ''; ?>">
                        <span class="icon">🏪</span>
                        <span class="text">الفروع</span>
                    </a>
                    
                    <a href="warehouses.php" class="menu-item submenu-item <?php echo basename($_SERVER['PHP_SELF']) == 'warehouses.php' ? 'active' : ''; ?>">
                        <span class="icon">🏭</span>
                        <span class="text">المخازن</span>
                    </a>
                </div>
            </div>
            
            <div class="menu-divider"></div>
            
            <!-- قسم التقارير والإدارة -->
            <div class="menu-group">
                <div class="menu-section" onclick="toggleSubmenu(this)">
                    <span class="icon">📈</span>
                    <span class="text">التقارير والإدارة</span>
                    <span class="arrow full-text">▼</span>
                </div>
                <div class="submenu">
                    <a href="reports.php" class="menu-item submenu-item <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
                        <span class="icon">📊</span>
                        <span class="text">التقارير المالية</span>
                    </a>
                    
                    <a href="users.php" class="menu-item submenu-item <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                        <span class="icon">👥</span>
                        <span class="text">المستخدمين</span>
                    </a>
                    
                    <a href="settings.php" class="menu-item submenu-item <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                        <span class="icon">⚙️</span>
                        <span class="text">الإعدادات</span>
                    </a>
                </div>
            </div>
            
            <div class="menu-divider"></div>
            
            <!-- قسم التوثيق -->
            <div class="menu-group">
                <div class="menu-section" onclick="toggleSubmenu(this)">
                    <span class="icon">📚</span>
                    <span class="text">التوثيق</span>
                    <span class="arrow full-text">▼</span>
                </div>
                <div class="submenu">
                    <a href="build-guide.php" class="menu-item submenu-item <?php echo basename($_SERVER['PHP_SELF']) == 'build-guide.php' ? 'active' : ''; ?>">
                        <span class="icon">📋</span>
                        <span class="text">دليل البناء</span>
                    </a>
                    
                    <a href="system-structure.php" class="menu-item submenu-item <?php echo basename($_SERVER['PHP_SELF']) == 'system-structure.php' ? 'active' : ''; ?>">
                        <span class="icon">🏗️</span>
                        <span class="text">مخطط البناء</span>
                    </a>
                </div>
            </div>
        </nav>
        
        <!-- تذييل القائمة الجانبية -->
        <div class="sidebar-footer">
            <a href="logout.php" class="menu-item">
                <span class="icon">🚪</span>
                <span class="text">تسجيل الخروج</span>
            </a>
        </div>
    </div>

    <!-- المحتوى الرئيسي -->
    <div class="main-content" id="mainContent">
