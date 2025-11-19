@extends('layouts.dashboard')

@section('title', 'الموظفون')
@section('page-title', 'الموظفون')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-user-tie"></i> قائمة الموظفين</h5>
        <a href="{{ route('employees.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> موظف جديد
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
                        <th>الوظيفة</th>
                        <th>الهاتف</th>
                        <th>الراتب</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees ?? [] as $employee)
                    <tr>
                        <td>{{ $employee->id }}</td>
                        <td>{{ $employee->code }}</td>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->position }}</td>
                        <td>{{ $employee->phone }}</td>
                        <td>{{ number_format($employee->salary ?? 0) }}</td>
                        <td>
                            <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">لا يوجد موظفون</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
