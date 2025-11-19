@extends('layouts.admin')

@section('page-title', 'إدارة الأحداث والمستمعين')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">Events & Listeners</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Events System Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> نظام الأحداث والمستمعين</h5>
                </div>
                <div class="card-body">
                    <p class="lead">نظام متقدم للتفاعل مع أحداث النظام وتنفيذ إجراءات تلقائية</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6><i class="fas fa-check-circle text-success"></i> الفوائد:</h6>
                            <ul>
                                <li>فصل المنطق البرمجي (Decoupling)</li>
                                <li>إعادة استخدام الكود</li>
                                <li>سهولة الصيانة</li>
                                <li>تنفيذ إجراءات متعددة لحدث واحد</li>
                                <li>Queued Listeners للأداء العالي</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-cog text-info"></i> حالات الاستخدام:</h6>
                            <ul>
                                <li>إرسال إشعارات عند إنشاء حساب</li>
                                <li>تسجيل الأنشطة (Activity Log)</li>
                                <li>تحديث الإحصائيات</li>
                                <li>إرسال بريد إلكتروني</li>
                                <li>تنفيذ مهام خلفية</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Events -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> الأحداث المتاحة</h5>
                    <button class="btn btn-light btn-sm" onclick="createEvent()">
                        <i class="fas fa-plus"></i> إنشاء حدث
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>اسم الحدث</th>
                                    <th>المستمعين</th>
                                    <th>الوصف</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>AccountCreated</code></td>
                                    <td>
                                        <span class="badge bg-primary">SendAccountCreatedNotification</span>
                                    </td>
                                    <td>يُطلق عند إنشاء حساب جديد</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="viewEvent('AccountCreated')">
                                            <i class="fas fa-eye"></i> عرض
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><code>UserLoggedIn</code></td>
                                    <td>
                                        <span class="badge bg-primary">LogUserActivity</span>
                                        <span class="badge bg-primary">UpdateLastLogin</span>
                                    </td>
                                    <td>يُطلق عند تسجيل دخول المستخدم</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="viewEvent('UserLoggedIn')">
                                            <i class="fas fa-eye"></i> عرض
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><code>TransactionCreated</code></td>
                                    <td>
                                        <span class="badge bg-primary">UpdateAccountBalance</span>
                                        <span class="badge bg-primary">SendTransactionNotification</span>
                                    </td>
                                    <td>يُطلق عند إنشاء معاملة</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="viewEvent('TransactionCreated')">
                                            <i class="fas fa-eye"></i> عرض
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
                    <h5 class="mb-0"><i class="fas fa-code"></i> إنشاء Event</h5>
                </div>
                <div class="card-body">
                    <h6>إنشاء Event جديد:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>php artisan make:event AccountCreated</code></pre>

                    <h6 class="mt-3">ملف Event:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>class AccountCreated
{
    use Dispatchable, SerializesModels;
    
    public function __construct(
        public Account $account
    ) {}
}</code></pre>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-headphones"></i> إنشاء Listener</h5>
                </div>
                <div class="card-body">
                    <h6>إنشاء Listener جديد:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>php artisan make:listener SendNotification --event=AccountCreated</code></pre>

                    <h6 class="mt-3">ملف Listener:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>class SendNotification
{
    public function handle(AccountCreated $event)
    {
        // إرسال إشعار
        $event->account->user->notify(
            new AccountCreatedNotification()
        );
    }
}</code></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Registration -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-cog"></i> تسجيل الأحداث</h5>
                </div>
                <div class="card-body">
                    <h6>في ملف <code>app/Providers/EventServiceProvider.php</code>:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>protected $listen = [
    AccountCreated::class => [
        SendAccountCreatedNotification::class,
        LogAccountActivity::class,
    ],
    
    UserLoggedIn::class => [
        LogUserActivity::class,
        UpdateLastLogin::class,
    ],
];</code></pre>

                    <h6 class="mt-3">إطلاق حدث:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>// في Controller أو Model
event(new AccountCreated($account));

// أو باستخدام Helper
AccountCreated::dispatch($account);</code></pre>
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
                    <button class="btn btn-primary" onclick="generateEvents()">
                        <i class="fas fa-magic"></i> إنشاء Events تلقائياً
                    </button>
                    <button class="btn btn-success" onclick="cacheEvents()">
                        <i class="fas fa-save"></i> Cache Events
                    </button>
                    <button class="btn btn-info" onclick="listEvents()">
                        <i class="fas fa-list"></i> عرض جميع الأحداث
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function createEvent() {
    alert('📝 إنشاء Event جديد...');
}

function viewEvent(name) {
    alert(`👁️ عرض Event: ${name}`);
}

function generateEvents() {
    alert('⏳ جاري إنشاء Events تلقائياً...');
}

function cacheEvents() {
    alert('⏳ جاري حفظ Events في Cache...');
}

function listEvents() {
    alert('📋 عرض جميع الأحداث المسجلة...');
}
</script>
@endpush
