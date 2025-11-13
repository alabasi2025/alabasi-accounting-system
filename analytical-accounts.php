<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'admin';
    $_SESSION['role'] = 'admin';
}

$pageTitle = 'الحسابات التحليلية';

// Get analytical accounts with main account names
$stmt = $pdo->query("
    SELECT aa.*, a.code as accountCode, a.nameAr as accountName 
    FROM analyticalAccounts aa 
    JOIN accounts a ON aa.accountId = a.id 
    ORDER BY aa.code
");
$analyticalAccounts = $stmt->fetchAll();

// Get accounts for dropdown
$accounts = $pdo->query("SELECT id, code, nameAr FROM accounts WHERE allowPosting = 1 ORDER BY code")->fetchAll();

require_once 'includes/header.php';
?>

<div class="page-header">
    <h2>📋 الحسابات التحليلية</h2>
    <button class="btn btn-primary" onclick="showAddModal()">+ إضافة حساب تحليلي</button>
</div>

<div class="card">
    <div class="card-body">
        <div class="filters">
            <input type="text" id="searchInput" placeholder="بحث..." onkeyup="filterTable()">
            <select id="typeFilter" onchange="filterTable()">
                <option value="">جميع الأنواع</option>
                <option value="customer">عميل</option>
                <option value="supplier">مورد</option>
                <option value="employee">موظف</option>
                <option value="project">مشروع</option>
                <option value="cost_center">مركز تكلفة</option>
                <option value="other">أخرى</option>
            </select>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>قائمة الحسابات التحليلية (<?= count($analyticalAccounts) ?>)</h2>
    </div>
    <div class="card-body">
        <table class="data-table" id="analyticalTable">
            <thead>
                <tr>
                    <th>الرمز</th>
                    <th>الاسم</th>
                    <th>النوع</th>
                    <th>الحساب الرئيسي</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($analyticalAccounts as $aa): ?>
                <tr data-type="<?= $aa['type'] ?>">
                    <td><?= htmlspecialchars($aa['code']) ?></td>
                    <td><?= htmlspecialchars($aa['nameAr']) ?></td>
                    <td>
                        <?php
                        $types = [
                            'customer' => 'عميل',
                            'supplier' => 'مورد',
                            'employee' => 'موظف',
                            'project' => 'مشروع',
                            'cost_center' => 'مركز تكلفة',
                            'other' => 'أخرى'
                        ];
                        echo $types[$aa['type']] ?? $aa['type'];
                        ?>
                    </td>
                    <td><?= $aa['accountCode'] ?> - <?= htmlspecialchars($aa['accountName']) ?></td>
                    <td>
                        <span class="badge badge-<?= $aa['isActive'] ? 'success' : 'danger' ?>">
                            <?= $aa['isActive'] ? 'نشط' : 'غير نشط' ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editAnalytical(<?= $aa['id'] ?>)">تعديل</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteAnalytical(<?= $aa['id'] ?>)">حذف</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="analyticalModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">إضافة حساب تحليلي</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="analyticalForm" onsubmit="saveAnalytical(event)">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="analyticalId">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>الرمز *</label>
                        <input type="text" name="code" id="code" required>
                    </div>
                    <div class="form-group">
                        <label>الاسم (عربي) *</label>
                        <input type="text" name="nameAr" id="nameAr" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>الاسم (إنجليزي)</label>
                        <input type="text" name="nameEn" id="nameEn">
                    </div>
                    <div class="form-group">
                        <label>النوع *</label>
                        <select name="type" id="type" required>
                            <option value="">اختر النوع</option>
                            <option value="customer">عميل</option>
                            <option value="supplier">مورد</option>
                            <option value="employee">موظف</option>
                            <option value="project">مشروع</option>
                            <option value="cost_center">مركز تكلفة</option>
                            <option value="other">أخرى</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>الحساب الرئيسي *</label>
                    <select name="accountId" id="accountId" required>
                        <option value="">اختر الحساب</option>
                        <?php foreach ($accounts as $account): ?>
                        <option value="<?= $account['id'] ?>">
                            <?= $account['code'] ?> - <?= htmlspecialchars($account['nameAr']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
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
    document.getElementById('modalTitle').textContent = 'إضافة حساب تحليلي';
    document.getElementById('formAction').value = 'add';
    document.getElementById('analyticalForm').reset();
    document.getElementById('isActive').checked = true;
    document.getElementById('analyticalModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('analyticalModal').style.display = 'none';
}

function saveAnalytical(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    fetch('api/analytical-accounts.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('خطأ: ' + data.message);
        }
    });
}

function editAnalytical(id) {
    fetch('api/analytical-accounts.php?action=get&id=' + id)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const aa = data.data;
            document.getElementById('modalTitle').textContent = 'تعديل الحساب التحليلي';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('analyticalId').value = aa.id;
            document.getElementById('code').value = aa.code;
            document.getElementById('nameAr').value = aa.nameAr;
            document.getElementById('nameEn').value = aa.nameEn || '';
            document.getElementById('type').value = aa.type;
            document.getElementById('accountId').value = aa.accountId;
            document.getElementById('isActive').checked = aa.isActive == 1;
            document.getElementById('analyticalModal').style.display = 'block';
        }
    });
}

function deleteAnalytical(id) {
    if (confirm('هل أنت متأكد من حذف هذا الحساب التحليلي؟')) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);
        
        fetch('api/analytical-accounts.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('خطأ: ' + data.message);
            }
        });
    }
}

function filterTable() {
    const searchValue = document.getElementById('searchInput').value.toLowerCase();
    const typeValue = document.getElementById('typeFilter').value;
    
    const table = document.getElementById('analyticalTable');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const code = row.cells[0].textContent.toLowerCase();
        const name = row.cells[1].textContent.toLowerCase();
        const type = row.getAttribute('data-type');
        
        let show = true;
        
        if (searchValue && !code.includes(searchValue) && !name.includes(searchValue)) {
            show = false;
        }
        
        if (typeValue && type !== typeValue) {
            show = false;
        }
        
        row.style.display = show ? '' : 'none';
    }
}

window.onclick = function(event) {
    const modal = document.getElementById('analyticalModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>

<style>
.filters {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 15px;
}
</style>

<?php require_once 'includes/footer.php'; ?>
