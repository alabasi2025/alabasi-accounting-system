@extends('layouts.admin')

@section('page-title', 'إدارة Middleware')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">Middleware</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Middleware System Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-filter"></i> نظام Middleware</h5>
                </div>
                <div class="card-body">
                    <p class="lead">طبقة وسيطة للتحكم في الطلبات قبل وصولها للـ Controllers</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6><i class="fas fa-check-circle text-success"></i> الفوائد:</h6>
                            <ul>
                                <li>التحقق من المصادقة (Authentication)</li>
                                <li>التحكم في الصلاحيات (Authorization)</li>
                                <li>حماية CSRF</li>
                                <li>Rate Limiting</li>
                                <li>Logging</li>
                                <li>تعديل الطلبات والاستجابات</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-layer-group text-info"></i> الأنواع:</h6>
                            <ul>
                                <li><strong>Global:</strong> يعمل على جميع الطلبات</li>
                                <li><strong>Route:</strong> يعمل على مسارات محددة</li>
                                <li><strong>Group:</strong> يعمل على مجموعة مسارات</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Middleware -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Middleware المفعّلة</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>النوع</th>
                                    <th>الوصف</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>auth</code></td>
                                    <td><span class="badge bg-primary">Route</span></td>
                                    <td>التحقق من تسجيل الدخول</td>
                                    <td><span class="badge bg-success">نشط</span></td>
                                </tr>
                                <tr>
                                    <td><code>verified</code></td>
                                    <td><span class="badge bg-primary">Route</span></td>
                                    <td>التحقق من البريد الإلكتروني</td>
                                    <td><span class="badge bg-success">نشط</span></td>
                                </tr>
                                <tr>
                                    <td><code>throttle</code></td>
                                    <td><span class="badge bg-warning">Global</span></td>
                                    <td>Rate Limiting</td>
                                    <td><span class="badge bg-success">نشط</span></td>
                                </tr>
                                <tr>
                                    <td><code>csrf</code></td>
                                    <td><span class="badge bg-warning">Global</span></td>
                                    <td>حماية CSRF</td>
                                    <td><span class="badge bg-success">نشط</span></td>
                                </tr>
                                <tr>
                                    <td><code>localization</code></td>
                                    <td><span class="badge bg-info">Group</span></td>
                                    <td>تحديد اللغة</td>
                                    <td><span class="badge bg-success">نشط</span></td>
                                </tr>
                                <tr>
                                    <td><code>log.requests</code></td>
                                    <td><span class="badge bg-warning">Global</span></td>
                                    <td>تسجيل الطلبات</td>
                                    <td><span class="badge bg-success">نشط</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Code Examples -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-code"></i> إنشاء Middleware</h5>
                </div>
                <div class="card-body">
                    <h6>إنشاء Middleware جديد:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>php artisan make:middleware CheckRole</code></pre>

                    <h6 class="mt-3">ملف Middleware:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>class CheckRole
{
    public function handle($request, Closure $next, $role)
    {
        if (!auth()->user()->hasRole($role)) {
            abort(403);
        }
        
        return $next($request);
    }
}</code></pre>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-cog"></i> تسجيل Middleware</h5>
                </div>
                <div class="card-body">
                    <h6>في <code>app/Http/Kernel.php</code>:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>protected $routeMiddleware = [
    'role' => \App\Http\Middleware\CheckRole::class,
];</code></pre>

                    <h6 class="mt-3">الاستخدام في Routes:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>// مسار واحد
Route::get('/admin', function () {
    //
})->middleware('role:admin');

// مجموعة مسارات
Route::middleware(['auth', 'role:admin'])->group(function () {
    //
});</code></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Middleware Groups -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-layer-group"></i> مجموعات Middleware</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><strong>web</strong> Group:</h6>
                            <ul>
                                <li>EncryptCookies</li>
                                <li>AddQueuedCookiesToResponse</li>
                                <li>StartSession</li>
                                <li>ShareErrorsFromSession</li>
                                <li>VerifyCsrfToken</li>
                                <li>SubstituteBindings</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><strong>api</strong> Group:</h6>
                            <ul>
                                <li>ThrottleRequests:60,1</li>
                                <li>SubstituteBindings</li>
                            </ul>
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
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <button class="btn btn-primary" onclick="createMiddleware()">
                        <i class="fas fa-plus"></i> إنشاء Middleware
                    </button>
                    <button class="btn btn-info" onclick="listMiddleware()">
                        <i class="fas fa-list"></i> عرض جميع Middleware
                    </button>
                    <button class="btn btn-warning" onclick="testMiddleware()">
                        <i class="fas fa-vial"></i> اختبار Middleware
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function createMiddleware() {
    alert('📝 إنشاء Middleware جديد...');
}

function listMiddleware() {
    alert('📋 عرض جميع Middleware المسجلة...');
}

function testMiddleware() {
    alert('⏳ جاري اختبار Middleware...');
}
</script>
@endpush
