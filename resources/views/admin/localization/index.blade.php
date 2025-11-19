@extends('layouts.admin')

@section('page-title', 'إدارة اللغات والترجمة')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">اللغات</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Languages Overview -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-center border-success">
                <div class="card-body">
                    <i class="fas fa-language fa-3x text-success mb-3"></i>
                    <h5>العربية</h5>
                    <p class="text-muted">اللغة الافتراضية</p>
                    <span class="badge bg-success">نشط</span>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card text-center border-secondary">
                <div class="card-body">
                    <i class="fas fa-language fa-3x text-secondary mb-3"></i>
                    <h5>English</h5>
                    <p class="text-muted">اللغة الثانوية</p>
                    <span class="badge bg-secondary">معطل</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Localization System -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-globe"></i> نظام اللغات المتعدد</h5>
                </div>
                <div class="card-body">
                    <p class="lead">دعم كامل للغة العربية مع RTL وإمكانية إضافة لغات أخرى</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6><i class="fas fa-check-circle text-success"></i> الميزات:</h6>
                            <ul>
                                <li>دعم RTL كامل للعربية</li>
                                <li>ملفات ترجمة منظمة</li>
                                <li>تبديل اللغة ديناميكي</li>
                                <li>ترجمة Validation Messages</li>
                                <li>ترجمة Pagination</li>
                                <li>دعم التواريخ بالعربية</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-file-alt text-info"></i> ملفات اللغة:</h6>
                            <ul>
                                <li><code>lang/ar/messages.php</code></li>
                                <li><code>lang/ar/validation.php</code></li>
                                <li><code>lang/ar/pagination.php</code></li>
                                <li><code>lang/ar/passwords.php</code></li>
                                <li><code>lang/en/messages.php</code></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Translation Keys -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-key"></i> مفاتيح الترجمة</h5>
                    <button class="btn btn-light btn-sm" onclick="addTranslation()">
                        <i class="fas fa-plus"></i> إضافة ترجمة
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>المفتاح</th>
                                    <th>العربية</th>
                                    <th>English</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>welcome</code></td>
                                    <td>مرحباً بك في نظام الأباسي المحاسبي</td>
                                    <td>Welcome to Alabasi Accounting System</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="editTranslation('welcome')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><code>dashboard</code></td>
                                    <td>لوحة التحكم</td>
                                    <td>Dashboard</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="editTranslation('dashboard')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><code>units</code></td>
                                    <td>الوحدات</td>
                                    <td>Units</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="editTranslation('units')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><code>companies</code></td>
                                    <td>المؤسسات</td>
                                    <td>Companies</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="editTranslation('companies')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><code>accounts</code></td>
                                    <td>الحسابات</td>
                                    <td>Accounts</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="editTranslation('accounts')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Usage Examples -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-code"></i> الاستخدام في الكود</h5>
                </div>
                <div class="card-body">
                    <h6>في PHP:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>// استخدام الترجمة
__('messages.welcome');
trans('messages.dashboard');

// مع متغيرات
__('messages.hello', ['name' => 'محمد']);

// تغيير اللغة
App::setLocale('ar');</code></pre>

                    <h6 class="mt-3">في Blade:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>{{ __('messages.welcome') }}
@lang('messages.dashboard')</code></pre>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-cog"></i> الإعدادات</h5>
                </div>
                <div class="card-body">
                    <h6>في ملف <code>config/app.php</code>:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>'locale' => 'ar',
'fallback_locale' => 'en',
'faker_locale' => 'ar_SA',</code></pre>

                    <h6 class="mt-3">Middleware للغة:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>public function handle($request, Closure $next)
{
    if ($request->has('lang')) {
        App::setLocale($request->lang);
    }
    return $next($request);
}</code></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- RTL Support -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-align-right"></i> دعم RTL</h5>
                </div>
                <div class="card-body">
                    <p>تم تفعيل دعم RTL كامل للعربية في جميع صفحات النظام</p>
                    <div class="alert alert-success">
                        <h6><i class="fas fa-check-circle"></i> الميزات المفعّلة:</h6>
                        <ul class="mb-0">
                            <li>اتجاه النص من اليمين لليسار</li>
                            <li>محاذاة العناصر بشكل صحيح</li>
                            <li>Bootstrap RTL</li>
                            <li>الأيقونات والأزرار محاذاة صحيحة</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function addTranslation() {
    alert('📝 إضافة ترجمة جديدة...');
    // يجب إنشاء modal لإضافة ترجمة جديدة
}

function editTranslation(key) {
    alert(`✏️ تعديل الترجمة: ${key}`);
    // يجب إنشاء modal لتعديل الترجمة
}
</script>
@endpush
