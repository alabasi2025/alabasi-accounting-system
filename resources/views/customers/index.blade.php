@extends('layouts.dashboard')

@section('title', 'العملاء')
@section('page-title', 'العملاء')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-users"></i> قائمة العملاء</h5>
        <a href="{{ route('customers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> عميل جديد
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
                    @forelse($customers ?? [] as $customer)
                    <tr>
                        <td>{{ $customer->id }}</td>
                        <td>{{ $customer->code }}</td>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ number_format($customer->balance ?? 0) }}</td>
                        <td>
                            <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">لا يوجد عملاء</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
