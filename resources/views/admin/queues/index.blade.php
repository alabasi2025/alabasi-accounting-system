@extends('layouts.admin')

@section('page-title', 'إدارة قوائم الانتظار')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">Queues</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Queue Info -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-tasks fa-3x text-primary mb-3"></i>
                    <h3>0</h3>
                    <p class="text-muted">مهام قيد الانتظار</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-spinner fa-3x text-info mb-3"></i>
                    <h3>0</h3>
                    <p class="text-muted">مهام قيد التنفيذ</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h3>0</h3>
                    <p class="text-muted">مهام مكتملة</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                    <h3>0</h3>
                    <p class="text-muted">مهام فاشلة</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Queue System Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> نظام قوائم الانتظار (Queues & Jobs)</h5>
                </div>
                <div class="card-body">
                    <p class="lead">نظام متقدم لمعالجة المهام الثقيلة في الخلفية دون إبطاء التطبيق</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6><i class="fas fa-check-circle text-success"></i> الميزات:</h6>
                            <ul>
                                <li>معالجة المهام في الخلفية</li>
                                <li>Job Batching (تجميع المهام)</li>
                                <li>Job Chaining (ربط المهام)</li>
                                <li>Failed Job Handling</li>
                                <li>Job Retry Mechanism</li>
                                <li>Job Timeout Control</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-cog text-info"></i> الإعدادات:</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>المحرك:</strong></td>
                                    <td><span class="badge bg-primary">{{ config('queue.default') }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>الاتصال:</strong></td>
                                    <td>{{ config('queue.connections.' . config('queue.default') . '.driver') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Queue Name:</strong></td>
                                    <td>default</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Jobs -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> المهام المتاحة (Jobs)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>اسم المهمة</th>
                                    <th>الوصف</th>
                                    <th>المسار</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>ProcessAccountingReport</code></td>
                                    <td>معالجة التقارير المحاسبية</td>
                                    <td><code>app/Jobs/ProcessAccountingReport.php</code></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="dispatchJob('ProcessAccountingReport')">
                                            <i class="fas fa-play"></i> تشغيل
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><code>SendEmailNotification</code></td>
                                    <td>إرسال إشعارات البريد الإلكتروني</td>
                                    <td><code>app/Jobs/SendEmailNotification.php</code></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="dispatchJob('SendEmailNotification')">
                                            <i class="fas fa-play"></i> تشغيل
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><code>GenerateBackup</code></td>
                                    <td>إنشاء نسخة احتياطية</td>
                                    <td><code>app/Jobs/GenerateBackup.php</code></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="dispatchJob('GenerateBackup')">
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

    <!-- Queue Commands -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-terminal"></i> أوامر Queue</h5>
                </div>
                <div class="card-body">
                    <div class="bg-dark text-white p-3 rounded">
                        <code>
                            # تشغيل Queue Worker<br>
                            php artisan queue:work<br><br>
                            
                            # تشغيل مع إعادة محاولة<br>
                            php artisan queue:work --tries=3<br><br>
                            
                            # معالجة مهمة واحدة فقط<br>
                            php artisan queue:work --once<br><br>
                            
                            # عرض المهام الفاشلة<br>
                            php artisan queue:failed<br><br>
                            
                            # إعادة محاولة المهام الفاشلة<br>
                            php artisan queue:retry all
                        </code>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-code"></i> مثال على الاستخدام</h5>
                </div>
                <div class="card-body">
                    <h6>إنشاء Job:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>php artisan make:job ProcessReport</code></pre>

                    <h6 class="mt-3">Dispatch Job:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>use App\Jobs\ProcessReport;

ProcessReport::dispatch($data);</code></pre>

                    <h6 class="mt-3">Delayed Dispatch:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>ProcessReport::dispatch($data)
    ->delay(now()->addMinutes(10));</code></pre>
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
                    <button class="btn btn-primary" onclick="startWorker()">
                        <i class="fas fa-play"></i> تشغيل Queue Worker
                    </button>
                    <button class="btn btn-warning" onclick="retryFailed()">
                        <i class="fas fa-redo"></i> إعادة محاولة الفاشلة
                    </button>
                    <button class="btn btn-danger" onclick="clearFailed()">
                        <i class="fas fa-trash"></i> حذف المهام الفاشلة
                    </button>
                    <button class="btn btn-info" onclick="viewFailed()">
                        <i class="fas fa-eye"></i> عرض المهام الفاشلة
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function dispatchJob(jobName) {
    alert('⏳ جاري تشغيل المهمة: ' + jobName);
    // يجب إنشاء API endpoint لتشغيل المهام
}

function startWorker() {
    alert('⏳ جاري تشغيل Queue Worker...');
}

function retryFailed() {
    if (confirm('هل تريد إعادة محاولة جميع المهام الفاشلة؟')) {
        alert('⏳ جاري إعادة المحاولة...');
    }
}

function clearFailed() {
    if (confirm('هل تريد حذف جميع المهام الفاشلة؟')) {
        alert('⏳ جاري الحذف...');
    }
}

function viewFailed() {
    alert('📋 عرض المهام الفاشلة...');
}
</script>
@endpush
