<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'admin';
    $_SESSION['role'] = 'admin';
}

$pageTitle = 'إضافة حساب وسيط جديد';

$errors = [];
$success = '';

// معالجة النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entityType = $_POST['entityType'] ?? '';
    $entityId = $_POST['entityId'] ?? 0;
    
    // التحقق من البيانات
    if (empty($entityType)) {
        $errors[] = 'يجب اختيار نوع الكيان';
    }
    
    if (empty($entityId) || $entityId <= 0) {
        $errors[] = 'يجب اختيار الكيان';
    }
    
    // التحقق من عدم وجود حساب وسيط مسبقاً لنفس الكيان
    if (empty($errors)) {
        $check = $pdo->prepare("SELECT id FROM intermediate_accounts WHERE entityType = ? AND entityId = ?");
        $check->execute([$entityType, $entityId]);
        if ($check->fetch()) {
            $errors[] = 'يوجد حساب وسيط بالفعل لهذا الكيان';
        }
    }
    
    // إنشاء الحساب
    if (empty($errors)) {
        try {
            // تحديد رقم الحساب بناءً على نوع الكيان
            $accountNumber = $entityType === 'unit' ? 1900 : 1950;
            
            $stmt = $pdo->prepare("INSERT INTO intermediate_accounts 
                (accountNumber, entityType, entityId, balance, isActive, createdAt, createdBy) 
                VALUES (?, ?, ?, 0, 1, NOW(), ?)");
            
            $stmt->execute([
                $accountNumber,
                $entityType,
                $entityId,
                $_SESSION['user_id']
            ]);
            
            $newId = $pdo->lastInsertId();
            $success = 'تم إنشاء الحساب الوسيط بنجاح!';
            
            // إعادة التوجيه بعد 2 ثانية
            header("refresh:2;url=intermediate-account-details.php?id=$newId");
        } catch (Exception $e) {
            $errors[] = 'حدث خطأ أثناء إنشاء الحساب: ' . $e->getMessage();
        }
    }
}

// جلب قائمة الوحدات
$units = [];
try {
    $stmt = $pdo->query("SELECT id, code, nameAr FROM accounting_units WHERE isActive = 1 ORDER BY nameAr");
    $units = $stmt->fetchAll();
} catch (Exception $e) {
    $units = [];
}

// جلب قائمة المؤسسات
$companies = [];
try {
    $stmt = $pdo->query("SELECT id, code, nameAr FROM organizations WHERE isActive = 1 ORDER BY nameAr");
    $companies = $stmt->fetchAll();
} catch (Exception $e) {
    $companies = [];
}

require_once 'includes/header.php';
?>

<style>
/* التصميم الاحترافي الجديد */
.page-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 30px 20px;
}

.page-header-new {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px;
    border-radius: 12px;
    margin-bottom: 40px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.page-header-new h1 {
    margin: 0 0 15px 0;
    font-size: 32px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 15px;
}

.page-header-new .subtitle {
    font-size: 16px;
    opacity: 0.95;
    margin: 0;
    font-weight: 400;
}

.breadcrumb-nav {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 20px;
    font-size: 14px;
}

.breadcrumb-nav a {
    color: white;
    text-decoration: none;
    opacity: 0.9;
    transition: opacity 0.3s;
}

.breadcrumb-nav a:hover {
    opacity: 1;
    text-decoration: underline;
}

.breadcrumb-nav span {
    opacity: 0.7;
}

/* بطاقة النموذج */
.form-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.form-card-header {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    padding: 25px 30px;
    border-bottom: 3px solid #667eea;
}

.form-card-header h2 {
    margin: 0;
    font-size: 22px;
    color: #2d3748;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-card-body {
    padding: 40px;
}

/* صندوق المعلومات */
.info-card {
    background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%);
    border-right: 5px solid #3b82f6;
    padding: 25px;
    border-radius: 10px;
    margin-bottom: 35px;
    box-shadow: 0 2px 10px rgba(59, 130, 246, 0.1);
}

.info-card-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 18px;
    font-weight: 700;
    color: #1e40af;
    margin: 0 0 15px 0;
}

.info-card-content {
    color: #1e3a8a;
    line-height: 1.8;
}

.info-card-content p {
    margin: 0 0 12px 0;
}

.info-list {
    list-style: none;
    padding: 0;
    margin: 15px 0 0 0;
}

.info-list li {
    padding: 10px 15px;
    background: white;
    margin-bottom: 8px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 500;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.info-list li::before {
    content: "✓";
    display: inline-block;
    width: 24px;
    height: 24px;
    background: #3b82f6;
    color: white;
    border-radius: 50%;
    text-align: center;
    line-height: 24px;
    font-weight: bold;
    flex-shrink: 0;
}

.account-number {
    background: #1e40af;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-weight: 700;
    font-size: 16px;
}

/* مجموعات النموذج */
.form-section-title {
    font-size: 18px;
    font-weight: 600;
    color: #2d3748;
    margin: 0 0 25px 0;
    padding-bottom: 12px;
    border-bottom: 2px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-group-modern {
    margin-bottom: 28px;
}

.form-label-modern {
    display: block;
    font-size: 15px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 10px;
}

.form-label-modern .required-star {
    color: #ef4444;
    font-size: 18px;
    margin-right: 4px;
}

.form-control-modern {
    width: 100%;
    padding: 14px 18px;
    font-size: 15px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    background: #f9fafb;
    transition: all 0.3s ease;
    font-family: inherit;
}

.form-control-modern:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.form-control-modern:hover {
    border-color: #cbd5e1;
}

/* الأزرار */
.form-actions-modern {
    display: flex;
    gap: 15px;
    margin-top: 40px;
    padding-top: 30px;
    border-top: 2px solid #e5e7eb;
}

.btn-modern {
    padding: 14px 32px;
    font-size: 16px;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.btn-primary-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.btn-secondary-modern {
    background: #6b7280;
    color: white;
}

.btn-secondary-modern:hover {
    background: #4b5563;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(107, 114, 128, 0.3);
}

/* رسائل التنبيه */
.alert-modern {
    padding: 20px 25px;
    border-radius: 10px;
    margin-bottom: 25px;
    font-size: 15px;
    line-height: 1.6;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.alert-danger-modern {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    border-right: 5px solid #ef4444;
    color: #991b1b;
}

.alert-success-modern {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    border-right: 5px solid #10b981;
    color: #065f46;
}

.alert-modern ul {
    margin: 10px 0 0 0;
    padding-right: 25px;
}

.alert-modern li {
    margin-bottom: 5px;
}

.alert-icon {
    font-size: 20px;
    margin-left: 8px;
}

/* تحسينات الاستجابة */
@media (max-width: 768px) {
    .page-header-new {
        padding: 25px;
    }
    
    .page-header-new h1 {
        font-size: 24px;
    }
    
    .form-card-body {
        padding: 25px 20px;
    }
    
    .form-actions-modern {
        flex-direction: column;
    }
    
    .btn-modern {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="page-wrapper">
    <!-- رأس الصفحة -->
    <div class="page-header-new">
        <h1>
            <span>➕</span>
            <span>إضافة حساب وسيط جديد</span>
        </h1>
        <p class="subtitle">إنشاء حساب وسيط لوحدة محاسبية أو مؤسسة</p>
        <div class="breadcrumb-nav">
            <a href="index.php">🏠 الرئيسية</a>
            <span>›</span>
            <a href="intermediate-accounts-list.php">الحسابات الوسيطة</a>
            <span>›</span>
            <span>إضافة جديد</span>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert-modern alert-danger-modern">
            <strong><span class="alert-icon">⚠️</span> يوجد أخطاء في النموذج:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert-modern alert-success-modern">
            <strong><span class="alert-icon">✅</span> <?= htmlspecialchars($success) ?></strong>
            <br>
            <small>سيتم تحويلك إلى صفحة التفاصيل خلال ثوانٍ...</small>
        </div>
    <?php endif; ?>

    <!-- بطاقة النموذج -->
    <div class="form-card">
        <div class="form-card-header">
            <h2>📝 بيانات الحساب الوسيط</h2>
        </div>
        
        <div class="form-card-body">
            <!-- صندوق المعلومات -->
            <div class="info-card">
                <div class="info-card-title">
                    <span>ℹ️</span>
                    <span>معلومات مهمة</span>
                </div>
                <div class="info-card-content">
                    <p>يتم إنشاء حساب وسيط واحد فقط لكل كيان (وحدة محاسبية أو مؤسسة)</p>
                    <ul class="info-list">
                        <li>
                            <span>🏛️ الوحدات المحاسبية: رقم الحساب</span>
                            <span class="account-number">1900</span>
                        </li>
                        <li>
                            <span>🏢 المؤسسات: رقم الحساب</span>
                            <span class="account-number">1950</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- النموذج -->
            <form method="POST" action="">
                <div class="form-section-title">
                    <span>🎯</span>
                    <span>اختيار الكيان</span>
                </div>

                <!-- نوع الكيان -->
                <div class="form-group-modern">
                    <label for="entityType" class="form-label-modern">
                        <span class="required-star">*</span>
                        نوع الكيان
                    </label>
                    <select name="entityType" id="entityType" class="form-control-modern" required onchange="toggleEntityList()">
                        <option value="">-- اختر نوع الكيان --</option>
                        <option value="unit">🏛️ وحدة محاسبية</option>
                        <option value="company">🏢 مؤسسة</option>
                    </select>
                </div>

                <!-- قائمة الوحدات -->
                <div class="form-group-modern" id="unitGroup" style="display: none;">
                    <label for="unitId" class="form-label-modern">
                        <span class="required-star">*</span>
                        الوحدة المحاسبية
                    </label>
                    <select name="entityId" id="unitId" class="form-control-modern">
                        <option value="">-- اختر الوحدة المحاسبية --</option>
                        <?php foreach ($units as $unit): ?>
                            <option value="<?= $unit['id'] ?>">
                                [<?= htmlspecialchars($unit['code']) ?>] <?= htmlspecialchars($unit['nameAr']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- قائمة المؤسسات -->
                <div class="form-group-modern" id="companyGroup" style="display: none;">
                    <label for="companyId" class="form-label-modern">
                        <span class="required-star">*</span>
                        المؤسسة
                    </label>
                    <select name="entityId" id="companyId" class="form-control-modern">
                        <option value="">-- اختر المؤسسة --</option>
                        <?php foreach ($companies as $company): ?>
                            <option value="<?= $company['id'] ?>">
                                [<?= htmlspecialchars($company['code']) ?>] <?= htmlspecialchars($company['nameAr']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- أزرار الإجراءات -->
                <div class="form-actions-modern">
                    <button type="submit" class="btn-modern btn-primary-modern">
                        <span>✅</span>
                        <span>إنشاء الحساب الوسيط</span>
                    </button>
                    <a href="intermediate-accounts-list.php" class="btn-modern btn-secondary-modern">
                        <span>↩️</span>
                        <span>إلغاء والعودة</span>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleEntityList() {
    const entityType = document.getElementById('entityType').value;
    const unitGroup = document.getElementById('unitGroup');
    const companyGroup = document.getElementById('companyGroup');
    const unitSelect = document.getElementById('unitId');
    const companySelect = document.getElementById('companyId');
    
    if (entityType === 'unit') {
        unitGroup.style.display = 'block';
        companyGroup.style.display = 'none';
        unitSelect.required = true;
        companySelect.required = false;
        companySelect.value = '';
    } else if (entityType === 'company') {
        unitGroup.style.display = 'none';
        companyGroup.style.display = 'block';
        unitSelect.required = false;
        companySelect.required = true;
        unitSelect.value = '';
    } else {
        unitGroup.style.display = 'none';
        companyGroup.style.display = 'none';
        unitSelect.required = false;
        companySelect.required = false;
        unitSelect.value = '';
        companySelect.value = '';
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
