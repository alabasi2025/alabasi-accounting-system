<?php
/**
 * صفحة قائمة الحسابات الوسيطة
 * Intermediate Accounts List
 */

require_once 'includes/db.php';
require_once 'includes/functions.php';

// التحقق من تسجيل الدخول
// requireLogin(); // معطل مؤقتاً للاختبار

$pageTitle = 'قائمة الحسابات الوسيطة';

require_once 'includes/header.php';
?>

<div class="page-container">
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1>📋 <?php echo $pageTitle; ?></h1>
            <p style="margin: 5px 0 0 0; color: #666;">عرض جميع الحسابات الوسيطة للوحدات والمؤسسات</p>
        </div>
        <a href="intermediate-account-add.php" class="btn btn-primary" style="background: #10b981; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            ➕ إضافة حساب وسيط جديد
        </a>
    </div>

    <!-- الإحصائيات السريعة -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">إجمالي الحسابات</div>
            <div style="font-size: 32px; font-weight: bold;" id="totalAccounts">-</div>
            <div style="font-size: 12px; opacity: 0.8; margin-top: 8px;" id="accountsBreakdown">-</div>
        </div>

        <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">الرصيد الكلي</div>
            <div style="font-size: 32px; font-weight: bold;" id="totalBalance">-</div>
            <div style="font-size: 12px; opacity: 0.8; margin-top: 8px;">مجموع جميع الأرصدة</div>
        </div>

        <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">📈 أرصدة دائنة</div>
            <div style="font-size: 32px; font-weight: bold;" id="positiveCount">-</div>
            <div style="font-size: 12px; opacity: 0.8; margin-top: 8px;">حساب</div>
        </div>

        <div class="stat-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">📉 أرصدة مدينة</div>
            <div style="font-size: 32px; font-weight: bold;" id="negativeCount">-</div>
            <div style="font-size: 12px; opacity: 0.8; margin-top: 8px;">حساب</div>
        </div>
    </div>

    <!-- البحث والفلاتر -->
    <div class="card" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; font-size: 18px;">🔍 البحث والفلاتر</h3>
            <div style="display: flex; gap: 10px;">
                <button onclick="resetFilters()" class="btn-secondary" style="padding: 8px 16px; border: 1px solid #ddd; background: white; border-radius: 6px; cursor: pointer;">
                    ❌ إعادة تعيين
                </button>
                <button onclick="toggleFilters()" class="btn-primary" id="toggleFiltersBtn" style="padding: 8px 16px; background: #667eea; color: white; border: none; border-radius: 6px; cursor: pointer;">
                    🔽 إظهار الفلاتر
                </button>
            </div>
        </div>

        <!-- حقل البحث -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500;">البحث</label>
            <input type="text" id="searchInput" placeholder="ابحث برقم الحساب أو رقم الكيان..." style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
        </div>

        <!-- الفلاتر المتقدمة -->
        <div id="advancedFilters" style="display: none; padding-top: 20px; border-top: 1px solid #eee;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">نوع الكيان</label>
                    <select id="filterType" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                        <option value="">الكل</option>
                        <option value="unit">وحدة</option>
                        <option value="organization">مؤسسة</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">الحالة</label>
                    <select id="filterStatus" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                        <option value="">الكل</option>
                        <option value="active">نشط</option>
                        <option value="inactive">غير نشط</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول الحسابات -->
    <div class="card" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; font-size: 18px;">📊 قائمة الحسابات</h3>
            <div id="resultsCount" style="color: #666; font-size: 14px;">-</div>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: right; font-weight: 600;">رقم الحساب</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600;">نوع الكيان</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600;">رقم الكيان</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600;">الرصيد</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600;">حالة الرصيد</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600;">الحالة</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600;">تاريخ الإنشاء</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody id="accountsTableBody">
                    <tr>
                        <td colspan="8" style="padding: 40px; text-align: center; color: #999;">
                            <div class="loading">جاري التحميل...</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
let allAccounts = [];
let filteredAccounts = [];

// تحميل البيانات
async function loadAccounts() {
    try {
        const response = await fetch('api/intermediate-accounts-list.php');
        const data = await response.json();
        
        if (data.success) {
            allAccounts = data.accounts;
            applyFilters();
            updateStats();
        } else {
            showError('فشل تحميل البيانات: ' + data.message);
        }
    } catch (error) {
        showError('خطأ في الاتصال بالخادم');
        console.error(error);
    }
}

// تطبيق الفلاتر
function applyFilters() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const filterType = document.getElementById('filterType').value;
    const filterStatus = document.getElementById('filterStatus').value;

    filteredAccounts = allAccounts.filter(account => {
        const matchesSearch = searchTerm === '' || 
            account.accountId.toString().includes(searchTerm) ||
            account.entityId.toString().includes(searchTerm);

        const matchesType = filterType === '' || account.entityType === filterType;

        const matchesStatus = filterStatus === '' || 
            (filterStatus === 'active' && account.isActive == 1) ||
            (filterStatus === 'inactive' && account.isActive == 0);

        return matchesSearch && matchesType && matchesStatus;
    });

    renderTable();
    updateResultsCount();
}

// عرض الجدول
function renderTable() {
    const tbody = document.getElementById('accountsTableBody');
    
    if (filteredAccounts.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" style="padding: 40px; text-align: center; color: #999;">لا توجد حسابات وسيطة</td></tr>';
        return;
    }

    tbody.innerHTML = filteredAccounts.map(account => {
        const balance = parseFloat(account.balance || 0);
        const balanceBadge = balance > 0 
            ? '<span style="background: #10b981; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px;">📈 دائن</span>'
            : balance < 0 
            ? '<span style="background: #ef4444; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px;">📉 مدين</span>'
            : '<span style="background: #6b7280; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px;">متوازن</span>';

        const typeBadge = account.entityType === 'unit'
            ? '<span style="background: #3b82f6; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px;">🏛️ وحدة</span>'
            : '<span style="background: #8b5cf6; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px;">🏢 مؤسسة</span>';

        const statusBadge = account.isActive == 1
            ? '<span style="background: #10b981; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px;">نشط</span>'
            : '<span style="background: #6b7280; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px;">غير نشط</span>';

        return `
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 12px; font-weight: 600;">${account.accountId}</td>
                <td style="padding: 12px;">${typeBadge}</td>
                <td style="padding: 12px;">${account.entityId}</td>
                <td style="padding: 12px; font-weight: 600;">${balance.toLocaleString('ar-SA')} ريال</td>
                <td style="padding: 12px;">${balanceBadge}</td>
                <td style="padding: 12px;">${statusBadge}</td>
                <td style="padding: 12px;">${new Date(account.createdAt).toLocaleDateString('ar-SA')}</td>
                <td style="padding: 12px;">
                    <a href="intermediate-account-details.php?id=${account.id}" style="background: #667eea; color: white; padding: 6px 16px; border-radius: 6px; text-decoration: none; font-size: 13px;">
                        👁️ عرض التفاصيل
                    </a>
                </td>
            </tr>
        `;
    }).join('');
}

// تحديث الإحصائيات
function updateStats() {
    const total = allAccounts.length;
    const units = allAccounts.filter(a => a.entityType === 'unit').length;
    const organizations = allAccounts.filter(a => a.entityType === 'organization').length;
    
    const totalBalance = allAccounts.reduce((sum, a) => sum + parseFloat(a.balance || 0), 0);
    const positiveCount = allAccounts.filter(a => parseFloat(a.balance) > 0).length;
    const negativeCount = allAccounts.filter(a => parseFloat(a.balance) < 0).length;

    document.getElementById('totalAccounts').textContent = total;
    document.getElementById('accountsBreakdown').textContent = `${units} وحدة • ${organizations} مؤسسة`;
    document.getElementById('totalBalance').textContent = totalBalance.toLocaleString('ar-SA') + ' ريال';
    document.getElementById('positiveCount').textContent = positiveCount;
    document.getElementById('negativeCount').textContent = negativeCount;
}

// تحديث عدد النتائج
function updateResultsCount() {
    document.getElementById('resultsCount').textContent = 
        `عرض ${filteredAccounts.length} من أصل ${allAccounts.length} حساب`;
}

// إظهار/إخفاء الفلاتر
function toggleFilters() {
    const filters = document.getElementById('advancedFilters');
    const btn = document.getElementById('toggleFiltersBtn');
    
    if (filters.style.display === 'none') {
        filters.style.display = 'block';
        btn.textContent = '🔼 إخفاء الفلاتر';
    } else {
        filters.style.display = 'none';
        btn.textContent = '🔽 إظهار الفلاتر';
    }
}

// إعادة تعيين الفلاتر
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterType').value = '';
    document.getElementById('filterStatus').value = '';
    applyFilters();
}

// عرض رسالة خطأ
function showError(message) {
    const tbody = document.getElementById('accountsTableBody');
    tbody.innerHTML = `<tr><td colspan="8" style="padding: 40px; text-align: center; color: #ef4444;">${message}</td></tr>`;
}

// Event Listeners
document.getElementById('searchInput').addEventListener('input', applyFilters);
document.getElementById('filterType').addEventListener('change', applyFilters);
document.getElementById('filterStatus').addEventListener('change', applyFilters);

// تحميل البيانات عند فتح الصفحة
loadAccounts();
</script>

<?php require_once 'includes/footer.php'; ?>
