@extends('layouts.admin')

@section('page-title', 'إدارة API')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">API</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- API Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plug"></i> Laravel API Resources & Sanctum</h5>
                </div>
                <div class="card-body">
                    <p class="lead">نظام API متكامل باستخدام Laravel Sanctum للمصادقة و API Resources للاستجابات المنظمة</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6><i class="fas fa-check-circle text-success"></i> الميزات:</h6>
                            <ul>
                                <li>مصادقة آمنة باستخدام Laravel Sanctum</li>
                                <li>API Resources منظمة</li>
                                <li>Rate Limiting</li>
                                <li>API Versioning</li>
                                <li>JSON Responses موحدة</li>
                                <li>Error Handling متقدم</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-shield-alt text-info"></i> الأمان:</h6>
                            <ul>
                                <li>Token-based Authentication</li>
                                <li>Token Abilities (Permissions)</li>
                                <li>CORS Configuration</li>
                                <li>Request Validation</li>
                                <li>Encrypted Responses</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- API Endpoints -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> API Endpoints المتاحة</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Method</th>
                                    <th>Endpoint</th>
                                    <th>الوصف</th>
                                    <th>المصادقة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-success">GET</span></td>
                                    <td><code>/api/units</code></td>
                                    <td>قائمة الوحدات</td>
                                    <td><i class="fas fa-lock text-warning"></i> مطلوبة</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-success">GET</span></td>
                                    <td><code>/api/companies</code></td>
                                    <td>قائمة المؤسسات</td>
                                    <td><i class="fas fa-lock text-warning"></i> مطلوبة</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-success">GET</span></td>
                                    <td><code>/api/accounts</code></td>
                                    <td>قائمة الحسابات</td>
                                    <td><i class="fas fa-lock text-warning"></i> مطلوبة</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-primary">POST</span></td>
                                    <td><code>/api/accounts</code></td>
                                    <td>إنشاء حساب جديد</td>
                                    <td><i class="fas fa-lock text-warning"></i> مطلوبة</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-info">PUT</span></td>
                                    <td><code>/api/accounts/{id}</code></td>
                                    <td>تحديث حساب</td>
                                    <td><i class="fas fa-lock text-warning"></i> مطلوبة</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-danger">DELETE</span></td>
                                    <td><code>/api/accounts/{id}</code></td>
                                    <td>حذف حساب</td>
                                    <td><i class="fas fa-lock text-warning"></i> مطلوبة</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Authentication -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-key"></i> المصادقة</h5>
                </div>
                <div class="card-body">
                    <h6>الحصول على Token:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>POST /api/login
Content-Type: application/json

{
  "email": "admin@alabasi.es",
  "password": "password"
}

Response:
{
  "token": "1|abc123...",
  "type": "Bearer"
}</code></pre>

                    <h6 class="mt-3">استخدام Token:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>GET /api/accounts
Authorization: Bearer 1|abc123...
Accept: application/json</code></pre>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-code"></i> مثال على الاستخدام</h5>
                </div>
                <div class="card-body">
                    <h6>JavaScript (Fetch API):</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>fetch('/api/accounts', {
  method: 'GET',
  headers: {
    'Authorization': 'Bearer ' + token,
    'Accept': 'application/json'
  }
})
.then(response => response.json())
.then(data => console.log(data));</code></pre>

                    <h6 class="mt-3">cURL:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>curl -X GET \
  https://alabasi.es/api/accounts \
  -H 'Authorization: Bearer 1|abc123...' \
  -H 'Accept: application/json'</code></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Rate Limiting -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-tachometer-alt"></i> Rate Limiting</h5>
                </div>
                <div class="card-body">
                    <p>الحد الأقصى للطلبات: <strong>60 طلب في الدقيقة</strong></p>
                    <p>عند تجاوز الحد، سيتم إرجاع خطأ 429 (Too Many Requests)</p>
                    
                    <h6 class="mt-3">Response Headers:</h6>
                    <pre class="bg-light p-3 rounded"><code>X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1700000000</code></pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
