@extends('layouts.app')

@section('title', 'تفاصيل الفرع')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-building me-2"></i>
                        تفاصيل الفرع: {{ $branch->branch_name }}
                    </h5>
                    <div>
                        <a href="{{ route('branches.edit', $branch->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <a href="{{ route('branches.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-right"></i> رجوع
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- المعلومات الأساسية -->
                        <div class="col-md-6">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                المعلومات الأساسية
                            </h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%" class="bg-light">رمز الفرع</th>
                                    <td>{{ $branch->branch_code }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">اسم الفرع</th>
                                    <td>{{ $branch->branch_name }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">المؤسسة</th>
                                    <td>
                                        @if($branch->company)
                                            <span class="badge bg-info">{{ $branch->company->company_name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">الوحدة</th>
                                    <td>
                                        @if($branch->unit)
                                            <span class="badge bg-secondary">{{ $branch->unit->unit_name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">الحالة</th>
                                    <td>
                                        @if($branch->is_active)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle"></i> نشط
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle"></i> غير نشط
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- معلومات الاتصال -->
                        <div class="col-md-6">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-address-card me-2"></i>
                                معلومات الاتصال
                            </h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%" class="bg-light">العنوان</th>
                                    <td>{{ $branch->address ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">الهاتف</th>
                                    <td>
                                        @if($branch->phone)
                                            <i class="fas fa-phone text-success me-2"></i>
                                            {{ $branch->phone }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">البريد الإلكتروني</th>
                                    <td>
                                        @if($branch->email)
                                            <i class="fas fa-envelope text-primary me-2"></i>
                                            <a href="mailto:{{ $branch->email }}">{{ $branch->email }}</a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">اسم المدير</th>
                                    <td>
                                        @if($branch->manager_name)
                                            <i class="fas fa-user text-info me-2"></i>
                                            {{ $branch->manager_name }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">تاريخ الإنشاء</th>
                                    <td>
                                        <i class="fas fa-calendar text-secondary me-2"></i>
                                        {{ $branch->created_at->format('Y-m-d H:i') }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection
