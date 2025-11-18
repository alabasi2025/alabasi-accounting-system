@extends('layouts.app')

@section('title', 'الصناديق')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-cash-register"></i>
                        الصناديق
                    </h4>
                    <a href="{{ route('cashboxes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        إضافة صندوق جديد
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
                                <th>الاسم</th>
                                <th>الفرع</th>
                                <th>الرصيد الحالي</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cashBoxes as $cashBox)
                            <tr>
                                <td>{{ $cashBox->code }}</td>
                                <td>{{ $cashBox->name_ar }}</td>
                                <td>{{ $cashBox->branch->name_ar }}</td>
                                <td>{{ number_format($cashBox->current_balance, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $cashBox->is_active ? 'success' : 'secondary' }}">
                                        {{ $cashBox->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('cashboxes.show', $cashBox) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('cashboxes.edit', $cashBox) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('cashboxes.destroy', $cashBox) }}" method="POST" class="d-inline">
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
                                <td colspan="6" class="text-center">لا توجد صناديق</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $cashBoxes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
