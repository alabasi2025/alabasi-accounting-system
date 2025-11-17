@extends('layouts.app')

@section('title', 'دليل الحسابات')

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-book text-primary me-2"></i>
            دليل الحسابات
        </h2>
        <a href="{{ route('accounts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            إضافة حساب جديد
        </a>
    </div>

    <!-- Filters Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small text-muted">المؤسسة:</label>
                    <select class="form-select form-select-sm" onchange="window.location.href='/accounts?company_id='+this.value">
                        @foreach($companies as $comp)
                            <option value="{{ $comp->id }}" {{ $company->id == $comp->id ? 'selected' : '' }}>
                                {{ $comp->company_name }} ({{ $comp->company_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">الوحدة:</label>
                    <input type="text" class="form-control form-control-sm" value="{{ $company->unit->name ?? '-' }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted">بحث:</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="searchInput" class="form-control" placeholder="بحث بالرمز أو الاسم...">
                    </div>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-3">
                    <select id="levelFilter" class="form-select form-select-sm">
                        <option value="">جميع المستويات</option>
                        <option value="1">المستوى 1</option>
                        <option value="2">المستوى 2</option>
                        <option value="3">المستوى 3</option>
                        <option value="4">المستوى 4</option>
                        <option value="5">المستوى 5</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="typeFilter" class="form-select form-select-sm">
                        <option value="">جميع الأنواع</option>
                        <option value="parent">حسابات رئيسية</option>
                        <option value="sub">حسابات فرعية</option>
                        <option value="posting">قابلة للترحيل</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="statusFilter" class="form-select form-select-sm">
                        <option value="">جميع الحالات</option>
                        <option value="1">نشط</option>
                        <option value="0">غير نشط</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-sm btn-outline-secondary w-100" onclick="clearFilters()">
                        <i class="fas fa-redo me-1"></i> إعادة تعيين
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content: Tree + Details -->
    <div class="row">
        <!-- Tree View (Right Side) -->
        <div class="col-md-5">
            <div class="card shadow-sm" style="height: calc(100vh - 300px);">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-sitemap me-2"></i>
                        شجرة الحسابات
                    </h6>
                </div>
                <div class="card-body p-0" style="overflow-y: auto;">
                    <div id="accountsTree" class="accounts-tree">
                        @foreach($accounts as $account)
                            @include('accounts.partials.tree-item-onyx', ['account' => $account, 'level' => 0])
                        @endforeach
                    </div>
                </div>
                <div class="card-footer bg-light text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    إجمالي الحسابات: <span class="fw-bold">{{ $accounts->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Details View (Left Side) -->
        <div class="col-md-7">
            <div class="card shadow-sm" style="height: calc(100vh - 300px);">
                <div class="card-header bg-light">
                    <span class="fw-bold">
                        <i class="fas fa-file-alt me-2 text-primary"></i>
                        تفاصيل الحساب
                    </span>
                </div>
                <div class="card-body" style="overflow-y: auto;">
                    <div id="accountDetails" class="text-center text-muted py-5">
                        <i class="fas fa-hand-pointer fa-3x mb-3 opacity-25"></i>
                        <p>اختر حساباً من الشجرة لعرض تفاصيله</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.accounts-tree {
    font-family: 'Cairo', sans-serif;
}

.tree-item {
    padding: 8px 12px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
}

.tree-item:hover {
    background-color: #f8f9fa;
}

.tree-item.selected {
    background-color: #e3f2fd;
    border-right: 3px solid #2196F3;
}

.tree-item .toggle-icon {
    width: 20px;
    text-align: center;
    margin-left: 5px;
    color: #666;
    font-size: 12px;
}

.tree-item .account-icon {
    width: 24px;
    height: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    margin-left: 8px;
    font-size: 12px;
}

.tree-item .account-code {
    font-weight: 600;
    color: #1976D2;
    min-width: 80px;
    margin-left: 10px;
}

.tree-item .account-name {
    flex: 1;
    color: #333;
}

.tree-item .account-badge {
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 3px;
    margin-right: 5px;
}

/* Level Colors */
.tree-item[data-level="0"] {
    background-color: #f5f5f5;
    font-weight: bold;
}

.tree-item[data-level="0"] .account-icon {
    background-color: #1976D2;
    color: white;
}

.tree-item[data-level="1"] .account-icon {
    background-color: #42A5F5;
    color: white;
}

.tree-item[data-level="2"] .account-icon {
    background-color: #90CAF9;
    color: white;
}

.tree-item[data-level="3"] .account-icon {
    background-color: #BBDEFB;
    color: #333;
}

.tree-item[data-level="4"] .account-icon {
    background-color: #E3F2FD;
    color: #333;
}

.tree-children {
    display: none !important;
}

.tree-children.expanded {
    display: block !important;
}

/* Details Panel */
.detail-row {
    display: flex;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
}

.detail-label {
    font-weight: 600;
    color: #666;
    width: 150px;
}

.detail-value {
    flex: 1;
    color: #333;
}

.action-buttons {
    margin-top: 20px;
}
</style>

<script>
// Tree Navigation
function selectAccount(accountId) {
    // Remove previous selection
    document.querySelectorAll('.tree-item').forEach(item => {
        item.classList.remove('selected');
    });
    
    // Add selection to clicked item
    const item = document.querySelector(`[data-account-id="${accountId}"]`);
    if (item) {
        item.classList.add('selected');
    }
    
    // Load account details via AJAX
    loadAccountDetails(accountId);
}

function toggleChildren(accountId) {
    const children = document.querySelector(`#children-${accountId}`);
    const icon = document.querySelector(`#icon-${accountId}`);
    
    if (children) {
        if (children.classList.contains('expanded')) {
            children.classList.remove('expanded');
            if (icon) icon.className = 'fas fa-plus-square toggle-icon';
        } else {
            children.classList.add('expanded');
            if (icon) icon.className = 'fas fa-minus-square toggle-icon';
        }
    }
}

function expandAll() {
    document.querySelectorAll('.tree-children').forEach(el => {
        el.classList.add('expanded');
    });
    document.querySelectorAll('.toggle-icon').forEach(icon => {
        icon.className = 'fas fa-minus-square toggle-icon';
    });
}

function collapseAll() {
    document.querySelectorAll('.tree-children').forEach(el => {
        el.classList.remove('expanded');
    });
    document.querySelectorAll('.toggle-icon').forEach(icon => {
        icon.className = 'fas fa-plus-square toggle-icon';
    });
}

function loadAccountDetails(accountId) {
    // AJAX call to load account details
    fetch(`/accounts/${accountId}/details`)
        .then(response => response.json())
        .then(data => {
            displayAccountDetails(data);
        })
        .catch(error => {
            console.error('Error loading account details:', error);
        });
}

function displayAccountDetails(account) {
    const detailsDiv = document.getElementById('accountDetails');
    detailsDiv.innerHTML = `
        <div class="detail-row">
            <div class="detail-label">رمز الحساب:</div>
            <div class="detail-value"><strong>${account.code}</strong></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">اسم الحساب:</div>
            <div class="detail-value">${account.name_ar}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">الاسم بالإنجليزية:</div>
            <div class="detail-value">${account.name_en || '-'}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">نوع الحساب:</div>
            <div class="detail-value">${account.is_parent ? 'حساب رئيسي' : 'حساب فرعي'}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">النوع التحليلي:</div>
            <div class="detail-value">${account.analytical_type || '-'}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">الوصف:</div>
            <div class="detail-value">${account.description || '-'}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">الحالة:</div>
            <div class="detail-value">
                <span class="badge ${account.is_active ? 'bg-success' : 'bg-secondary'}">
                    ${account.is_active ? 'نشط' : 'غير نشط'}
                </span>
            </div>
        </div>
        <div class="action-buttons">
            <a href="/accounts/${account.id}/edit" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i> تعديل
            </a>
            <a href="/accounts/create?parent_id=${account.id}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i> إضافة حساب فرعي
            </a>
            <button class="btn btn-danger" onclick="deleteAccount(${account.id})">
                <i class="fas fa-trash me-1"></i> حذف
            </button>
        </div>
    `;
}

// Search and Filter
document.getElementById('searchInput')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    document.querySelectorAll('.tree-item').forEach(item => {
        const code = item.querySelector('.account-code')?.textContent.toLowerCase() || '';
        const name = item.querySelector('.account-name')?.textContent.toLowerCase() || '';
        
        if (code.includes(searchTerm) || name.includes(searchTerm)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
});

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('levelFilter').value = '';
    document.getElementById('typeFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.querySelectorAll('.tree-item').forEach(item => {
        item.style.display = 'flex';
    });
}
</script>
@endsection
