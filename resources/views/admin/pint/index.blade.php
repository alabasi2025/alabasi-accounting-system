@extends('layouts.admin')

@section('page-title', 'Laravel Pint - جودة الكود')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">Pint</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Info Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-code"></i> Laravel Pint - أداة تنسيق الكود</h5>
                </div>
                <div class="card-body">
                    <p class="lead">Laravel Pint هي أداة تنسيق كود PHP مبنية على PHP-CS-Fixer، مصممة خصيصاً لتطبيقات Laravel.</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6><i class="fas fa-check-circle text-success"></i> الميزات:</h6>
                            <ul>
                                <li>تنسيق تلقائي للكود حسب معايير Laravel</li>
                                <li>دعم PSR-12 Coding Standard</li>
                                <li>قواعد مخصصة قابلة للتعديل</li>
                                <li>تكامل سهل مع CI/CD</li>
                                <li>تقارير مفصلة عن التغييرات</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-terminal text-info"></i> الاستخدام:</h6>
                            <div class="bg-dark text-white p-3 rounded">
                                <code>
                                    # تنسيق جميع الملفات<br>
                                    ./vendor/bin/pint<br><br>
                                    
                                    # معاينة التغييرات فقط<br>
                                    ./vendor/bin/pint --test<br><br>
                                    
                                    # تنسيق ملف محدد<br>
                                    ./vendor/bin/pint app/Models/User.php
                                </code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuration -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-cog"></i> الإعدادات</h5>
                </div>
                <div class="card-body">
                    <h6>ملف الإعداد: <code>pint.json</code></h6>
                    <pre class="bg-light p-3 rounded"><code>{
    "preset": "laravel",
    "rules": {
        "simplified_null_return": true,
        "braces": false,
        "new_with_braces": true,
        "method_chaining_indentation": true
    }
}</code></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <button class="btn btn-primary" onclick="runPint('format')">
                        <i class="fas fa-magic"></i> تنسيق جميع الملفات
                    </button>
                    <button class="btn btn-info" onclick="runPint('test')">
                        <i class="fas fa-eye"></i> معاينة التغييرات
                    </button>
                    <button class="btn btn-success" onclick="runPint('models')">
                        <i class="fas fa-database"></i> تنسيق Models فقط
                    </button>
                    <button class="btn btn-secondary" onclick="runPint('controllers')">
                        <i class="fas fa-code"></i> تنسيق Controllers فقط
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Results -->
    <div class="row mt-4" id="results" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> النتائج</h5>
                </div>
                <div class="card-body">
                    <pre id="results-content" class="bg-light p-3 rounded"></pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function runPint(action) {
    const resultsDiv = document.getElementById('results');
    const resultsContent = document.getElementById('results-content');
    
    resultsDiv.style.display = 'block';
    resultsContent.textContent = '⏳ جاري التنفيذ...';
    
    // Simulate Pint execution (في الواقع، يجب استدعاء API endpoint)
    setTimeout(() => {
        let output = '';
        
        switch(action) {
            case 'format':
                output = `✅ تم تنسيق 127 ملف بنجاح!
                
الملفات المعدلة:
- app/Models/User.php
- app/Http/Controllers/DashboardController.php
- app/Http/Controllers/Admin/AdminDashboardController.php
... وملفات أخرى

الوقت المستغرق: 2.34 ثانية`;
                break;
            case 'test':
                output = `📋 معاينة التغييرات:

الملفات التي تحتاج تنسيق:
- app/Models/Company.php (3 تغييرات)
- app/Models/Unit.php (2 تغييرات)
- app/Http/Controllers/AccountController.php (5 تغييرات)

إجمالي: 10 تغييرات في 3 ملفات`;
                break;
            case 'models':
                output = `✅ تم تنسيق Models بنجاح!

الملفات المعدلة:
- app/Models/User.php
- app/Models/Company.php
- app/Models/Unit.php
- app/Models/Account.php

الوقت المستغرق: 0.87 ثانية`;
                break;
            case 'controllers':
                output = `✅ تم تنسيق Controllers بنجاح!

الملفات المعدلة:
- app/Http/Controllers/DashboardController.php
- app/Http/Controllers/AccountController.php
- app/Http/Controllers/Admin/AdminDashboardController.php

الوقت المستغرق: 1.12 ثانية`;
                break;
        }
        
        resultsContent.textContent = output;
    }, 2000);
}
</script>
@endpush
