@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">
                    <i class="fas fa-tags"></i> الحسابات التحليلية
                </h3>
                <a href="{{ route('analytical-accounts.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> إضافة حساب تحليلي
                </a>
            </div>
        </div>

        <div class="card-body">
            {{-- Filters --}}
            <form method="GET" action="{{ route('analytical-accounts.index') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <select name="analytical_account_type_id" class="form-control" onchange="this.form.submit()">
                            <option value="">-- جميع الأنواع --</option>
                            @foreach($analyticalAccountTypes as $type)
                                <option value="{{ $type->id }}" {{ request('analytical_account_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="is_active" class="form-control" onchange="this.form.submit()">
                            <option value="">-- جميع الحالات --</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-info btn-block">
                            <i class="fas fa-search"></i> بحث
                        </button>
                    </div>
                </div>
            </form>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>الرمز</th>
                            <th>الاسم</th>
                            <th>النوع</th>
                            <th>الحساب المرتبط</th>
                            <th>معلومات الاتصال</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($analyticalAccounts as $analyticalAccount)
                            <tr>
                                <td><strong>{{ $analyticalAccount->code }}</strong></td>
                                <td>{{ $analyticalAccount->name }}</td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $analyticalAccount->analyticalAccountType->name }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $analyticalAccount->account->account_code }} - {{ $analyticalAccount->account->name }}
                                    </small>
                                </td>
                                <td>
                                    @if($analyticalAccount->contact_info)
                                        <small>{{ Str::limit($analyticalAccount->contact_info, 30) }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($analyticalAccount->is_active)
                                        <span class="badge badge-success">نشط</span>
                                    @else
                                        <span class="badge badge-secondary">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('analytical-accounts.edit', $analyticalAccount) }}" 
                                           class="btn btn-warning" 
                                           title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('analytical-accounts.destroy', $analyticalAccount) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الحساب التحليلي؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    <i class="fas fa-inbox"></i> لا توجد حسابات تحليلية
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center">
                {{ $analyticalAccounts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
