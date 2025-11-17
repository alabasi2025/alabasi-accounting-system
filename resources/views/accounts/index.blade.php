@extends('layouts.app')

@section('title', 'دليل الحسابات')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-list-ul"></i> دليل الحسابات</h2>
        <a href="{{ route('accounts.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> إضافة حساب جديد
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            @if($accounts->count() > 0)
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>الرمز</th>
                            <th>اسم الحساب</th>
                            <th>النوع</th>
                            <th>السماح بالترحيل</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accounts as $account)
                        <tr>
                            <td><strong>{{ $account->code }}</strong></td>
                            <td>{{ $account->name_ar }}</td>
                            <td>
                                @if($account->is_parent)
                                    <span class="badge bg-info">حساب رئيسي</span>
                                @else
                                    <span class="badge bg-success">حساب فرعي</span>
                                @endif
                            </td>
                            <td>
                                @if($account->allow_posting)
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> نعم</span>
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> لا</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('accounts.edit', $account->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> تعديل
                                </a>
                                <form action="{{ route('accounts.destroy', $account->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                        <i class="bi bi-trash"></i> حذف
                                    </button>
                                </form>
                            </td>
                        </tr>

                        @if($account->children && $account->children->count() > 0)
                            @foreach($account->children as $child)
                            <tr class="table-light">
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $child->code }}</td>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $child->name_ar }}</td>
                                <td><span class="badge bg-success">حساب فرعي</span></td>
                                <td>
                                    @if($child->allow_posting)
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> نعم</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> لا</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('accounts.edit', $child->id) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i> لا توجد حسابات حتى الآن. 
                    <a href="{{ route('accounts.create') }}">أضف حساب جديد</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
