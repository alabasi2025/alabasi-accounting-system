@extends('layouts.app')

@section('title', 'إعداد النظام')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-cogs"></i>
                        إعداد النظام - إنشاء الهيكلية الكاملة
                    </h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> ما سيتم إنشاؤه:</h5>
                        <ul class="mb-0">
                            <li><strong>وحدتين:</strong> وحدة أعمال الحديدة، وحدة أعمال العباسي</li>
                            <li><strong>7 مؤسسات:</strong> 6 في الحديدة + 1 في العباسي</li>
                            <li><strong>11 فرع:</strong> 5 في أعمال الموظفين + 5 في أعمال المحاسب + 1 في النقدية</li>
                            <li><strong>دليل حسابات كامل:</strong> في مؤسسة النقدية (البنوك، الصرافين، المحافظ، الصناديق)</li>
                        </ul>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <form action="{{ route('setup.execute') }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إنشاء الهيكلية الكاملة؟');">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    <i class="fas fa-play"></i>
                                    تنفيذ - إنشاء الهيكلية الكاملة
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('setup.reset') }}" method="POST" onsubmit="return confirm('⚠️ تحذير! سيتم حذف جميع البيانات (الوحدات، المؤسسات، الفروع، الحسابات). هل أنت متأكد؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-lg w-100">
                                    <i class="fas fa-trash"></i>
                                    إعادة تعيين - حذف جميع البيانات
                                </button>
                            </form>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5><i class="fas fa-database"></i> الهيكلية الحالية:</h5>

                    @if($units->count() > 0)
                        <div class="accordion" id="unitsAccordion">
                            @foreach($units as $unit)
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#unit{{ $unit->id }}">
                                            <i class="fas fa-building text-primary"></i>
                                            <strong class="ms-2">{{ $unit->name }}</strong>
                                            <span class="badge bg-info ms-2">{{ $unit->companies->count() }} مؤسسات</span>
                                        </button>
                                    </h2>
                                    <div id="unit{{ $unit->id }}" class="accordion-collapse collapse show">
                                        <div class="accordion-body">
                                            @if($unit->companies->count() > 0)
                                                @foreach($unit->companies as $company)
                                                    <div class="card mb-2">
                                                        <div class="card-body">
                                                            <h6>
                                                                <i class="fas fa-briefcase text-success"></i>
                                                                {{ $company->name }}
                                                                <span class="badge bg-secondary">{{ $company->branches->count() }} فروع</span>
                                                            </h6>
                                                            @if($company->branches->count() > 0)
                                                                <ul class="list-unstyled ms-4 mb-0">
                                                                    @foreach($company->branches as $branch)
                                                                        <li>
                                                                            <i class="fas fa-map-marker-alt text-warning"></i>
                                                                            {{ $branch->name }}
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <p class="text-muted">لا توجد مؤسسات في هذه الوحدة</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            لا توجد بيانات حالياً. اضغط على "تنفيذ" لإنشاء الهيكلية الكاملة.
                        </div>
                    @endif

                    @if($companies->where('code', 'COM-CASH')->first())
                        @php
                            $cashCompany = $companies->where('code', 'COM-CASH')->first();
                        @endphp
                        <hr class="my-4">
                        <h5><i class="fas fa-book text-success"></i> دليل الحسابات (مؤسسة النقدية):</h5>
                        @if($cashCompany->accounts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>الرمز</th>
                                            <th>اسم الحساب</th>
                                            <th>النوع</th>
                                            <th>الحالة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cashCompany->accounts->where('parent_id', null) as $account)
                                            @include('setup.partials.account-tree-item', ['account' => $account, 'level' => 0])
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">لا توجد حسابات في مؤسسة النقدية</p>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
