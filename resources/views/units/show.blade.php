@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-building"></i> تفاصيل الوحدة</h5>
                    <div>
                        <a href="{{ route('units.edit', $unit->id) }}" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil"></i> تعديل
                        </a>
                        <a href="{{ route('units.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-right"></i> رجوع
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%" class="bg-light">رمز الوحدة</th>
                                    <td>{{ $unit->unit_code }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">اسم الوحدة</th>
                                    <td>{{ $unit->unit_name }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">الوصف</th>
                                    <td>{{ $unit->description ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">العنوان</th>
                                    <td>{{ $unit->address ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">المدير</th>
                                    <td>{{ $unit->manager_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">الهاتف</th>
                                    <td>{{ $unit->phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">البريد الإلكتروني</th>
                                    <td>{{ $unit->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">الحالة</th>
                                    <td>
                                        @if($unit->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-danger">غير نشط</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">تاريخ الإنشاء</th>
                                    <td>{{ $unit->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">آخر تحديث</th>
                                    <td>{{ $unit->updated_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3"><i class="bi bi-buildings"></i> المؤسسات التابعة</h6>
                            @if($unit->companies && $unit->companies->count() > 0)
                                <div class="list-group">
                                    @foreach($unit->companies as $company)
                                        <a href="{{ route('companies.show', $company->id) }}" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">{{ $company->company_name }}</h6>
                                                <small>{{ $company->company_code }}</small>
                                            </div>
                                            <p class="mb-1"><small>المدير: {{ $company->manager_name ?? '-' }}</small></p>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> لا توجد مؤسسات مرتبطة بهذه الوحدة
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
