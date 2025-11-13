<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'admin';
    $_SESSION['role'] = 'admin';
}

$pageTitle = 'إدارة الوحدات';

// Get units with company count
$stmt = $pdo->query("
    SELECT u.*, 
           COUNT(DISTINCT c.id) as companyCount,
           u2.username as createdByName
    FROM units u
    LEFT JOIN companies c ON u.id = c.unitId
    LEFT JOIN users u2 ON u.createdBy = u2.id
    GROUP BY u.id
    ORDER BY u.id DESC
");
$units = $stmt->fetchAll();

// Get statistics
$totalUnits = count($units);
$activeUnits = count(array_filter($units, fn($u) => $u['isActive'] == 1));
$totalCompanies = array_sum(array_column($units, 'companyCount'));

require_once 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h2>🏛️ إدارة الوحدات</h2>
        <p class="page-subtitle">إدارة وحدات النظام والمؤسسات التابعة لها</p>
    </div>
    <button class="btn btn-primary btn-lg" onclick="showAddModal()">
        <span>➕</span> إضافة وحدة جديدة
    </button>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card stat-primary">
        <div class="stat-icon">🏛️</div>
        <div class="stat-details">
            <div class="stat-value"><?= $totalUnits ?></div>
            <div class="stat-label">إجمالي الوحدات</div>
        </div>
    </div>
    
    <div class="stat-card stat-success">
        <div class="stat-icon">✅</div>
        <div class="stat-details">
            <div class="stat-value"><?= $activeUnits ?></div>
            <div class="stat-label">وحدات نشطة</div>
        </div>
    </div>
    
    <div class="stat-card stat-info">
        <div class="stat-icon">🏢</div>
        <div class="stat-details">
            <div class="stat-value"><?= $totalCompanies ?></div>
            <div class="stat-label">إجمالي المؤسسات</div>
        </div>
    </div>
    
    <div class="stat-card stat-warning">
        <div class="stat-icon">⚠️</div>
        <div class="stat-details">
            <div class="stat-value"><?= $totalUnits - $activeUnits ?></div>
            <div class="stat-label">وحدات غير نشطة</div>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="card">
    <div class="card-body">
        <div class="filters-advanced">
            <div class="filter-group">
                <label>🔍 بحث</label>
                <input type="text" id="searchInput" placeholder="ابحث برمز أو اسم الوحدة..." onkeyup="filterTable()">
            </div>
            <div class="filter-group">
                <label>📊 الحالة</label>
                <select id="statusFilter" onchange="filterTable()">
                    <option value="">جميع الحالات</option>
                    <option value="1">نشط</option>
                    <option value="0">غير نشط</option>
                </select>
            </div>
            <div class="filter-group">
                <label>🏢 المؤسسات</label>
                <select id="companyFilter" onchange="filterTable()">
                    <option value="">الكل</option>
                    <option value="has">لديها مؤسسات</option>
                    <option value="empty">بدون مؤسسات</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Units Table -->
<div class="card">
    <div class="card-header">
        <h3>📋 قائمة الوحدات</h3>
        <span class="badge badge-primary"><?= count($units) ?> وحدة</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table modern-table" id="unitsTable">
                <thead>
                    <tr>
                        <th width="80">الرمز</th>
                        <th>اسم الوحدة</th>
                        <th>الاسم بالإنجليزية</th>
                        <th width="120">عدد المؤسسات</th>
                        <th width="100">الحالة</th>
                        <th width="120">تاريخ الإنشاء</th>
                        <th width="200">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($units)): ?>
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="empty-state">
                                <div class="empty-icon">📭</div>
                                <h3>لا توجد وحدات</h3>
                                <p>ابدأ بإضافة وحدة جديدة</p>
                                <button class="btn btn-primary" onclick="showAddModal()">إضافة وحدة</button>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($units as $unit): ?>
                        <tr data-status="<?= $unit['isActive'] ?>" data-companies="<?= $unit['companyCount'] ?>">
                            <td>
                                <span class="code-badge"><?= htmlspecialchars($unit['code']) ?></span>
                            </td>
                            <td>
                                <div class="unit-name">
                                    <strong><?= htmlspecialchars($unit['nameAr']) ?></strong>
                                    <?php if ($unit['description']): ?>
                                    <small><?= htmlspecialchars(substr($unit['description'], 0, 50)) ?><?= strlen($unit['description']) > 50 ? '...' : '' ?></small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($unit['nameEn'] ?? '-') ?></td>
                            <td class="text-center">
                                <?php if ($unit['companyCount'] > 0): ?>
                                <span class="badge badge-info badge-lg">
                                    🏢 <?= $unit['companyCount'] ?>
                                </span>
                                <?php else: ?>
                                <span class="badge badge-secondary">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?= $unit['isActive'] ? 'active' : 'inactive' ?>">
                                    <?= $unit['isActive'] ? '✅ نشط' : '⛔ غير نشط' ?>
                                </span>
                            </td>
                            <td><?= date('Y-m-d', strtotime($unit['createdAt'])) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-info" onclick="viewUnit(<?= $unit['id'] ?>)" title="عرض التفاصيل">
                                        👁️ عرض
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="editUnit(<?= $unit['id'] ?>)" title="تعديل">
                                        ✏️ تعديل
                                    </button>
                                    <?php if ($unit['companyCount'] == 0): ?>
                                    <button class="btn btn-sm btn-danger" onclick="deleteUnit(<?= $unit['id'] ?>)" title="حذف">
                                        🗑️ حذف
                                    </button>
                                    <?php else: ?>
                                    <button class="btn btn-sm btn-secondary" disabled title="لا يمكن حذف وحدة تحتوي على مؤسسات">
                                        🔒 حذف
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="unitModal" class="modal" style="display: none;">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h2 id="modalTitle">➕ إضافة وحدة جديدة</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="unitForm" onsubmit="saveUnit(event)">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="unitId">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>🔢 رمز الوحدة *</label>
                        <input type="text" name="code" id="code" required placeholder="مثال: UNIT001" class="form-control">
                        <small class="form-hint">رمز فريد للوحدة (حروف وأرقام فقط)</small>
                    </div>

                    <div class="form-group">
                        <label>✅ الحالة</label>
                        <div class="checkbox-wrapper">
                            <label class="checkbox-label">
                                <input type="checkbox" name="isActive" id="isActive" value="1" checked>
                                <span>وحدة نشطة</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>📝 اسم الوحدة (عربي) *</label>
                        <input type="text" name="nameAr" id="nameAr" required placeholder="مثال: الوحدة الرئيسية" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>🔤 اسم الوحدة (إنجليزي)</label>
                        <input type="text" name="nameEn" id="nameEn" placeholder="Example: Main Unit" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label>📄 الوصف</label>
                    <textarea name="description" id="description" rows="4" placeholder="وصف مختصر عن الوحدة ونشاطها..." class="form-control"></textarea>
                    <small class="form-hint">وصف اختياري يساعد في توضيح نشاط الوحدة</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-lg">
                        💾 حفظ الوحدة
                    </button>
                    <button type="button" class="btn btn-secondary btn-lg" onclick="closeModal()">
                        ❌ إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Modal -->
<div id="viewModal" class="modal" style="display: none;">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h2>📋 تفاصيل الوحدة</h2>
            <span class="close" onclick="closeViewModal()">&times;</span>
        </div>
        <div class="modal-body" id="viewContent">
            <!-- Will be filled by JavaScript -->
        </div>
    </div>
</div>

<script>
function showAddModal() {
    document.getElementById('modalTitle').innerHTML = '➕ إضافة وحدة جديدة';
    document.getElementById('formAction').value = 'add';
    document.getElementById('unitForm').reset();
    document.getElementById('isActive').checked = true;
    document.getElementById('unitModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('unitModal').style.display = 'none';
}

function closeViewModal() {
    document.getElementById('viewModal').style.display = 'none';
}

function saveUnit(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '⏳ جاري الحفظ...';
    
    fetch('api/units.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            location.reload();
        } else {
            alert('❌ خطأ: ' + data.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        alert('❌ خطأ في الاتصال: ' + error);
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

function viewUnit(id) {
    fetch('api/units.php?action=get&id=' + id)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const unit = data.data;
            const companies = data.companies || [];
            
            let companiesHtml = '';
            if (companies.length > 0) {
                companiesHtml = `
                    <div class="companies-section">
                        <h3>🏢 المؤسسات التابعة (${companies.length})</h3>
                        <div class="companies-list">
                            ${companies.map(c => `
                                <div class="company-item">
                                    <span class="company-code">${c.code}</span>
                                    <span class="company-name">${c.nameAr}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            } else {
                companiesHtml = `
                    <div class="empty-state-small">
                        <p>📭 لا توجد مؤسسات تابعة لهذه الوحدة</p>
                    </div>
                `;
            }
            
            document.getElementById('viewContent').innerHTML = `
                <div class="unit-details-view">
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label">🔢 الرمز</span>
                            <span class="detail-value code-badge">${unit.code}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">📊 الحالة</span>
                            <span class="detail-value">
                                <span class="status-badge status-${unit.isActive ? 'active' : 'inactive'}">
                                    ${unit.isActive ? '✅ نشط' : '⛔ غير نشط'}
                                </span>
                            </span>
                        </div>
                        <div class="detail-item full-width">
                            <span class="detail-label">📝 الاسم (عربي)</span>
                            <span class="detail-value">${unit.nameAr}</span>
                        </div>
                        <div class="detail-item full-width">
                            <span class="detail-label">🔤 الاسم (إنجليزي)</span>
                            <span class="detail-value">${unit.nameEn || '-'}</span>
                        </div>
                        <div class="detail-item full-width">
                            <span class="detail-label">📄 الوصف</span>
                            <span class="detail-value">${unit.description || '-'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">📅 تاريخ الإنشاء</span>
                            <span class="detail-value">${unit.createdAt}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">🔄 آخر تحديث</span>
                            <span class="detail-value">${unit.updatedAt}</span>
                        </div>
                    </div>
                    <hr>
                    ${companiesHtml}
                </div>
            `;
            document.getElementById('viewModal').style.display = 'block';
        }
    });
}

function editUnit(id) {
    fetch('api/units.php?action=get&id=' + id)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const unit = data.data;
            document.getElementById('modalTitle').innerHTML = '✏️ تعديل الوحدة';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('unitId').value = unit.id;
            document.getElementById('code').value = unit.code;
            document.getElementById('nameAr').value = unit.nameAr;
            document.getElementById('nameEn').value = unit.nameEn || '';
            document.getElementById('description').value = unit.description || '';
            document.getElementById('isActive').checked = unit.isActive == 1;
            document.getElementById('unitModal').style.display = 'block';
        }
    });
}

function deleteUnit(id) {
    if (confirm('⚠️ هل أنت متأكد من حذف هذه الوحدة؟\n\nلن تتمكن من التراجع عن هذا الإجراء.')) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);
        
        fetch('api/units.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ خطأ: ' + data.message);
            }
        });
    }
}

function filterTable() {
    const searchValue = document.getElementById('searchInput').value.toLowerCase();
    const statusValue = document.getElementById('statusFilter').value;
    const companyValue = document.getElementById('companyFilter').value;
    
    const table = document.getElementById('unitsTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        
        // Skip empty state row
        if (row.querySelector('.empty-state')) continue;
        
        const cells = row.cells;
        const code = cells[0].textContent.toLowerCase();
        const nameAr = cells[1].textContent.toLowerCase();
        const nameEn = cells[2].textContent.toLowerCase();
        const status = row.getAttribute('data-status');
        const companies = parseInt(row.getAttribute('data-companies'));
        
        let show = true;
        
        // Search filter
        if (searchValue && !code.includes(searchValue) && !nameAr.includes(searchValue) && !nameEn.includes(searchValue)) {
            show = false;
        }
        
        // Status filter
        if (statusValue && status !== statusValue) {
            show = false;
        }
        
        // Company filter
        if (companyValue === 'has' && companies === 0) {
            show = false;
        } else if (companyValue === 'empty' && companies > 0) {
            show = false;
        }
        
        row.style.display = show ? '' : 'none';
    }
}

window.onclick = function(event) {
    const modal = document.getElementById('unitModal');
    const viewModal = document.getElementById('viewModal');
    if (event.target == modal) {
        closeModal();
    }
    if (event.target == viewModal) {
        closeViewModal();
    }
}
</script>

<style>
/* Enhanced Styles */
.page-subtitle {
    color: #666;
    margin-top: 5px;
    font-size: 14px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 15px;
    border-left: 4px solid #ddd;
}

.stat-card.stat-primary { border-left-color: #4CAF50; }
.stat-card.stat-success { border-left-color: #2196F3; }
.stat-card.stat-info { border-left-color: #FF9800; }
.stat-card.stat-warning { border-left-color: #f44336; }

.stat-icon {
    font-size: 40px;
    opacity: 0.8;
}

.stat-value {
    font-size: 32px;
    font-weight: bold;
    color: #333;
}

.stat-label {
    font-size: 14px;
    color: #666;
}

.filters-advanced {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: 20px;
}

.filter-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #555;
    font-size: 14px;
}

.code-badge {
    background: #e3f2fd;
    color: #1976d2;
    padding: 4px 12px;
    border-radius: 6px;
    font-family: 'Courier New', monospace;
    font-weight: bold;
    font-size: 13px;
}

.unit-name strong {
    display: block;
    margin-bottom: 4px;
    color: #333;
}

.unit-name small {
    display: block;
    color: #999;
    font-size: 12px;
}

.status-badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
}

.status-active {
    background: #e8f5e9;
    color: #2e7d32;
}

.status-inactive {
    background: #ffebee;
    color: #c62828;
}

.action-buttons {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-icon {
    font-size: 80px;
    margin-bottom: 20px;
    opacity: 0.5;
}

.empty-state h3 {
    color: #666;
    margin-bottom: 10px;
}

.empty-state p {
    color: #999;
    margin-bottom: 25px;
}

.modal-lg {
    max-width: 700px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-hint {
    display: block;
    margin-top: 6px;
    color: #999;
    font-size: 12px;
}

.checkbox-wrapper {
    margin-top: 10px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    font-size: 15px;
}

.checkbox-label input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.unit-details-view {
    padding: 10px;
}

.details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.detail-item.full-width {
    grid-column: 1 / -1;
}

.detail-label {
    font-size: 13px;
    color: #666;
    font-weight: 600;
}

.detail-value {
    font-size: 15px;
    color: #333;
}

.companies-section {
    margin-top: 25px;
}

.companies-section h3 {
    margin-bottom: 15px;
    color: #333;
    font-size: 18px;
}

.companies-list {
    display: grid;
    gap: 10px;
}

.company-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 8px;
    border-right: 3px solid #2196F3;
}

.company-code {
    background: #e3f2fd;
    color: #1976d2;
    padding: 4px 10px;
    border-radius: 4px;
    font-family: monospace;
    font-weight: bold;
    font-size: 12px;
}

.company-name {
    font-weight: 500;
    color: #333;
}

.empty-state-small {
    text-align: center;
    padding: 30px;
    color: #999;
}

.table-responsive {
    overflow-x: auto;
}

.modern-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.modern-table thead th {
    background: #f5f5f5;
    padding: 14px;
    text-align: right;
    font-weight: 600;
    color: #555;
    border-bottom: 2px solid #ddd;
    position: sticky;
    top: 0;
}

.modern-table tbody td {
    padding: 14px;
    border-bottom: 1px solid #eee;
    vertical-align: middle;
}

.modern-table tbody tr:hover {
    background: #f9f9f9;
}

@media (max-width: 768px) {
    .filters-advanced {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .details-grid {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>
