@extends('layouts.admin')

@section('page-title', 'جدولة المهام')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">Scheduler</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Scheduler Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-clock"></i> Laravel Task Scheduling</h5>
                </div>
                <div class="card-body">
                    <p class="lead">نظام جدولة المهام التلقائية (Cron Jobs) المدمج في Laravel</p>
                    
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> تفعيل Scheduler:</h6>
                        <p>أضف هذا السطر إلى Crontab في السيرفر:</p>
                        <pre class="bg-dark text-white p-3 rounded mb-0"><code>* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scheduled Tasks -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> المهام المجدولة</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>المهمة</th>
                                    <th>التوقيت</th>
                                    <th>الوصف</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>telescope:prune</code></td>
                                    <td><span class="badge bg-info">يومياً</span></td>
                                    <td>تنظيف بيانات Telescope القديمة</td>
                                    <td><span class="badge bg-success">نشط</span></td>
                                </tr>
                                <tr>
                                    <td><code>cache:clear</code></td>
                                    <td><span class="badge bg-warning">أسبوعياً</span></td>
                                    <td>مسح الذاكرة المؤقتة</td>
                                    <td><span class="badge bg-secondary">معطل</span></td>
                                </tr>
                                <tr>
                                    <td><code>backup:run</code></td>
                                    <td><span class="badge bg-danger">يومياً 2:00 AM</span></td>
                                    <td>نسخ احتياطي للنظام</td>
                                    <td><span class="badge bg-secondary">معطل</span></td>
                                </tr>
                                <tr>
                                    <td><code>Update Statistics</code></td>
                                    <td><span class="badge bg-primary">كل ساعة</span></td>
                                    <td>تحديث إحصائيات النظام</td>
                                    <td><span class="badge bg-success">نشط</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Frequencies -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar"></i> التوقيتات المتاحة</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><code>->everyMinute()</code></td>
                            <td>كل دقيقة</td>
                        </tr>
                        <tr>
                            <td><code>->everyFiveMinutes()</code></td>
                            <td>كل 5 دقائق</td>
                        </tr>
                        <tr>
                            <td><code>->hourly()</code></td>
                            <td>كل ساعة</td>
                        </tr>
                        <tr>
                            <td><code>->daily()</code></td>
                            <td>يومياً</td>
                        </tr>
                        <tr>
                            <td><code>->weekly()</code></td>
                            <td>أسبوعياً</td>
                        </tr>
                        <tr>
                            <td><code>->monthly()</code></td>
                            <td>شهرياً</td>
                        </tr>
                        <tr>
                            <td><code>->yearly()</code></td>
                            <td>سنوياً</td>
                        </tr>
                        <tr>
                            <td><code>->dailyAt('13:00')</code></td>
                            <td>يومياً في وقت محدد</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-code"></i> أمثلة على الاستخدام</h5>
                </div>
                <div class="card-body">
                    <h6>في ملف <code>app/Console/Kernel.php</code>:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>protected function schedule(Schedule $schedule)
{
    // نسخ احتياطي يومي
    $schedule->command('backup:run')
             ->daily()
             ->at('02:00');
    
    // تنظيف أسبوعي
    $schedule->command('cache:clear')
             ->weekly()
             ->sundays()
             ->at('01:00');
    
    // تحديث كل ساعة
    $schedule->call(function () {
        Cache::forget('stats');
    })->hourly();
    
    // مهمة مخصصة
    $schedule->job(new ProcessReports)
             ->daily()
             ->when(function () {
                 return true; // شرط
             });
}</code></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Commands -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-terminal"></i> أوامر مفيدة</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>تشغيل Scheduler يدوياً:</h6>
                            <pre class="bg-dark text-white p-3 rounded"><code>php artisan schedule:run</code></pre>
                            
                            <h6 class="mt-3">عرض المهام المجدولة:</h6>
                            <pre class="bg-dark text-white p-3 rounded"><code>php artisan schedule:list</code></pre>
                        </div>
                        <div class="col-md-6">
                            <h6>اختبار مهمة معينة:</h6>
                            <pre class="bg-dark text-white p-3 rounded"><code>php artisan schedule:test</code></pre>
                            
                            <h6 class="mt-3">عرض المهام التالية:</h6>
                            <pre class="bg-dark text-white p-3 rounded"><code>php artisan schedule:work</code></pre>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button class="btn btn-primary" onclick="runSchedule()">
                            <i class="fas fa-play"></i> تشغيل Scheduler الآن
                        </button>
                        <button class="btn btn-info" onclick="listSchedule()">
                            <i class="fas fa-list"></i> عرض قائمة المهام
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
function runSchedule() {
    alert('⏳ جاري تشغيل جميع المهام المجدولة...');
    // يجب إنشاء API endpoint لتشغيل schedule:run
}

function listSchedule() {
    alert('📋 عرض قائمة المهام المجدولة...');
    // يجب إنشاء API endpoint لعرض schedule:list
}
</script>
@endpush
