<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Auto login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'admin';
    $_SESSION['role'] = 'admin';
}

$pageTitle = 'إدارة المؤسسات';

// Get all companies with unit names
$stmt = $pdo->query("
    SELECT c.*, u.nameAr as unitName 
    FROM companies c
    LEFT JOIN units u ON c.unitId = u.id
    ORDER BY c.id DESC
");
$companies = $stmt->fetchAll();

// Get all active units for dropdown
$unitsStmt = $pdo->query("SELECT id, code, nameAr FROM units WHERE isActive = 1 ORDER BY nameAr");
$units = $unitsStmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="page-header">
    <h1>🏢 إدارة المؤسسات</h1>
    <button class="btn btn-primary" onclick="showAddModal()">+ إضافة مؤسسة جديدة</button>
</div>

<div class="card">
    <div class="card-header">
        <h2>قائمة المؤسسات</h2>
    </div>
    <div class="card-body">
        <table class="data-table">
            <thead>
                <tr>
                    <th>الوحدة</th>
                    <th>الرمز</th>
                    <th>اسم المؤسسة</th>
                    <th>الرقم الضريبي</th>
                    <th>السجل التجاري</th>
                    <th>الهاتف</th>
                    <th>المدينة</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($companies as $company): ?>
                <tr>
                    <td>
                        <span class="badge badge-info"><?= htmlspecialchars($company['unitName'] ?? '-') ?></span>
                    </td>
                    <td><?= htmlspecialchars($company['code']) ?></td>
                    <td><?= htmlspecialchars($company['nameAr']) ?></td>
                    <td><?= htmlspecialchars($company['taxNumber'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($company['commercialRegister'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($company['phone'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($company['email'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($company['city'] ?? '-') ?></td>
                    <td>
                        <span class="badge badge-<?= $company['isActive'] ? 'success' : 'danger' ?>">
                            <?= $company['isActive'] ? 'نشط' : 'غير نشط' ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="viewCompany(<?= $company['id'] ?>)">عرض</button>
                        <button class="btn btn-sm btn-warning" onclick="editCompany(<?= $company['id'] ?>)">تعديل</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteCompany(<?= $company['id'] ?>)">حذف</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="companyModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">إضافة مؤسسة جديدة</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="companyForm" method="POST" action="api/companies.php">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="companyId">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>🏛️ الوحدة التابعة *</label>
                        <select name="unitId" id="unitId" required class="form-control">
                            <option value="">اختر الوحدة...</option>
                            <?php foreach ($units as $unit): ?>
                            <option value="<?= $unit['id'] ?>"><?= htmlspecialchars($unit['nameAr']) ?> (<?= htmlspecialchars($unit['code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>الرمز *</label>
                        <input type="text" name="code" id="code" required class="form-control">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>اسم المؤسسة (عربي) *</label>
                        <input type="text" name="nameAr" id="nameAr" required class="form-control">
                    </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>اسم المؤسسة (إنجليزي)</label>
                        <input type="text" name="nameEn" id="nameEn">
                    </div>
                    <div class="form-group">
                        <label>الرقم الضريبي</label>
                        <input type="text" name="taxNumber" id="taxNumber">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>السجل التجاري</label>
                        <input type="text" name="commercialRegister" id="commercialRegister">
                    </div>
                    <div class="form-group">
                        <label>الهاتف</label>
                        <input type="text" name="phone" id="phone">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>البريد الإلكتروني</label>
                        <input type="email" name="email" id="email">
                    </div>
                    <div class="form-group">
                        <label>الموقع الإلكتروني</label>
                        <input type="text" name="website" id="website">
                    </div>
                </div>

                <div class="form-group">
                    <label>العنوان</label>
                    <textarea name="address" id="address" rows="2"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>المدينة</label>
                        <input type="text" name="city" id="city">
                    </div>
                    <div class="form-group">
                        <label>الدولة</label>
                        <input type="text" name="country" id="country" value="السعودية">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>بداية السنة المالية</label>
                        <input type="date" name="fiscalYearStart" id="fiscalYearStart">
                    </div>
                    <div class="form-group">
                        <label>نهاية السنة المالية</label>
                        <input type="date" name="fiscalYearEnd" id="fiscalYearEnd">
                    </div>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="isActive" id="isActive" value="1" checked>
                        نشط
                    </label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">حفظ</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showAddModal() {
    document.getElementById('modalTitle').textContent = 'إضافة مؤسسة جديدة';
    document.getElementById('formAction').value = 'add';
    document.getElementById('companyForm').reset();
    document.getElementById('companyModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('companyModal').style.display = 'none';
}

function editCompany(id) {
    fetch('api/companies.php?action=get&id=' + id)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const company = data.data;
            document.getElementById('modalTitle').textContent = 'تعديل المؤسسة';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('companyId').value = company.id;
            document.getElementById('unitId').value = company.unitId || '';
            document.getElementById('code').value = company.code;
            document.getElementById('nameAr').value = company.nameAr;
            document.getElementById('nameEn').value = company.nameEn || '';
            document.getElementById('taxNumber').value = company.taxNumber || '';
            document.getElementById('commercialRegister').value = company.commercialRegister || '';
            document.getElementById('phone').value = company.phone || '';
            document.getElementById('email').value = company.email || '';
            document.getElementById('address').value = company.address || '';
            document.getElementById('city').value = company.city || '';
            document.getElementById('country').value = company.country || '';
            document.getElementById('postalCode').value = company.postalCode || '';
            document.getElementById('website').value = company.website || '';
            document.getElementById('isActive').checked = company.isActive == 1;
            document.getElementById('companyModal').style.display = 'block';
        } else {
            alert('خطأ: ' + data.message);
        }
    })
    .catch(error => {
        alert('خطأ في تحميل البيانات: ' + error);
    });
}

function viewCompany(id) {
    // TODO: Show company details
    alert('قريباً: عرض تفاصيل المؤسسة #' + id);
}

function deleteCompany(id) {
    if (confirm('هل أنت متأكد من حذف هذه المؤسسة؟')) {
        // TODO: Delete company
        alert('قريباً: حذف المؤسسة #' + id);
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('companyModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>

<style>
.modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 2% auto;
    padding: 0;
    border-radius: 8px;
    width: 90%;
    max-width: 800px;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-body {
    padding: 20px;
}

.close {
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    color: #999;
}

.close:hover {
    color: #333;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 15px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.form-group input[type="checkbox"] {
    width: auto;
    margin-right: 5px;
}

.form-actions {
    margin-top: 20px;
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.badge-danger {
    background-color: #dc3545;
    color: white;
}
</style>

<?php require_once 'includes/footer.php'; ?>
