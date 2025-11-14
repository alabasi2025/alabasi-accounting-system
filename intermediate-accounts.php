<?php
/**
 * صفحة ربط الحسابات الوسيطة
 * Intermediate Accounts Mapping
 */

require_once 'includes/db.php';
require_once 'includes/functions.php';

// التحقق من تسجيل الدخول
requireLogin();

$pageTitle = 'ربط الحسابات الوسيطة';

// جلب جميع الربطات
try {
    $mappings = $pdo->query("
        SELECT m.*,
               sa.code as sourceAccountCode, sa.nameAr as sourceAccountName,
               ta.code as targetAccountCode, ta.nameAr as targetAccountName
        FROM intermediate_accounts_mapping m
        LEFT JOIN accounts sa ON m.sourceAccountId = sa.id
        LEFT JOIN accounts ta ON m.targetAccountId = ta.id
        ORDER BY m.entityType, m.id DESC
    ")->fetchAll();
} catch (PDOException $e) {
    $mappings = [];
}

// جلب الكيانات للقوائم المنسدلة
$units = $pdo->query("SELECT id, code, nameAr FROM units WHERE isActive = 1")->fetchAll();
$companies = $pdo->query("SELECT id, code, nameAr FROM companies WHERE isActive = 1")->fetchAll();
$branches = $pdo->query("SELECT id, code, nameAr FROM branches WHERE isActive = 1")->fetchAll();

require_once 'includes/header.php';
?>

<div class="page-container">
    <div class="page-header">
        <h1>🔗 <?php echo $pageTitle; ?></h1>
        <p>إدارة الحسابات الوسيطة للعمليات بين الوحدات والمؤسسات والفروع</p>
    </div>

    <!-- معلومات توضيحية -->
    <div class="info-box" style="background: #e3f2fd; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
        <h3 style="color: #1976d2; margin-top: 0;">📋 كيفية عمل الحسابات الوسيطة:</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 15px;">
            <div>
                <h4>🏢 بين المؤسسات</h4>
                <p>عند تحويل 10,000 من مؤسسة 1 → مؤسسة 2:</p>
                <ul>
                    <li><strong>مؤسسة 1:</strong> من ح/ وسيط مؤسسة 2 (مدين 10,000)</li>
                    <li><strong>مؤسسة 2:</strong> إلى ح/ وسيط مؤسسة 1 (دائن 10,000)</li>
                </ul>
            </div>

            <div>
                <h4>🏛️ بين الوحدات</h4>
                <p>عند تحويل مصروف من وحدة 1 → وحدة 2:</p>
                <ul>
                    <li><strong>وحدة 1:</strong> من ح/ وسيط وحدة 2</li>
                    <li><strong>وحدة 2:</strong> إلى ح/ وسيط وحدة 1</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- أزرار الإجراءات -->
    <div class="actions-bar">
        <button class="btn btn-primary" onclick="openAddModal()">
            ➕ إضافة ربط جديد
        </button>
    </div>

    <!-- التبويبات -->
    <div class="tabs">
        <button class="tab-btn active" onclick="filterByType('all')">الكل (<?php echo count($mappings); ?>)</button>
        <button class="tab-btn" onclick="filterByType('unit')">الوحدات</button>
        <button class="tab-btn" onclick="filterByType('company')">المؤسسات</button>
    </div>

    <!-- جدول الربطات -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>النوع</th>
                    <th>الكيان المصدر</th>
                    <th>الحساب الوسيط (المصدر)</th>
                    <th>↔️</th>
                    <th>الكيان الهدف</th>
                    <th>الحساب الوسيط (الهدف)</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody id="mappingsTable">
                <?php if (empty($mappings)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: #999;">
                            <div style="font-size: 48px; margin-bottom: 10px;">🔗</div>
                            <p>لا توجد ربطات حالياً</p>
                            <p style="font-size: 14px;">قم بإضافة ربط جديد للبدء</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($mappings as $mapping): ?>
                        <tr data-type="<?php echo $mapping['entityType']; ?>">
                            <td>
                                <?php
                $typeLabels = [
                    'unit' => '🏛️ وحدة',
                    'company' => '🏢 مؤسسة'
                ];
                                echo $typeLabels[$mapping['entityType']];
                                ?>
                            </td>
                            <td><?php echo $mapping['sourceEntityId']; ?></td>
                            <td>
                                <div style="font-weight: bold;"><?php echo $mapping['sourceAccountCode']; ?></div>
                                <div style="font-size: 12px; color: #666;"><?php echo $mapping['sourceAccountName']; ?></div>
                            </td>
                            <td style="text-align: center; font-size: 20px;">↔️</td>
                            <td><?php echo $mapping['targetEntityId']; ?></td>
                            <td>
                                <div style="font-weight: bold;"><?php echo $mapping['targetAccountCode']; ?></div>
                                <div style="font-size: 12px; color: #666;"><?php echo $mapping['targetAccountName']; ?></div>
                            </td>
                            <td>
                                <span class="badge <?php echo $mapping['isActive'] ? 'badge-success' : 'badge-danger'; ?>">
                                    <?php echo $mapping['isActive'] ? '✓ نشط' : '✗ غير نشط'; ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn-icon" onclick="editMapping(<?php echo $mapping['id']; ?>)" title="تعديل">
                                    ✏️
                                </button>
                                <button class="btn-icon" onclick="deleteMapping(<?php echo $mapping['id']; ?>)" title="حذف">
                                    🗑️
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: إضافة/تعديل ربط -->
<div id="mappingModal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h2 id="modalTitle">إضافة ربط جديد</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <form id="mappingForm" onsubmit="saveMapping(event)">
            <input type="hidden" id="mappingId" name="id">
            
            <div class="form-group">
                <label>نوع الكيان *</label>
                <select id="entityType" name="entityType" required onchange="loadEntities()">
                    <option value="">-- اختر النوع --</option>
                    <option value="unit">🏛️ وحدة</option>
                    <option value="company">🏢 مؤسسة</option>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>الكيان المصدر *</label>
                    <select id="sourceEntityId" name="sourceEntityId" required onchange="loadSourceAccounts()">
                        <option value="">-- اختر الكيان --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>الكيان الهدف *</label>
                    <select id="targetEntityId" name="targetEntityId" required onchange="loadTargetAccounts()">
                        <option value="">-- اختر الكيان --</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>الحساب الوسيط (المصدر) *</label>
                    <select id="sourceAccountId" name="sourceAccountId" required>
                        <option value="">-- اختر الحساب --</option>
                    </select>
                    <small>هذا الحساب سيظهر في دفاتر الكيان المصدر</small>
                </div>
                <div class="form-group">
                    <label>الحساب الوسيط (الهدف) *</label>
                    <select id="targetAccountId" name="targetAccountId" required>
                        <option value="">-- اختر الحساب --</option>
                    </select>
                    <small>هذا الحساب سيظهر في دفاتر الكيان الهدف</small>
                </div>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" id="isActive" name="isActive" checked>
                    نشط
                </label>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">إلغاء</button>
                <button type="submit" class="btn btn-primary">حفظ</button>
            </div>
        </form>
    </div>
</div>

<script>
// البيانات من PHP
const units = <?php echo json_encode($units); ?>;
const companies = <?php echo json_encode($companies); ?>;
const branches = <?php echo json_encode($branches); ?>;

// فتح نافذة الإضافة
function openAddModal() {
    document.getElementById('mappingId').value = '';
    document.getElementById('modalTitle').textContent = 'إضافة ربط جديد';
    document.getElementById('mappingForm').reset();
    document.getElementById('mappingModal').style.display = 'flex';
}

// إغلاق النافذة
function closeModal() {
    document.getElementById('mappingModal').style.display = 'none';
}

// تحميل الكيانات حسب النوع
function loadEntities() {
    const type = document.getElementById('entityType').value;
    const sourceSelect = document.getElementById('sourceEntityId');
    const targetSelect = document.getElementById('targetEntityId');
    
    sourceSelect.innerHTML = '<option value="">-- اختر الكيان --</option>';
    targetSelect.innerHTML = '<option value="">-- اختر الكيان --</option>';
    
    let entities = [];
    if (type === 'unit') entities = units;
    else if (type === 'company') entities = companies;
    else if (type === 'branch') entities = branches;
    
    entities.forEach(entity => {
        sourceSelect.innerHTML += `<option value="${entity.id}">${entity.code} - ${entity.nameAr}</option>`;
        targetSelect.innerHTML += `<option value="${entity.id}">${entity.code} - ${entity.nameAr}</option>`;
    });
}

// تحميل حسابات المصدر
function loadSourceAccounts() {
    const entityId = document.getElementById('sourceEntityId').value;
    if (!entityId) return;
    
    // TODO: جلب الحسابات من API
    console.log('Loading source accounts for entity:', entityId);
}

// تحميل حسابات الهدف
function loadTargetAccounts() {
    const entityId = document.getElementById('targetEntityId').value;
    if (!entityId) return;
    
    // TODO: جلب الحسابات من API
    console.log('Loading target accounts for entity:', entityId);
}

// حفظ الربط
function saveMapping(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    fetch('api/intermediate_accounts_api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('تم الحفظ بنجاح');
            location.reload();
        } else {
            alert('خطأ: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء الحفظ');
    });
}

// تعديل ربط
function editMapping(id) {
    // TODO: جلب البيانات وملء النموذج
    console.log('Edit mapping:', id);
}

// حذف ربط
function deleteMapping(id) {
    if (!confirm('هل أنت متأكد من حذف هذا الربط؟')) return;
    
    fetch(`api/intermediate_accounts_api.php?action=delete&id=${id}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('تم الحذف بنجاح');
            location.reload();
        } else {
            alert('خطأ: ' + data.message);
        }
    });
}

// فلترة حسب النوع
function filterByType(type) {
    const rows = document.querySelectorAll('#mappingsTable tr[data-type]');
    const tabs = document.querySelectorAll('.tab-btn');
    
    tabs.forEach(tab => tab.classList.remove('active'));
    event.target.classList.add('active');
    
    rows.forEach(row => {
        if (type === 'all' || row.dataset.type === type) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>

<style>
.info-box ul {
    margin: 10px 0;
    padding-left: 20px;
}

.info-box li {
    margin: 5px 0;
    font-size: 14px;
}

.tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    border-bottom: 2px solid #e0e0e0;
}

.tab-btn {
    padding: 10px 20px;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 14px;
    color: #666;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
}

.tab-btn.active {
    color: #6366f1;
    border-bottom-color: #6366f1;
    font-weight: bold;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>
