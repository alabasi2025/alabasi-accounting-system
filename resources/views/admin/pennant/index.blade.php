@extends('layouts.admin')

@section('page-title', 'Laravel Pennant - Feature Flags')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">Pennant</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Pennant Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-flag"></i> Laravel Pennant - Feature Flags</h5>
                </div>
                <div class="card-body">
                    <p class="lead">نظام إدارة الميزات (Feature Flags) للتحكم في تفعيل وتعطيل الميزات بشكل ديناميكي</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6><i class="fas fa-check-circle text-success"></i> الفوائد:</h6>
                            <ul>
                                <li>تفعيل/تعطيل الميزات دون نشر كود جديد</li>
                                <li>A/B Testing للميزات الجديدة</li>
                                <li>إطلاق تدريجي للميزات (Gradual Rollout)</li>
                                <li>تحكم في الميزات حسب المستخدم أو الدور</li>
                                <li>تجربة الميزات قبل الإطلاق الكامل</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-cog text-info"></i> حالات الاستخدام:</h6>
                            <ul>
                                <li>إطلاق ميزات جديدة تدريجياً</li>
                                <li>تعطيل ميزات مؤقتاً للصيانة</li>
                                <li>اختبار ميزات مع مجموعة محددة</li>
                                <li>ميزات خاصة بالوحدات أو المؤسسات</li>
                                <li>ميزات تجريبية (Beta Features)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Feature Flags List -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list"></i> الميزات المتاحة</h5>
                    <button class="btn btn-light btn-sm" onclick="createFeature()">
                        <i class="fas fa-plus"></i> إضافة ميزة
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>اسم الميزة</th>
                                    <th>الوصف</th>
                                    <th>النطاق</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>advanced-reports</code></td>
                                    <td>التقارير المتقدمة مع الرسوم البيانية</td>
                                    <td><span class="badge bg-primary">الكل</span></td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" checked onchange="toggleFeature('advanced-reports', this.checked)">
                                            <label class="form-check-label text-success">مفعّل</label>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="editFeature('advanced-reports')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteFeature('advanced-reports')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><code>clearing-transactions</code></td>
                                    <td>التحويلات بين الوحدات والمؤسسات</td>
                                    <td><span class="badge bg-warning">الوحدة المركزية</span></td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" checked onchange="toggleFeature('clearing-transactions', this.checked)">
                                            <label class="form-check-label text-success">مفعّل</label>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="editFeature('clearing-transactions')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteFeature('clearing-transactions')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><code>api-access</code></td>
                                    <td>الوصول إلى API الخارجي</td>
                                    <td><span class="badge bg-info">مستخدمين محددين</span></td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" onchange="toggleFeature('api-access', this.checked)">
                                            <label class="form-check-label text-danger">معطّل</label>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="editFeature('api-access')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteFeature('api-access')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><code>beta-dashboard</code></td>
                                    <td>لوحة التحكم الجديدة (تجريبي)</td>
                                    <td><span class="badge bg-danger">Beta</span></td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" onchange="toggleFeature('beta-dashboard', this.checked)">
                                            <label class="form-check-label text-danger">معطّل</label>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="editFeature('beta-dashboard')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteFeature('beta-dashboard')">
                                            <i class="fas fa-trash"></i>
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
                    <h6>التحقق من ميزة:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>use Laravel\Pennant\Feature;

if (Feature::active('advanced-reports')) {
    // عرض التقارير المتقدمة
}</code></pre>

                    <h6 class="mt-3">في Blade:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>@feature('advanced-reports')
    &lt;!-- محتوى التقارير المتقدمة --&gt;
@endfeature</code></pre>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-terminal"></i> الأوامر</h5>
                </div>
                <div class="card-body">
                    <h6>إنشاء Feature:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>php artisan pennant:feature AdvancedReports</code></pre>

                    <h6 class="mt-3">تفعيل لمستخدم:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>Feature::activate('advanced-reports', $user);</code></pre>

                    <h6 class="mt-3">تفعيل للجميع:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>Feature::activateForEveryone('advanced-reports');</code></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Scope Management -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-users"></i> إدارة النطاق (Scope)</h5>
                </div>
                <div class="card-body">
                    <p>يمكنك تحديد نطاق الميزة بناءً على:</p>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-user fa-2x text-primary mb-2"></i>
                                    <h6>مستخدم محدد</h6>
                                    <small class="text-muted">User-based</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-building fa-2x text-success mb-2"></i>
                                    <h6>وحدة محددة</h6>
                                    <small class="text-muted">Unit-based</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-briefcase fa-2x text-warning mb-2"></i>
                                    <h6>مؤسسة محددة</h6>
                                    <small class="text-muted">Company-based</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-globe fa-2x text-info mb-2"></i>
                                    <h6>الجميع</h6>
                                    <small class="text-muted">Global</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleFeature(featureName, isActive) {
    const status = isActive ? 'تفعيل' : 'تعطيل';
    alert(`⏳ جاري ${status} الميزة: ${featureName}`);
    // يجب إنشاء API endpoint لتفعيل/تعطيل الميزات
}

function createFeature() {
    alert('📝 إنشاء ميزة جديدة...');
    // يجب إنشاء modal لإنشاء ميزة جديدة
}

function editFeature(featureName) {
    alert(`✏️ تعديل الميزة: ${featureName}`);
    // يجب إنشاء modal لتعديل الميزة
}

function deleteFeature(featureName) {
    if (confirm(`هل تريد حذف الميزة: ${featureName}؟`)) {
        alert(`🗑️ جاري حذف الميزة...`);
        // يجب إنشاء API endpoint لحذف الميزات
    }
}
</script>
@endpush
