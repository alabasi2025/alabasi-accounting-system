<?php
/**
 * صفحة التقارير
 * Reports Page
 */

require_once 'includes/db.php';
require_once 'includes/functions.php';

// التحقق من تسجيل الدخول
requireLogin();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التقارير - نظام العباسي الموحد</title>
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
                    <a href="journals.php" class="nav-link">القيود اليومية</a>
                    <a href="reports.php" class="nav-link active">التقارير</a>
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
                <h1 class="page-title">التقارير</h1>
                <p class="page-subtitle">التقارير المالية والإدارية</p>
            </div>
            
            <!-- التقارير المالية -->
            <h2 style="margin: 30px 0 20px; color: #333;">📊 التقارير المالية</h2>
            <div class="modules-grid">
                <div class="module-card" onclick="alert('قريباً')">
                    <div class="module-icon">📋</div>
                    <h3>ميزان المراجعة</h3>
                    <p>عرض ميزان المراجعة لفترة محددة</p>
                </div>
                
                <div class="module-card" onclick="alert('قريباً')">
                    <div class="module-icon">💰</div>
                    <h3>قائمة الدخل</h3>
                    <p>قائمة الأرباح والخسائر</p>
                </div>
                
                <div class="module-card" onclick="alert('قريباً')">
                    <div class="module-icon">📊</div>
                    <h3>الميزانية العمومية</h3>
                    <p>المركز المالي للشركة</p>
                </div>
                
                <div class="module-card" onclick="alert('قريباً')">
                    <div class="module-icon">💵</div>
                    <h3>قائمة التدفقات النقدية</h3>
                    <p>حركة النقد والنقد المعادل</p>
                </div>
            </div>
            
            <!-- تقارير الحسابات -->
            <h2 style="margin: 40px 0 20px; color: #333;">📁 تقارير الحسابات</h2>
            <div class="modules-grid">
                <div class="module-card" onclick="alert('قريباً')">
                    <div class="module-icon">📖</div>
                    <h3>دليل الحسابات</h3>
                    <p>طباعة دليل الحسابات الكامل</p>
                </div>
                
                <div class="module-card" onclick="alert('قريباً')">
                    <div class="module-icon">📄</div>
                    <h3>كشف حساب</h3>
                    <p>كشف حساب تفصيلي لحساب معين</p>
                </div>
                
                <div class="module-card" onclick="alert('قريباً')">
                    <div class="module-icon">🔍</div>
                    <h3>أرصدة الحسابات</h3>
                    <p>عرض أرصدة جميع الحسابات</p>
                </div>
                
                <div class="module-card" onclick="alert('قريباً')">
                    <div class="module-icon">👥</div>
                    <h3>كشف حساب عميل/مورد</h3>
                    <p>كشف الحسابات التحليلية</p>
                </div>
            </div>
            
            <!-- تقارير القيود -->
            <h2 style="margin: 40px 0 20px; color: #333;">📝 تقارير القيود</h2>
            <div class="modules-grid">
                <div class="module-card" onclick="alert('قريباً')">
                    <div class="module-icon">📅</div>
                    <h3>دفتر اليومية</h3>
                    <p>جميع القيود لفترة محددة</p>
                </div>
                
                <div class="module-card" onclick="alert('قريباً')">
                    <div class="module-icon">📊</div>
                    <h3>دفتر الأستاذ</h3>
                    <p>حركة الحسابات في دفتر الأستاذ</p>
                </div>
                
                <div class="module-card" onclick="alert('قريباً')">
                    <div class="module-icon">🔢</div>
                    <h3>القيود حسب النوع</h3>
                    <p>تصنيف القيود حسب النوع</p>
                </div>
                
                <div class="module-card" onclick="alert('قريباً')">
                    <div class="module-icon">👤</div>
                    <h3>القيود حسب المستخدم</h3>
                    <p>القيود المدخلة من كل مستخدم</p>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 نظام العباسي الموحد - جميع الحقوق محفوظة</p>
        </div>
    </footer>
</body>
</html>
