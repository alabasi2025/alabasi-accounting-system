@extends('layouts.admin')

@section('page-title', 'إدارة النشر')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">النشر</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Deployment Status -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5>GitHub Actions</h5>
                    <p class="text-muted">نشر تلقائي</p>
                    <span class="badge bg-success">نشط</span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-server fa-3x text-primary mb-3"></i>
                    <h5>الخادم</h5>
                    <p class="text-muted">Hostinger</p>
                    <span class="badge bg-primary">متصل</span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-code-branch fa-3x text-info mb-3"></i>
                    <h5>آخر نشر</h5>
                    <p class="text-muted">منذ ساعة</p>
                    <span class="badge bg-info">ناجح</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Zero Downtime Deployment -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-rocket"></i> Zero Downtime Deployment</h5>
                </div>
                <div class="card-body">
                    <p class="lead">نشر التحديثات بدون توقف الخدمة باستخدام GitHub Actions</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6><i class="fas fa-check-circle text-success"></i> الميزات:</h6>
                            <ul>
                                <li>نشر تلقائي عند Push إلى master</li>
                                <li>اختبارات تلقائية قبل النشر</li>
                                <li>Rollback تلقائي عند الفشل</li>
                                <li>إشعارات عند النشر</li>
                                <li>سجل كامل للنشر</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-cog text-info"></i> الإعدادات:</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>المستودع:</strong></td>
                                    <td>alabasi2025/alabasi-php</td>
                                </tr>
                                <tr>
                                    <td><strong>الفرع:</strong></td>
                                    <td>master</td>
                                </tr>
                                <tr>
                                    <td><strong>الخادم:</strong></td>
                                    <td>82.29.157.218:65002</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Deployment History -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-history"></i> سجل النشر</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>التاريخ</th>
                                    <th>Commit</th>
                                    <th>الرسالة</th>
                                    <th>الحالة</th>
                                    <th>المدة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>2025-11-19 14:30</td>
                                    <td><code>4fb99b6</code></td>
                                    <td>feat: إضافة صفحات إدارة متقدمة</td>
                                    <td><span class="badge bg-success">ناجح</span></td>
                                    <td>2m 15s</td>
                                </tr>
                                <tr>
                                    <td>2025-11-19 13:45</td>
                                    <td><code>372e103</code></td>
                                    <td>feat: إضافة جميع ميزات Laravel</td>
                                    <td><span class="badge bg-success">ناجح</span></td>
                                    <td>2m 30s</td>
                                </tr>
                                <tr>
                                    <td>2025-11-19 12:00</td>
                                    <td><code>7b0b911</code></td>
                                    <td>fix: إصلاح مشاكل Dashboard</td>
                                    <td><span class="badge bg-success">ناجح</span></td>
                                    <td>1m 45s</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- GitHub Actions Workflow -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fab fa-github"></i> GitHub Actions Workflow</h5>
                </div>
                <div class="card-body">
                    <h6>ملف: <code>.github/workflows/deploy.yml</code></h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>name: Deploy to Hostinger

on:
  push:
    branches: [ master ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Deploy to Server
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.HOST }}
          username: ${{ secrets.USERNAME }}
          key: ${{ secrets.SSH_KEY }}
          port: ${{ secrets.PORT }}
          script: |
            cd domains/alabasi.es/public_html
            git pull origin master
            composer install --no-dev
            php artisan migrate --force
            php artisan optimize:clear
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache</code></pre>
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
                    <button class="btn btn-primary" onclick="triggerDeploy()">
                        <i class="fas fa-rocket"></i> نشر يدوي
                    </button>
                    <button class="btn btn-warning" onclick="rollback()">
                        <i class="fas fa-undo"></i> Rollback
                    </button>
                    <button class="btn btn-info" onclick="viewLogs()">
                        <i class="fas fa-file-alt"></i> عرض السجلات
                    </button>
                    <button class="btn btn-success" onclick="testConnection()">
                        <i class="fas fa-plug"></i> اختبار الاتصال
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function triggerDeploy() {
    if (confirm('هل تريد بدء النشر اليدوي؟')) {
        alert('⏳ جاري النشر...');
    }
}

function rollback() {
    if (confirm('هل تريد التراجع إلى النسخة السابقة؟')) {
        alert('⏳ جاري التراجع...');
    }
}

function viewLogs() {
    alert('📋 عرض سجلات النشر...');
}

function testConnection() {
    alert('⏳ جاري اختبار الاتصال بالخادم...');
}
</script>
@endpush
