@extends('layouts.dashboard')

@section('title', 'عميل جديد')
@section('page-title', 'عميل جديد')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-user-plus"></i> إضافة عميل جديد</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('customers.store') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">الكود</label>
                    <input type="text" name="code" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">الاسم</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">الهاتف</label>
                    <input type="tel" name="phone" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">العنوان</label>
                <textarea name="address" class="form-control" rows="3"></textarea>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> حفظ
                </button>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
