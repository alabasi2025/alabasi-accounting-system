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

$pageTitle = 'إدارة دليل الحسابات';

// Get all accounts with parent names
$stmt = $pdo->query("
    SELECT a.*, p.nameAr as parentName 
    FROM accounts a 
    LEFT JOIN accounts p ON a.parentId = p.id 
    ORDER BY a.code
");
$accounts = $stmt->fetchAll();

// Get parent accounts for dropdown
$parentAccounts = $pdo->query("SELECT id, code, nameAr FROM accounts WHERE isParent = 1 ORDER BY code")->fetchAll();

require_once 'includes/header.php';
?>

<div class="page-header">
    <h1>📊 إدارة دليل الحسابات</h1>
    <button class="btn btn-primary" onclick="showAddModal()">+ إضافة حساب جديد</button>
</div>

<!-- Filters -->
<div class="card">
    <div class="card-body">
        <div class="filters">
            <input type="text" id="searchInput" placeholder="بحث برقم أو اسم الحساب..." onkeyup="filterTable()">
            
            <select id="typeFilter" onchange="filterTable()">
                <option value="">جميع الأنواع</option>
                <option value="asset">أصول</option>
                <option value="liability">خصوم</option>
                <option value="equity">حقوق ملكية</option>
                <option value="revenue">إيرادات</option>
                <option value="expense">مصروفات</option>
            </select>
            
            <select id="levelFilter" onchange="filterTable()">
                <option value="">جميع المستويات</option>
                <option value="1">المستوى 1</option>
                <option value="2">المستوى 2</option>
                <option value="3">المستوى 3</option>
                <option value="4">المستوى 4</option>
            </select>
            
            <select id="statusFilter" onchange="filterTable()">
                <option value="">الكل</option>
                <option value="1">نشط</option>
                <option value="0">غير نشط</option>
            </select>
        </div>
    </div>
</div>

<!-- Accounts Table -->
<div class="card">
    <div class="card-header">
        <h2>قائمة الحسابات (<?= count($accounts) ?> حساب)</h2>
    </div>
    <div class="card-body">
        <table class="data-table" id="accountsTable">
            <thead>
                <tr>
                    <th>رقم الحساب</th>
                    <th>اسم الحساب</th>
                    <th>النوع</th>
                    <th>الحساب الأب</th>
                    <th>المستوى</th>
                    <th>حساب رئيسي</th>
                    <th>يسمح بالترحيل</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accounts as $account): ?>
                <tr data-type="<?= $account['type'] ?>" data-level="<?= $account['level'] ?>" data-status="<?= $account['isActive'] ?>">
                    <td><strong><?= htmlspecialchars($account['code']) ?></strong></td>
                    <td><?= htmlspecialchars($account['nameAr']) ?></td>
                    <td>
                        <?php
                        $types = [
                            'asset' => 'أصول',
                            'liability' => 'خصوم',
                            'equity' => 'حقوق ملكية',
                            'revenue' => 'إيرادات',
                            'expense' => 'مصروفات'
                        ];
                        echo $types[$account['type']] ?? $account['type'];
                        ?>
                    </td>
                    <td><?= $account['parentName'] ? htmlspecialchars($account['parentName']) : '-' ?></td>
                    <td><?= $account['level'] ?></td>
                    <td>
                        <span class="badge badge-<?= $account['isParent'] ? 'info' : 'secondary' ?>">
                            <?= $account['isParent'] ? 'نعم' : 'لا' ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-<?= $account['allowPosting'] ? 'success' : 'warning' ?>">
                            <?= $account['allowPosting'] ? 'نعم' : 'لا' ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-<?= $account['isActive'] ? 'success' : 'danger' ?>">
                            <?= $account['isActive'] ? 'نشط' : 'غير نشط' ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="viewAccount(<?= $account['id'] ?>)">عرض</button>
                        <button class="btn btn-sm btn-warning" onclick="editAccount(<?= $account['id'] ?>)">تعديل</button>
                        <?php if (!$account['isParent']): ?>
                        <button class="btn btn-sm btn-danger" onclick="deleteAccount(<?= $account['id'] ?>)">حذف</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="accountModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">إضافة حساب جديد</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="accountForm" onsubmit="saveAccount(event)">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="accountId">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>رقم الحساب *</label>
                        <input type="text" name="code" id="code" required>
                        <small>مثال: 1111 أو 2111</small>
                    </div>
                    <div class="form-group">
                        <label>اسم الحساب (عربي) *</label>
                        <input type="text" name="nameAr" id="nameAr" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>اسم الحساب (إنجليزي)</label>
                        <input type="text" name="nameEn" id="nameEn">
                    </div>
                    <div class="form-group">
                        <label>النوع *</label>
                        <select name="type" id="type" required>
                            <option value="">اختر النوع</option>
                            <option value="asset">أصول</option>
                            <option value="liability">خصوم</option>
                            <option value="equity">حقوق ملكية</option>
                            <option value="revenue">إيرادات</option>
                            <option value="expense">مصروفات</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>الحساب الأب</label>
                        <select name="parentId" id="parentId">
                            <option value="">لا يوجد (حساب رئيسي)</option>
                            <?php foreach ($parentAccounts as $parent): ?>
                            <option value="<?= $parent['id'] ?>">
                                <?= $parent['code'] ?> - <?= htmlspecialchars($parent['nameAr']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>المستوى *</label>
                        <input type="number" name="level" id="level" min="1" max="10" value="1" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>الوصف</label>
                    <textarea name="description" id="description" rows="3"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="isParent" id="isParent" value="1">
                            حساب رئيسي (يحتوي على حسابات فرعية)
                        </label>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="allowPosting" id="allowPosting" value="1" checked>
                            يسمح بالترحيل (إدخال قيود)
                        </label>
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
    document.getElementById('modalTitle').textContent = 'إضافة حساب جديد';
    document.getElementById('formAction').value = 'add';
    document.getElementById('accountForm').reset();
    document.getElementById('allowPosting').checked = true;
    document.getElementById('isActive').checked = true;
    document.getElementById('accountModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('accountModal').style.display = 'none';
}

function saveAccount(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    fetch('api/accounts-manage.php', {
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
    })
    .catch(error => {
        alert('حدث خطأ: ' + error);
    });
}

function editAccount(id) {
    fetch('api/accounts-manage.php?action=get&id=' + id)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const account = data.data;
            document.getElementById('modalTitle').textContent = 'تعديل الحساب';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('accountId').value = account.id;
            document.getElementById('code').value = account.code;
            document.getElementById('nameAr').value = account.nameAr;
            document.getElementById('nameEn').value = account.nameEn || '';
            document.getElementById('type').value = account.type;
            document.getElementById('parentId').value = account.parentId || '';
            document.getElementById('level').value = account.level;
            document.getElementById('description').value = account.description || '';
            document.getElementById('isParent').checked = account.isParent == 1;
            document.getElementById('allowPosting').checked = account.allowPosting == 1;
            document.getElementById('isActive').checked = account.isActive == 1;
            document.getElementById('accountModal').style.display = 'block';
        }
    });
}

function viewAccount(id) {
    window.location.href = 'account-details.php?id=' + id;
}

function deleteAccount(id) {
    if (confirm('هل أنت متأكد من حذف هذا الحساب؟\n\nتحذير: سيتم حذف جميع الحسابات الفرعية أيضاً!')) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);
        
        fetch('api/accounts-manage.php', {
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
    const levelValue = document.getElementById('levelFilter').value;
    const statusValue = document.getElementById('statusFilter').value;
    
    const table = document.getElementById('accountsTable');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const code = row.cells[0].textContent.toLowerCase();
        const name = row.cells[1].textContent.toLowerCase();
        const type = row.getAttribute('data-type');
        const level = row.getAttribute('data-level');
        const status = row.getAttribute('data-status');
        
        let show = true;
        
        if (searchValue && !code.includes(searchValue) && !name.includes(searchValue)) {
            show = false;
        }
        
        if (typeValue && type !== typeValue) {
            show = false;
        }
        
        if (levelValue && level !== levelValue) {
            show = false;
        }
        
        if (statusValue !== '' && status !== statusValue) {
            show = false;
        }
        
        row.style.display = show ? '' : 'none';
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('accountModal');
    if (event.target == modal) {
        closeModal();
    }
}

// Auto-calculate level based on code
document.getElementById('code').addEventListener('input', function() {
    const code = this.value;
    const level = code.length;
    document.getElementById('level').value = level;
});
</script>

<style>
.filters {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    gap: 15px;
}

.filters input,
.filters select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.badge-info {
    background-color: #17a2b8;
    color: white;
}

.badge-secondary {
    background-color: #6c757d;
    color: white;
}

.badge-warning {
    background-color: #ffc107;
    color: #333;
}
</style>

<?php require_once 'includes/footer.php'; ?>
