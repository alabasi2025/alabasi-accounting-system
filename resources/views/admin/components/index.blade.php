@extends('layouts.admin')

@section('page-title', 'Laravel Livewire - المكونات التفاعلية')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">Livewire</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Livewire Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Laravel Livewire - المكونات التفاعلية</h5>
                </div>
                <div class="card-body">
                    <p class="lead">بناء واجهات تفاعلية حديثة دون كتابة JavaScript!</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6><i class="fas fa-check-circle text-success"></i> الميزات:</h6>
                            <ul>
                                <li>واجهات تفاعلية بدون JavaScript</li>
                                <li>Real-time Validation</li>
                                <li>File Uploads</li>
                                <li>Pagination</li>
                                <li>Real-time Search</li>
                                <li>Form Handling</li>
                                <li>Component Nesting</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-rocket text-info"></i> الفوائد:</h6>
                            <ul>
                                <li>تطوير أسرع</li>
                                <li>كود أقل</li>
                                <li>سهولة الصيانة</li>
                                <li>تكامل سلس مع Laravel</li>
                                <li>أداء عالي</li>
                                <li>SEO Friendly</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Components -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-puzzle-piece"></i> المكونات المتاحة</h5>
                    <button class="btn btn-light btn-sm" onclick="createComponent()">
                        <i class="fas fa-plus"></i> إنشاء مكون
                    </button>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6><i class="fas fa-search text-primary"></i> SearchComponent</h6>
                                    <p class="text-muted small">بحث فوري مع نتائج مباشرة</p>
                                    <code>livewire:search</code>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary" onclick="viewComponent('search')">
                                            <i class="fas fa-eye"></i> عرض
                                        </button>
                                        <button class="btn btn-sm btn-info" onclick="editComponent('search')">
                                            <i class="fas fa-edit"></i> تعديل
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6><i class="fas fa-table text-success"></i> DataTable</h6>
                                    <p class="text-muted small">جدول بيانات تفاعلي مع فلترة</p>
                                    <code>livewire:data-table</code>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary" onclick="viewComponent('data-table')">
                                            <i class="fas fa-eye"></i> عرض
                                        </button>
                                        <button class="btn btn-sm btn-info" onclick="editComponent('data-table')">
                                            <i class="fas fa-edit"></i> تعديل
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6><i class="fas fa-wpforms text-warning"></i> FormBuilder</h6>
                                    <p class="text-muted small">نماذج ديناميكية مع validation</p>
                                    <code>livewire:form-builder</code>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary" onclick="viewComponent('form-builder')">
                                            <i class="fas fa-eye"></i> عرض
                                        </button>
                                        <button class="btn btn-sm btn-info" onclick="editComponent('form-builder')">
                                            <i class="fas fa-edit"></i> تعديل
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6><i class="fas fa-upload text-info"></i> FileUpload</h6>
                                    <p class="text-muted small">رفع ملفات مع معاينة</p>
                                    <code>livewire:file-upload</code>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary" onclick="viewComponent('file-upload')">
                                            <i class="fas fa-eye"></i> عرض
                                        </button>
                                        <button class="btn btn-sm btn-info" onclick="editComponent('file-upload')">
                                            <i class="fas fa-edit"></i> تعديل
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6><i class="fas fa-bell text-danger"></i> NotificationCenter</h6>
                                    <p class="text-muted small">مركز إشعارات فوري</p>
                                    <code>livewire:notifications</code>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary" onclick="viewComponent('notifications')">
                                            <i class="fas fa-eye"></i> عرض
                                        </button>
                                        <button class="btn btn-sm btn-info" onclick="editComponent('notifications')">
                                            <i class="fas fa-edit"></i> تعديل
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6><i class="fas fa-chart-line text-primary"></i> LiveChart</h6>
                                    <p class="text-muted small">رسوم بيانية تحديث فوري</p>
                                    <code>livewire:live-chart</code>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary" onclick="viewComponent('live-chart')">
                                            <i class="fas fa-eye"></i> عرض
                                        </button>
                                        <button class="btn btn-sm btn-info" onclick="editComponent('live-chart')">
                                            <i class="fas fa-edit"></i> تعديل
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                    <h5 class="mb-0"><i class="fas fa-code"></i> إنشاء Component</h5>
                </div>
                <div class="card-body">
                    <h6>إنشاء Component جديد:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>php artisan make:livewire SearchComponent</code></pre>

                    <h6 class="mt-3">ملف Component (PHP):</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>class SearchComponent extends Component
{
    public $search = '';
    
    public function render()
    {
        return view('livewire.search', [
            'results' => Account::where(
                'name', 'like', "%{$this->search}%"
            )->get()
        ]);
    }
}</code></pre>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-file-code"></i> View (Blade)</h5>
                </div>
                <div class="card-body">
                    <h6>ملف View:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>&lt;div&gt;
    &lt;input 
        type="text" 
        wire:model.live="search"
        placeholder="ابحث..."
    &gt;
    
    &lt;ul&gt;
        @foreach($results as $result)
            &lt;li&gt;{{ $result->name }}&lt;/li&gt;
        @endforeach
    &lt;/ul&gt;
&lt;/div&gt;</code></pre>

                    <h6 class="mt-3">الاستخدام في Blade:</h6>
                    <pre class="bg-dark text-white p-3 rounded"><code>@livewire('search-component')</code></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Demo -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-magic"></i> عرض توضيحي تفاعلي</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>مثال: بحث فوري</h6>
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="ابحث عن حساب..." id="liveSearch">
                            </div>
                            <div id="searchResults" class="alert alert-info">
                                النتائج ستظهر هنا فوراً أثناء الكتابة...
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6>مثال: عداد تفاعلي</h6>
                            <div class="text-center">
                                <h1 id="counter" class="display-1 text-primary">0</h1>
                                <div class="btn-group">
                                    <button class="btn btn-danger" onclick="decrementCounter()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button class="btn btn-success" onclick="incrementCounter()">
                                        <i class="fas fa-plus"></i>
                                    </button>
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
let counter = 0;

function incrementCounter() {
    counter++;
    document.getElementById('counter').textContent = counter;
}

function decrementCounter() {
    counter--;
    document.getElementById('counter').textContent = counter;
}

// Live Search Demo
document.getElementById('liveSearch')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value;
    const resultsDiv = document.getElementById('searchResults');
    
    if (searchTerm.length > 0) {
        resultsDiv.innerHTML = `<strong>البحث عن:</strong> "${searchTerm}"<br><small>في تطبيق حقيقي، ستظهر النتائج من قاعدة البيانات</small>`;
    } else {
        resultsDiv.innerHTML = 'النتائج ستظهر هنا فوراً أثناء الكتابة...';
    }
});

function createComponent() {
    alert('📝 إنشاء مكون Livewire جديد...');
}

function viewComponent(name) {
    alert(`👁️ عرض المكون: ${name}`);
}

function editComponent(name) {
    alert(`✏️ تعديل المكون: ${name}`);
}
</script>
@endpush
