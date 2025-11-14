<?php
/**
 * صفحة إدارة الأصناف
 * Inventory Items Management Page
 */

// التحقق من تسجيل الدخول
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../includes/db.php';
require_once '../includes/functions.php';

// عنوان الصفحة
$pageTitle = 'إدارة الأصناف';

// تضمين الهيدر
include '../includes/header.php';
?>

<style>
    .items-container {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin: 20px;
    }
    
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #7c3aed;
    }
    
    .page-header h2 {
        color: #7c3aed;
        margin: 0;
        font-size: 28px;
    }
    
    .btn-add {
        background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
        color: white;
        padding: 12px 25px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        transition: all 0.3s;
    }
    
    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(124, 58, 237, 0.3);
    }
    
    .filters-section {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 15px;
        margin-bottom: 25px;
    }
    
    .filter-input {
        padding: 12px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 15px;
        transition: all 0.3s;
    }
    
    .filter-input:focus {
        outline: none;
        border-color: #7c3aed;
    }
    
    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    
    .items-table thead {
        background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
        color: white;
    }
    
    .items-table th {
        padding: 15px;
        text-align: right;
        font-weight: bold;
        font-size: 16px;
    }
    
    .items-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .items-table tbody tr:hover {
        background-color: #f9fafb;
    }
    
    .btn-action {
        padding: 6px 12px;
        margin: 0 3px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s;
    }
    
    .btn-edit {
        background-color: #3b82f6;
        color: white;
    }
    
    .btn-delete {
        background-color: #ef4444;
        color: white;
    }
    
    .btn-action:hover {
        transform: scale(1.05);
    }
    
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        z-index: 1000;
    }
    
    .modal-content {
        background: white;
        margin: 50px auto;
        padding: 30px;
        border-radius: 15px;
        max-width: 600px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        max-height: 80vh;
        overflow-y: auto;
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #7c3aed;
    }
    
    .modal-header h3 {
        color: #7c3aed;
        margin: 0;
    }
    
    .close-modal {
        background: none;
        border: none;
        font-size: 28px;
        cursor: pointer;
        color: #6b7280;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #374151;
        font-weight: bold;
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 15px;
        transition: all 0.3s;
        box-sizing: border-box;
    }
    
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #7c3aed;
    }
    
    .form-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 25px;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
    }
    
    .btn-cancel {
        background: #6b7280;
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
    }
    
    .no-data {
        text-align: center;
        padding: 40px;
        color: #6b7280;
        font-size: 18px;
    }
</style>

<div class="items-container">
    <div class="page-header">
        <h2>📦 إدارة الأصناف</h2>
        <button class="btn-add" onclick="openAddModal()">
            ➕ إضافة صنف جديد
        </button>
    </div>
    
    <div class="filters-section">
        <input type="text" id="searchInput" class="filter-input" placeholder="🔍 بحث بالكود أو الاسم..." onkeyup="filterItems()">
        <select id="categoryFilter" class="filter-input" onchange="filterItems()">
            <option value="">جميع الفئات</option>
            <option value="مواد_خام">مواد خام</option>
            <option value="منتجات_تامة">منتجات تامة</option>
            <option value="قطع_غيار">قطع غيار</option>
            <option value="مستلزمات">مستلزمات</option>
        </select>
        <select id="statusFilter" class="filter-input" onchange="filterItems()">
            <option value="">جميع الحالات</option>
            <option value="1">نشط</option>
            <option value="0">غير نشط</option>
        </select>
    </div>
    
    <table class="items-table" id="itemsTable">
        <thead>
            <tr>
                <th>الكود</th>
                <th>اسم الصنف</th>
                <th>الوحدة</th>
                <th>سعر الشراء</th>
                <th>سعر البيع</th>
                <th>الحد الأدنى</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody id="itemsTableBody">
            <tr>
                <td colspan="8" class="no-data">جاري التحميل...</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Modal إضافة/تعديل صنف -->
<div id="itemModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">إضافة صنف جديد</h3>
            <button class="close-modal" onclick="closeModal()">&times;</button>
        </div>
        
        <form id="itemForm" onsubmit="saveItem(event)">
            <input type="hidden" id="itemId" name="id">
            
            <div class="form-group">
                <label for="itemCode">كود الصنف *</label>
                <input type="text" id="itemCode" name="code" required>
            </div>
            
            <div class="form-group">
                <label for="itemName">اسم الصنف *</label>
                <input type="text" id="itemName" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="itemUnit">الوحدة *</label>
                <input type="text" id="itemUnit" name="unit" required placeholder="مثال: كيلو، متر، قطعة">
            </div>
            
            <div class="form-group">
                <label for="purchasePrice">سعر الشراء *</label>
                <input type="number" id="purchasePrice" name="purchase_price" step="0.01" required>
            </div>
            
            <div class="form-group">
                <label for="salePrice">سعر البيع *</label>
                <input type="number" id="salePrice" name="sale_price" step="0.01" required>
            </div>
            
            <div class="form-group">
                <label for="minStock">الحد الأدنى للمخزون</label>
                <input type="number" id="minStock" name="min_stock" step="0.01" value="0">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit">💾 حفظ</button>
                <button type="button" class="btn-cancel" onclick="closeModal()">❌ إلغاء</button>
            </div>
        </form>
    </div>
</div>

<script>
    // تحميل الأصناف عند فتح الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        loadItems();
    });
    
    // تحميل الأصناف
    function loadItems() {
        fetch('api/items_api.php?action=list')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayItems(data.items);
                } else {
                    document.getElementById('itemsTableBody').innerHTML = 
                        '<tr><td colspan="8" class="no-data">لا توجد أصناف مسجلة</td></tr>';
                }
            })
            .catch(error => {
                console.error('خطأ:', error);
                document.getElementById('itemsTableBody').innerHTML = 
                    '<tr><td colspan="8" class="no-data">حدث خطأ في تحميل البيانات</td></tr>';
            });
    }
    
    // عرض الأصناف في الجدول
    function displayItems(items) {
        const tbody = document.getElementById('itemsTableBody');
        
        if (items.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="no-data">لا توجد أصناف مسجلة</td></tr>';
            return;
        }
        
        let html = '';
        items.forEach(item => {
            const statusBadge = item.is_active == 1 
                ? '<span style="color: #10b981; font-weight: bold;">✓ نشط</span>' 
                : '<span style="color: #ef4444; font-weight: bold;">✗ غير نشط</span>';
            
            html += `
                <tr>
                    <td>${item.code}</td>
                    <td>${item.name_ar}</td>
                    <td>${item.unit}</td>
                    <td>${parseFloat(item.purchase_price).toFixed(2)}</td>
                    <td>${parseFloat(item.sale_price).toFixed(2)}</td>
                    <td>${item.min_stock || 0}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <button class="btn-action btn-edit" onclick="editItem(${item.id})">✏️ تعديل</button>
                        <button class="btn-action btn-delete" onclick="deleteItem(${item.id})">🗑️ حذف</button>
                    </td>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
    }
    
    // فتح نافذة الإضافة
    function openAddModal() {
        document.getElementById('modalTitle').textContent = 'إضافة صنف جديد';
        document.getElementById('itemForm').reset();
        document.getElementById('itemId').value = '';
        document.getElementById('itemModal').style.display = 'block';
    }
    
    // تعديل صنف
    function editItem(id) {
        fetch(`api/items_api.php?action=get&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const item = data.item;
                    document.getElementById('modalTitle').textContent = 'تعديل صنف';
                    document.getElementById('itemId').value = item.id;
                    document.getElementById('itemCode').value = item.code;
                    document.getElementById('itemName').value = item.name_ar;
                    document.getElementById('itemUnit').value = item.unit;
                    document.getElementById('purchasePrice').value = item.purchase_price;
                    document.getElementById('salePrice').value = item.sale_price;
                    document.getElementById('minStock').value = item.min_stock || 0;
                    document.getElementById('itemModal').style.display = 'block';
                }
            })
            .catch(error => console.error('خطأ:', error));
    }
    
    // حفظ الصنف
    function saveItem(event) {
        event.preventDefault();
        
        const formData = new FormData(document.getElementById('itemForm'));
        const action = document.getElementById('itemId').value ? 'update' : 'create';
        formData.append('action', action);
        
        fetch('api/items_api.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ تم حفظ الصنف بنجاح');
                closeModal();
                loadItems();
            } else {
                alert('❌ خطأ: ' + data.message);
            }
        })
        .catch(error => {
            console.error('خطأ:', error);
            alert('❌ حدث خطأ أثناء الحفظ');
        });
    }
    
    // حذف صنف
    function deleteItem(id) {
        if (!confirm('هل أنت متأكد من حذف هذا الصنف؟')) {
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);
        
        fetch('api/items_api.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ تم حذف الصنف بنجاح');
                loadItems();
            } else {
                alert('❌ خطأ: ' + data.message);
            }
        })
        .catch(error => {
            console.error('خطأ:', error);
            alert('❌ حدث خطأ أثناء الحذف');
        });
    }
    
    // إغلاق النافذة المنبثقة
    function closeModal() {
        document.getElementById('itemModal').style.display = 'none';
    }
    
    // تصفية الأصناف
    function filterItems() {
        loadItems();
    }
    
    // إغلاق النافذة عند النقر خارجها
    window.onclick = function(event) {
        const modal = document.getElementById('itemModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>

<?php
// تضمين الفوتر إذا كان موجوداً
if (file_exists('../includes/footer.php')) {
    include '../includes/footer.php';
}
?>
