@extends('layouts.app')

@section('title', 'الفروع')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="mb-0">
                <i class="fas fa-building text-primary"></i>
                إدارة الفروع
            </h2>
            <p class="text-muted">عرض وإدارة فروع المؤسسات</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('branches.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                إضافة فرع جديد
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i>
                        قائمة الفروع
                        <span class="badge bg-primary">{{ $branches->count() }}</span>
                    </h5>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" id="searchInput" placeholder="بحث في الفروع...">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="branchesTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">#</th>
                            <th>رمز الفرع</th>
                            <th>اسم الفرع</th>
                            <th>المؤسسة</th>
                            <th>العنوان</th>
                            <th>الهاتف</th>
                            <th>المدير</th>
                            <th style="width: 100px;">الحالة</th>
                            <th style="width: 180px;" class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($branches as $index => $branch)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $branch->branch_code }}</span>
                                </td>
                                <td>
                                    <strong>{{ $branch->branch_name }}</strong>
                                </td>
                                <td>
                                    @if($branch->company)
                                        <span class="text-primary">
                                            <i class="fas fa-building"></i>
                                            {{ $branch->company->company_name }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $branch->address ?? '-' }}</td>
                                <td>
                                    @if($branch->phone)
                                        <i class="fas fa-phone text-success"></i>
                                        {{ $branch->phone }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $branch->manager_name ?? '-' }}</td>
                                <td>
                                    @if($branch->is_active)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check"></i>
                                            نشط
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times"></i>
                                            غير نشط
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('branches.show', $branch) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('branches.edit', $branch) }}" 
                                           class="btn btn-sm btn-warning" 
                                           title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="confirmDelete({{ $branch->id }})"
                                                title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <form id="delete-form-{{ $branch->id }}" 
                                          action="{{ route('branches.destroy', $branch) }}" 
                                          method="POST" 
                                          class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">لا توجد فروع مسجلة</p>
                                    <a href="{{ route('branches.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i>
                                        إضافة فرع جديد
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .table thead th {
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .btn-group .btn {
        margin: 0 2px;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
</style>

<script>
    // البحث في الجدول
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const table = document.getElementById('branchesTable');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        
        for (let row of rows) {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchValue) ? '' : 'none';
        }
    });
    
    // تأكيد الحذف
    function confirmDelete(branchId) {
        if (confirm('هل أنت متأكد من حذف هذا الفرع؟\nهذا الإجراء لا يمكن التراجع عنه.')) {
            document.getElementById('delete-form-' + branchId).submit();
        }
    }
</script>
@endsection
