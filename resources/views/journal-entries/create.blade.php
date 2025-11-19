@extends('layouts.dashboard')

@section('title', 'قيد يومي جديد')
@section('page-title', 'قيد يومي جديد')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-book"></i> إنشاء قيد يومي جديد</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('journal-entries.store') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">التاريخ</label>
                    <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">رقم القيد</label>
                    <input type="text" name="entry_number" class="form-control" placeholder="تلقائي">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">البيان</label>
                <textarea name="description" class="form-control" rows="3" required></textarea>
            </div>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                سيتم إضافة واجهة كاملة لإدخال القيود قريباً
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> حفظ
                </button>
                <a href="{{ route('journal-entries.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
