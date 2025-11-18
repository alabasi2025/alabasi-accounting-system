@extends('layouts.admin')

@section('page-title', 'إدارة التخزين')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">التخزين</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Storage Disks -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-hdd fa-3x text-primary mb-3"></i>
                    <h5>Local</h5>
                    <p class="text-muted">التخزين المحلي</p>
                    <span class="badge bg-success">نشط</span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fab fa-aws fa-3x text-warning mb-3"></i>
                    <h5>S3</h5>
                    <p class="text-muted">Amazon S3</p>
                    <span class="badge bg-secondary">معطل</span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-globe fa-3x text-info mb-3"></i>
                    <h5>Public</h5>
                    <p class="text-muted">الملفات العامة</p>
                    <span class="badge bg-success">نشط</span>
                </div>
            </div>
        </div>
    </div>

    <!-- File Storage System -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-folder"></i> نظام إدارة الملفات</h5>
                </div>
                <div class="card-body">
                    <p class="lead">نظام تخزين ملفات متقدم يدعم التخزين المحلي والسحابي</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6><i class="fas fa-check-circle text-success"></i> الميزات:</h6>
                            <ul>
                                <li>رفع ملفات متعددة</li>
                                <li>معاينة الصور</li>
                                <li>تخزين محلي وسحابي</li>
                                <li>إدارة الصلاحيات</li>
                                <li>ضغط الصور تلقائياً</li>
                                <li>Symbolic Links</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-chart-pie text-info"></i> الإحصائيات:</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>إجمالي الملفات:</td>
                                    <td><strong>0</strong></td>
                                </tr>
                                <tr>
                                    <td>المساحة المستخدمة:</td>
                                    <td><strong>0 MB</strong></td>
                                </tr>
                                <tr>
                                    <td>المساحة المتاحة:</td>
                                    <td><strong>∞</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <button class="btn btn-primary" onclick="createSymlink()">
                        <i class="fas fa-link"></i> إنشاء Symbolic Link
                    </button>
                    <button class="btn btn-warning" onclick="clearTemp()">
                        <i class="fas fa-trash"></i> مسح الملفات المؤقتة
                    </button>
                    <button class="btn btn-info" onclick="optimizeImages()">
                        <i class="fas fa-image"></i> تحسين الصور
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function createSymlink() {
    alert('⏳ جاري إنشاء Symbolic Link...');
}

function clearTemp() {
    if (confirm('هل تريد مسح جميع الملفات المؤقتة؟')) {
        alert('⏳ جاري المسح...');
    }
}

function optimizeImages() {
    alert('⏳ جاري تحسين الصور...');
}
</script>
@endpush
