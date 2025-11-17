@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-buildings"></i> تفاصيل المؤسسة</h5>
                    <div>
                        <a href="{{ route('companies.edit', $company->id) }}" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil"></i> تعديل
                        </a>
                        <a href="{{ route('companies.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-right"></i> رجوع
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%" class="bg-light">رمز المؤسسة</th>
                                    <td>{{ $company->company_code }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">اسم المؤسسة</th>
                                    <td>{{ $company->company_name }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">الوحدة</th>
                                    <td>
                                        @if($company->unit)
                                            <a href="{{ route('units.show', $company->unit->id) }}">
                                                {{ $company->unit->unit_name }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">الوصف</th>
                                    <td>{{ $company->description ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">العنوان</th>
                                    <td>{{ $company->address ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">المدير</th>
                                    <td>{{ $company->manager_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">الهاتف</th>
                                    <td>{{ $company->phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">البريد الإلكتروني</th>
                                    <td>{{ $company->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">الحالة</th>
                                    <td>
                                        @if($company->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-danger">غير نشط</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">تاريخ الإنشاء</th>
                                    <td>{{ $company->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">آخر تحديث</th>
                                    <td>{{ $company->updated_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3"><i class="bi bi-diagram-3"></i> الفروع التابعة</h6>
                            @if($company->branches && $company->branches->count() > 0)
                                <div class="list-group">
                                    @foreach($company->branches as $branch)
                                        <div class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">{{ $branch->branch_name }}</h6>
                                                <small>{{ $branch->branch_code }}</small>
                                            </div>
                                            <p class="mb-1"><small>المدير: {{ $branch->manager_name ?? '-' }}</small></p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> لا توجد فروع مرتبطة بهذه المؤسسة
                                </div>
                            @endif

                            <h6 class="mb-3 mt-4"><i class="bi bi-journal-text"></i> الدليل المحاسبي</h6>
                            <div class="alert alert-secondary">
                                <p class="mb-2"><strong>عدد الحسابات:</strong> {{ $company->accounts ? $company->accounts->count() : 0 }}</p>
                                <a href="{{ route('accounts.index', ['company_id' => $company->id]) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-journal-text"></i> عرض الدليل المحاسبي
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
