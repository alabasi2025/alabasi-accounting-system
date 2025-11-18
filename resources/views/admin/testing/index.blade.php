@extends('layouts.admin')

@section('page-title', 'إدارة الاختبارات')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">الاختبارات</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Testing Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-vial fa-3x text-primary mb-3"></i>
                    <h3>0</h3>
                    <p class="text-muted">إجمالي الاختبارات</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h3>0</h3>
                    <p class="text-muted">ناجحة</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                    <h3>0</h3>
                    <p class="text-muted">فاشلة</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-percentage fa-3x text-info mb-3"></i>
                    <h3>0%</h3>
                    <p class="text-muted">نسبة النجاح</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Testing Environment -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-flask"></i> بيئة الاختبار (PHPUnit)</h5>
                </div>
                <div class="card-body">
                    <p class="lead">بيئة اختبار كاملة لضمان جودة الكود وعمل النظام بشكل صحيح</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6><i class="fas fa-check-circle text-success"></i> أنواع الاختبارات:</h6>
                            <ul>
                                <li>Unit Tests - اختبارات الوحدات</li>
                                <li>Feature Tests - اختبارات الميزات</li>
                                <li>Integration Tests - اختبارات التكامل</li>
                                <li>Browser Tests (Dusk) - اختبارات المتصفح</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-cog text-info"></i> الإعدادات:</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Framework:</strong></td>
                                    <td>PHPUnit 10.x</td>
                                </tr>
                                <tr>
                                    <td><strong>Database:</strong></td>
                                    <td>:memory: (SQLite)</td>
                                </tr>
                                <tr>
                                    <td><strong>Coverage:</strong></td>
                                    <td>Enabled</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Suites -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list"></i> مجموعات الاختبار</h5>
                    <button class="btn btn-light btn-sm" onclick="createTest()">
                        <i class="fas fa-plus"></i> إنشاء اختبار
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>اسم الاختبار</th>
                                    <th>النوع</th>
                                    <th>المسار</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>UnitTest</code></td>
                                    <td><span class="badge bg-primary">Unit</span></td>
                                    <td><code>tests/Unit/UnitTest.php</code></td>
                                    <td>
                                        <button class="btn btn-sm btn-success" onclick="runTest('Unit')">
                                            <i class="fas fa-play"></i> تشغيل
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><code>CompanyTest</code></td>
                                    <td><span class="badge bg-primary">Unit</span></td>
                                    <td><code>tests/Unit/CompanyTest.php</code></td>
                                    <td>
                                        <button class="btn btn-sm btn-success" onclick="runTest('Company')">
                                            <i class="fas fa-play"></i> تشغيل
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><code>AccountTest</code></td>
                                    <td><span class="badge bg-info">Feature</span></td>
                                    <td><code>tests/Feature/AccountTest.php</code></td>
                                    <td>
                                        <button class="btn btn-sm btn-success" onclick="runTest('Account')">
                                            <i class="fas fa-play"></i> تشغيل
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

    <!-- Commands -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-terminal"></i> أوامر الاختبار</h5>
                </div>
                <div class="card-body">
                    <h6>تشغيل جميع الاختبارات:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>php artisan test</code></pre>

                    <h6 class="mt-3">تشغيل اختبار محدد:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>php artisan test --filter AccountTest</code></pre>

                    <h6 class="mt-3">مع Code Coverage:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>php artisan test --coverage</code></pre>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-code"></i> إنشاء اختبار</h5>
                </div>
                <div class="card-body">
                    <h6>إنشاء Unit Test:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>php artisan make:test AccountTest --unit</code></pre>

                    <h6 class="mt-3">إنشاء Feature Test:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>php artisan make:test AccountTest</code></pre>

                    <h6 class="mt-3">مثال على اختبار:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>public function test_account_creation()
{
    $account = Account::factory()->create();
    $this->assertDatabaseHas('accounts', [
        'id' => $account->id
    ]);
}</code></pre>
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
                    <button class="btn btn-primary" onclick="runAllTests()">
                        <i class="fas fa-play"></i> تشغيل جميع الاختبارات
                    </button>
                    <button class="btn btn-success" onclick="runUnitTests()">
                        <i class="fas fa-cube"></i> Unit Tests فقط
                    </button>
                    <button class="btn btn-info" onclick="runFeatureTests()">
                        <i class="fas fa-star"></i> Feature Tests فقط
                    </button>
                    <button class="btn btn-warning" onclick="generateCoverage()">
                        <i class="fas fa-chart-pie"></i> Code Coverage
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function createTest() {
    alert('📝 إنشاء اختبار جديد...');
}

function runTest(name) {
    alert(`⏳ جاري تشغيل اختبار: ${name}Test`);
}

function runAllTests() {
    alert('⏳ جاري تشغيل جميع الاختبارات...');
}

function runUnitTests() {
    alert('⏳ جاري تشغيل Unit Tests...');
}

function runFeatureTests() {
    alert('⏳ جاري تشغيل Feature Tests...');
}

function generateCoverage() {
    alert('⏳ جاري إنشاء تقرير Code Coverage...');
}
</script>
@endpush
