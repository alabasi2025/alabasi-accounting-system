@extends('layouts.admin')

@section('page-title', 'إدارة الذاكرة المؤقتة')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">الذاكرة المؤقتة</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Cache Types -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-cog fa-3x text-primary mb-3"></i>
                    <h5>ذاكرة الإعدادات</h5>
                    <p class="text-muted">Config Cache</p>
                    <button class="btn btn-primary btn-sm" onclick="clearCache('config')">
                        <i class="fas fa-broom"></i> مسح
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-route fa-3x text-success mb-3"></i>
                    <h5>ذاكرة المسارات</h5>
                    <p class="text-muted">Route Cache</p>
                    <button class="btn btn-success btn-sm" onclick="clearCache('route')">
                        <i class="fas fa-broom"></i> مسح
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-eye fa-3x text-info mb-3"></i>
                    <h5>ذاكرة العروض</h5>
                    <p class="text-muted">View Cache</p>
                    <button class="btn btn-info btn-sm" onclick="clearCache('view')">
                        <i class="fas fa-broom"></i> مسح
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-database fa-3x text-warning mb-3"></i>
                    <h5>ذاكرة التطبيق</h5>
                    <p class="text-muted">Application Cache</p>
                    <button class="btn btn-warning btn-sm" onclick="clearCache('cache')">
                        <i class="fas fa-broom"></i> مسح
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- All Cache -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> مسح جميع الذاكرة المؤقتة</h5>
                </div>
                <div class="card-body text-center">
                    <p class="lead">هذا الإجراء سيمسح جميع أنواع الذاكرة المؤقتة في النظام</p>
                    <button class="btn btn-danger btn-lg" onclick="clearCache('all')">
                        <i class="fas fa-bomb"></i> مسح الكل (optimize:clear)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cache Information -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> معلومات الذاكرة المؤقتة</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>المحرك الافتراضي:</strong></td>
                            <td><span class="badge bg-primary">{{ config('cache.default') }}</span></td>
                        </tr>
                        <tr>
                            <td><strong>مدة الصلاحية:</strong></td>
                            <td>{{ config('cache.ttl', 3600) }} ثانية</td>
                        </tr>
                        <tr>
                            <td><strong>المسار:</strong></td>
                            <td><code>storage/framework/cache</code></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-terminal"></i> أوامر مفيدة</h5>
                </div>
                <div class="card-body">
                    <div class="bg-dark text-white p-3 rounded">
                        <code>
                            # مسح ذاكرة الإعدادات<br>
                            php artisan config:clear<br><br>
                            
                            # مسح ذاكرة المسارات<br>
                            php artisan route:clear<br><br>
                            
                            # مسح ذاكرة العروض<br>
                            php artisan view:clear<br><br>
                            
                            # مسح كل شيء<br>
                            php artisan optimize:clear
                        </code>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Results -->
    <div class="row mt-4" id="results" style="display: none;">
        <div class="col-12">
            <div class="alert alert-success" role="alert">
                <h5 class="alert-heading"><i class="fas fa-check-circle"></i> نجح!</h5>
                <p id="results-message"></p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function clearCache(type) {
    const confirmMessage = type === 'all' 
        ? 'هل أنت متأكد من مسح جميع أنواع الذاكرة المؤقتة؟'
        : 'هل أنت متأكد من مسح هذه الذاكرة المؤقتة؟';
    
    if (!confirm(confirmMessage)) {
        return;
    }

    const resultsDiv = document.getElementById('results');
    const resultsMessage = document.getElementById('results-message');
    
    resultsDiv.style.display = 'none';

    fetch('{{ route("admin.cache.clear") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ type: type })
    })
    .then(response => response.json())
    .then(data => {
        resultsDiv.style.display = 'block';
        resultsMessage.textContent = data.message;
        
        if (!data.success) {
            resultsDiv.querySelector('.alert').classList.remove('alert-success');
            resultsDiv.querySelector('.alert').classList.add('alert-danger');
        }
    })
    .catch(error => {
        resultsDiv.style.display = 'block';
        resultsDiv.querySelector('.alert').classList.remove('alert-success');
        resultsDiv.querySelector('.alert').classList.add('alert-danger');
        resultsMessage.textContent = 'حدث خطأ: ' + error;
    });
}
</script>
@endpush
