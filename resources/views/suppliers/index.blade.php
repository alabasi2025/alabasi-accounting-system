@extends('layouts.dashboard')

@section('title', 'الموردون')
@section('page-title', 'الموردون')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-truck"></i> قائمة الموردين</h5>
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> مورد جديد
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الكود</th>
                        <th>الاسم</th>
                        <th>الهاتف</th>
                        <th>البريد الإلكتروني</th>
                        <th>الرصيد</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers ?? [] as $supplier)
                    <tr>
                        <td>{{ $supplier->id }}</td>
                        <td>{{ $supplier->code }}</td>
                        <td>{{ $supplier->name }}</td>
                        <td>{{ $supplier->phone }}</td>
                        <td>{{ $supplier->email }}</td>
                        <td>{{ number_format($supplier->balance ?? 0) }}</td>
                        <td>
                            <a href="{{ route('suppliers.show', $supplier->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">لا يوجد موردون</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
