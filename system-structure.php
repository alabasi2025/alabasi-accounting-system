<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'مخطط البناء';

// Get database structure
$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
$tableStructures = [];

foreach ($tables as $table) {
    $columns = $pdo->query("SHOW COLUMNS FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
    $tableStructures[$table] = $columns;
}

include 'includes/header.php';
?>

<link rel="stylesheet" href="css/professional.css">

<style>
.structure-container {
    padding: 20px;
}

.structure-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 10px 10px 0 0;
    margin-bottom: 0;
}

.structure-header h1 {
    margin: 0 0 10px 0;
    font-size: 28px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.structure-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 14px;
}

.breadcrumb {
    background: #f8f9fa;
    padding: 10px 20px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.breadcrumb a {
    color: #667eea;
    text-decoration: none;
}

.breadcrumb a:hover {
    text-decoration: underline;
}

.warning-box {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
    color: white;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    border-right: 5px solid #c92a2a;
}

.warning-box h3 {
    margin: 0 0 10px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.warning-box ul {
    margin: 10px 0 0 0;
    padding-right: 20px;
}

.warning-box li {
    margin: 5px 0;
}

.info-box {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    border-right: 5px solid #0c8599;
}

.info-box h3 {
    margin: 0 0 10px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.tables-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.table-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}

.table-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.table-card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 10px;
}

.table-card-body {
    padding: 15px;
    max-height: 400px;
    overflow-y: auto;
}

.column-item {
    padding: 8px;
    margin: 5px 0;
    background: #f8f9fa;
    border-radius: 5px;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.column-item.primary-key {
    background: #fff3cd;
    border-right: 3px solid #ffc107;
}

.column-item.unique {
    background: #d1ecf1;
    border-right: 3px solid #17a2b8;
}

.column-name {
    font-weight: bold;
    color: #495057;
}

.column-type {
    color: #6c757d;
    font-size: 12px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-align: center;
}

.stat-card .number {
    font-size: 36px;
    font-weight: bold;
    color: #667eea;
    margin: 10px 0;
}

.stat-card .label {
    color: #6c757d;
    font-size: 14px;
}

.rules-section {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin: 20px 0;
}

.rules-section h3 {
    color: #495057;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.rule-item {
    padding: 10px;
    margin: 10px 0;
    background: #f8f9fa;
    border-radius: 5px;
    border-right: 3px solid #667eea;
}

.rule-item strong {
    color: #495057;
}
</style>

<div class="structure-container">
    <div class="structure-header">
        <h1>🏗️ مخطط البناء</h1>
        <p>بنية النظام المحاسبي الموحد - توثيق كامل لقاعدة البيانات والعلاقات</p>
    </div>

    <div class="breadcrumb">
        <a href="dashboard.php">🏠 الرئيسية</a> › 
        <span>مخطط البناء</span>
    </div>

    <!-- تحذيرات مهمة -->
    <div class="warning-box">
        <h3>⚠️ تحذيرات مهمة - يرجى القراءة بعناية</h3>
        <ul>
            <li><strong>لا تحذف أي جدول مباشرة</strong> - قد يؤدي ذلك إلى تعطل النظام بالكامل</li>
            <li><strong>لا تعدل أسماء الأعمدة</strong> - سيؤدي ذلك إلى أخطاء في الكود</li>
            <li><strong>احذر من حذف السجلات المرتبطة</strong> - تحقق من العلاقات أولاً</li>
            <li><strong>استخدم النسخ الاحتياطي</strong> - قبل أي تعديل كبير على البنية</li>
            <li><strong>اتبع القواعد المذكورة أدناه</strong> - لضمان سلامة البيانات</li>
        </ul>
    </div>

    <!-- معلومات عامة -->
    <div class="info-box">
        <h3>ℹ️ معلومات عامة</h3>
        <p><strong>اسم قاعدة البيانات:</strong> alabasi_accounting</p>
        <p><strong>عدد الجداول:</strong> <?= count($tables) ?> جدول</p>
        <p><strong>النوع:</strong> MySQL/MariaDB</p>
        <p><strong>الترميز:</strong> UTF-8</p>
    </div>

    <!-- إحصائيات -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="label">📋 إجمالي الجداول</div>
            <div class="number"><?= count($tables) ?></div>
        </div>
        <div class="stat-card">
            <div class="label">🔑 الجداول الرئيسية</div>
            <div class="number"><?= count(array_filter($tables, function($t) { 
                return in_array($t, ['accounting_units', 'accounts', 'journal_entries', 'branches', 'institutions']); 
            })) ?></div>
        </div>
        <div class="stat-card">
            <div class="label">👁️ Views (العروض)</div>
            <div class="number"><?= count(array_filter($tables, function($t) { 
                return in_array($t, ['units', 'customer_balances', 'supplier_balances']); 
            })) ?></div>
        </div>
    </div>

    <!-- قواعد الحذف والتعديل -->
    <div class="rules-section">
        <h3>📜 قواعد الحذف والتعديل</h3>
        
        <div class="rule-item">
            <strong>1. الوحدات المحاسبية (accounting_units):</strong>
            <ul>
                <li>✅ يمكن الإضافة بحرية</li>
                <li>✅ يمكن التعديل (الاسم، الوصف، الحالة)</li>
                <li>⚠️ تحقق من عدم وجود مؤسسات مرتبطة قبل الحذف</li>
                <li>❌ لا تحذف الوحدة الافتراضية</li>
            </ul>
        </div>

        <div class="rule-item">
            <strong>2. الحسابات (accounts):</strong>
            <ul>
                <li>✅ يمكن إضافة حسابات فرعية</li>
                <li>⚠️ لا تحذف الحسابات الرئيسية (1000، 2000، 3000، 4000، 5000)</li>
                <li>⚠️ تحقق من عدم وجود قيود مرتبطة قبل الحذف</li>
                <li>❌ لا تعدل رموز الحسابات الرئيسية</li>
            </ul>
        </div>

        <div class="rule-item">
            <strong>3. القيود اليومية (journal_entries):</strong>
            <ul>
                <li>✅ يمكن إضافة قيود جديدة</li>
                <li>⚠️ لا تحذف القيود المرحّلة (posted)</li>
                <li>✅ يمكن تعديل القيود المسودة (draft)</li>
                <li>❌ لا تحذف القيود المرتبطة بسندات</li>
            </ul>
        </div>

        <div class="rule-item">
            <strong>4. الحسابات الوسيطة (intermediate_accounts):</strong>
            <ul>
                <li>✅ يمكن الإضافة والتعديل بحرية</li>
                <li>⚠️ تحقق من الرصيد قبل الحذف (يجب أن يكون صفر)</li>
                <li>✅ استخدم واجهة الإدارة للحذف الآمن</li>
            </ul>
        </div>

        <div class="rule-item">
            <strong>5. المستخدمين (users):</strong>
            <ul>
                <li>✅ يمكن إضافة مستخدمين جدد</li>
                <li>❌ لا تحذف حساب المالك (owner)</li>
                <li>⚠️ تحقق من الصلاحيات قبل التعديل</li>
            </ul>
        </div>
    </div>

    <!-- بنية الجداول -->
    <h2 style="margin: 30px 0 20px 0; color: #495057;">📊 بنية الجداول</h2>
    
    <div class="tables-grid">
        <?php foreach ($tableStructures as $tableName => $columns): ?>
            <div class="table-card">
                <div class="table-card-header">
                    📋 <?= $tableName ?>
                </div>
                <div class="table-card-body">
                    <?php foreach ($columns as $column): ?>
                        <?php
                        $isPrimary = $column['Key'] === 'PRI';
                        $isUnique = $column['Key'] === 'UNI';
                        $class = $isPrimary ? 'primary-key' : ($isUnique ? 'unique' : '');
                        $icon = $isPrimary ? '🔑' : ($isUnique ? '⭐' : '📌');
                        ?>
                        <div class="column-item <?= $class ?>">
                            <span><?= $icon ?></span>
                            <div style="flex: 1;">
                                <div class="column-name"><?= $column['Field'] ?></div>
                                <div class="column-type"><?= $column['Type'] ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- العلاقات الرئيسية -->
    <div class="rules-section" style="margin-top: 30px;">
        <h3>🔗 العلاقات الرئيسية بين الجداول</h3>
        
        <div class="rule-item">
            <strong>accounting_units → institutions:</strong> الوحدة تحتوي على مؤسسات (One-to-Many)
        </div>
        
        <div class="rule-item">
            <strong>institutions → branches:</strong> المؤسسة تحتوي على فروع (One-to-Many)
        </div>
        
        <div class="rule-item">
            <strong>accounts → journal_entry_lines:</strong> الحساب يستخدم في قيود (One-to-Many)
        </div>
        
        <div class="rule-item">
            <strong>journal_entries → journal_entry_lines:</strong> القيد يحتوي على سطور (One-to-Many)
        </div>
        
        <div class="rule-item">
            <strong>intermediate_accounts → accounting_units:</strong> الحساب الوسيط بين وحدتين (Many-to-One)
        </div>
    </div>

    <!-- ملاحظات ختامية -->
    <div class="info-box" style="margin-top: 30px;">
        <h3>💡 ملاحظات مهمة</h3>
        <ul>
            <li>هذا المخطط تم إنشاؤه تلقائياً من قاعدة البيانات</li>
            <li>يتم تحديثه تلقائياً عند إضافة جداول جديدة</li>
            <li>استخدم هذا المرجع قبل أي تعديل على البنية</li>
            <li>للمزيد من المعلومات، راجع التوثيق الفني</li>
        </ul>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
