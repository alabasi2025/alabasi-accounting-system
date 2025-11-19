@extends('layouts.admin')

@section('page-title', 'إدارة قواعد البيانات')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">قواعد البيانات</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Database Info -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-database fa-3x text-primary mb-3"></i>
                    <h3>{{ count($tables ?? []) }}</h3>
                    <p class="text-muted">عدد الجداول</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-table fa-3x text-success mb-3"></i>
                    <h3>{{ array_sum(array_column($tables ?? [], 'table_rows')) }}</h3>
                    <p class="text-muted">إجمالي الصفوف</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-hdd fa-3x text-warning mb-3"></i>
                    <h3>{{ array_sum(array_column($tables ?? [], 'size_mb')) }} MB</h3>
                    <p class="text-muted">الحجم الإجمالي</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> قائمة الجداول</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>اسم الجدول</th>
                                    <th>عدد الصفوف</th>
                                    <th>الحجم (MB)</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tables ?? [] as $index => $table)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><code>{{ $table['table_name'] }}</code></td>
                                    <td>{{ number_format($table['table_rows']) }}</td>
                                    <td>{{ number_format($table['size_mb'], 2) }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="viewTable('{{ $table['table_name'] }}')">
                                            <i class="fas fa-eye"></i> عرض
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">لا توجد جداول</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Eloquent Models -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-code"></i> Eloquent Models المحسّنة</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-check-circle text-success"></i> الميزات المفعّلة:</h6>
                            <ul>
                                <li>✅ Casts as Methods (Laravel 11+)</li>
                                <li>✅ Enhanced Type Safety</li>
                                <li>✅ Soft Deletes</li>
                                <li>✅ Global Scopes</li>
                                <li>✅ Query Scopes</li>
                                <li>✅ Model Observers</li>
                                <li>✅ Events & Listeners</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-database text-info"></i> النماذج المتوفرة:</h6>
                            <ul>
                                <li><code>App\Models\Unit</code></li>
                                <li><code>App\Models\Company</code></li>
                                <li><code>App\Models\User</code></li>
                                <li><code>App\Models\Account</code></li>
                                <li><code>App\Models\ClearingTransaction</code></li>
                                <li>... وغيرها</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Migrations -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-code-branch"></i> Migrations</h5>
                </div>
                <div class="card-body">
                    <p>إدارة هجرات قاعدة البيانات (Migrations)</p>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" onclick="runMigration('migrate')">
                            <i class="fas fa-play"></i> تشغيل Migrations
                        </button>
                        <button class="btn btn-warning" onclick="runMigration('rollback')">
                            <i class="fas fa-undo"></i> Rollback
                        </button>
                        <button class="btn btn-danger" onclick="runMigration('fresh')">
                            <i class="fas fa-sync"></i> Fresh (حذف وإعادة)
                        </button>
                        <button class="btn btn-success" onclick="runMigration('seed')">
                            <i class="fas fa-seedling"></i> Seed
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function viewTable(tableName) {
    alert('عرض تفاصيل الجدول: ' + tableName);
    // يمكن إضافة modal لعرض تفاصيل الجدول
}

function runMigration(action) {
    const messages = {
        'migrate': 'هل تريد تشغيل جميع الهجرات؟',
        'rollback': 'هل تريد التراجع عن آخر دفعة من الهجرات؟',
        'fresh': 'تحذير: هذا سيحذف جميع البيانات! هل أنت متأكد؟',
        'seed': 'هل تريد ملء قاعدة البيانات بالبيانات الأولية؟'
    };
    
    if (!confirm(messages[action])) {
        return;
    }
    
    alert('⏳ جاري تنفيذ: php artisan ' + action);
    // يجب إنشاء API endpoint لتنفيذ الأوامر
}
</script>
@endpush
