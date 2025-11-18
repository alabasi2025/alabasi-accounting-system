@extends('layouts.admin')

@section('page-title', 'إدارة الإشعارات')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">الإشعارات</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Notification Channels -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-database fa-3x text-primary mb-3"></i>
                    <h5>Database</h5>
                    <p class="text-muted">إشعارات داخل النظام</p>
                    <span class="badge bg-success">نشط</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-envelope fa-3x text-info mb-3"></i>
                    <h5>Email</h5>
                    <p class="text-muted">البريد الإلكتروني</p>
                    <span class="badge bg-success">نشط</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-sms fa-3x text-warning mb-3"></i>
                    <h5>SMS</h5>
                    <p class="text-muted">الرسائل النصية</p>
                    <span class="badge bg-secondary">معطل</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fab fa-slack fa-3x text-danger mb-3"></i>
                    <h5>Slack</h5>
                    <p class="text-muted">Slack Webhook</p>
                    <span class="badge bg-secondary">معطل</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification System Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-bell"></i> نظام الإشعارات المتقدم</h5>
                </div>
                <div class="card-body">
                    <p class="lead">نظام إشعارات متكامل يدعم قنوات متعددة مع إمكانية الجدولة والتخصيص</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6><i class="fas fa-check-circle text-success"></i> الميزات:</h6>
                            <ul>
                                <li>إشعارات داخل النظام (Database)</li>
                                <li>إشعارات البريد الإلكتروني</li>
                                <li>إشعارات SMS (Nexmo/Twilio)</li>
                                <li>إشعارات Slack</li>
                                <li>إشعارات مجدولة</li>
                                <li>إشعارات قابلة للتخصيص</li>
                                <li>Notification Queuing</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-cog text-info"></i> الإعدادات:</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Mail Driver:</strong></td>
                                    <td><span class="badge bg-primary">{{ config('mail.default') }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>From Address:</strong></td>
                                    <td>{{ config('mail.from.address') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>From Name:</strong></td>
                                    <td>{{ config('mail.from.name') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Notifications -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> الإشعارات المتاحة</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>اسم الإشعار</th>
                                    <th>القنوات</th>
                                    <th>الوصف</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>SystemNotification</code></td>
                                    <td>
                                        <span class="badge bg-primary">Database</span>
                                        <span class="badge bg-info">Email</span>
                                    </td>
                                    <td>إشعار عام للنظام</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="sendNotification('SystemNotification')">
                                            <i class="fas fa-paper-plane"></i> إرسال
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><code>AccountCreatedNotification</code></td>
                                    <td>
                                        <span class="badge bg-primary">Database</span>
                                    </td>
                                    <td>إشعار عند إنشاء حساب</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="sendNotification('AccountCreated')">
                                            <i class="fas fa-paper-plane"></i> إرسال
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><code>BackupCompletedNotification</code></td>
                                    <td>
                                        <span class="badge bg-info">Email</span>
                                        <span class="badge bg-danger">Slack</span>
                                    </td>
                                    <td>إشعار عند اكتمال النسخ الاحتياطي</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="sendNotification('BackupCompleted')">
                                            <i class="fas fa-paper-plane"></i> إرسال
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

    <!-- Code Examples -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-code"></i> إنشاء Notification</h5>
                </div>
                <div class="card-body">
                    <h6>إنشاء Notification جديدة:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>php artisan make:notification InvoiceCreated</code></pre>

                    <h6 class="mt-3">إرسال Notification:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>use App\Notifications\SystemNotification;

$user->notify(new SystemNotification(
    'عنوان الإشعار',
    'محتوى الإشعار',
    'success'
));</code></pre>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-envelope"></i> إعدادات البريد</h5>
                </div>
                <div class="card-body">
                    <h6>في ملف <code>.env</code>:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@alabasi.es
MAIL_FROM_NAME="نظام الأباسي"</code></pre>

                    <button class="btn btn-primary mt-2" onclick="testEmail()">
                        <i class="fas fa-paper-plane"></i> اختبار البريد
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Notifications -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-history"></i> الإشعارات الأخيرة</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><i class="fas fa-info-circle text-info"></i> مرحباً بك في النظام</h6>
                                <small>منذ ساعة</small>
                            </div>
                            <p class="mb-1">تم تسجيل دخولك بنجاح إلى نظام الأباسي المحاسبي</p>
                            <small>Database</small>
                        </div>
                        
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><i class="fas fa-check-circle text-success"></i> تم إنشاء حساب جديد</h6>
                                <small>منذ 3 ساعات</small>
                            </div>
                            <p class="mb-1">تم إنشاء حساب: الصندوق الرئيسي</p>
                            <small>Database, Email</small>
                        </div>
                        
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><i class="fas fa-database text-primary"></i> نسخ احتياطي مكتمل</h6>
                                <small>منذ يوم</small>
                            </div>
                            <p class="mb-1">تم إنشاء نسخة احتياطية بنجاح</p>
                            <small>Email, Slack</small>
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
function sendNotification(type) {
    alert('⏳ جاري إرسال الإشعار: ' + type);
    // يجب إنشاء API endpoint لإرسال الإشعارات
}

function testEmail() {
    if (confirm('هل تريد إرسال بريد اختبار؟')) {
        alert('⏳ جاري إرسال البريد الاختباري...');
        // يجب إنشاء API endpoint لاختبار البريد
    }
}
</script>
@endpush
