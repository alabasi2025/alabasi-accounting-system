@extends('layouts.app')

@section('title', 'الحسابات البنكية')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-university"></i>
                        الحسابات البنكية
                    </h4>
                    <a href="{{ route('bank-accounts.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        إضافة حساب بنكي جديد
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>الرمز</th>
                                <th>اسم البنك</th>
                                <th>رقم الحساب</th>
                                <th>الفرع</th>
                                <th>الرصيد الحالي</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bankAccounts as $bankAccount)
                            <tr>
                                <td>{{ $bankAccount->code }}</td>
                                <td>{{ $bankAccount->bank_name }}</td>
                                <td>{{ $bankAccount->account_number }}</td>
                                <td>{{ $bankAccount->branch->name_ar }}</td>
                                <td>{{ number_format($bankAccount->current_balance, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $bankAccount->is_active ? 'success' : 'secondary' }}">
                                        {{ $bankAccount->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('bank-accounts.show', $bankAccount) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('bank-accounts.edit', $bankAccount) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('bank-accounts.destroy', $bankAccount) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">لا توجد حسابات بنكية</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $bankAccounts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
