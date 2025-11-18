@extends('layouts.app')

@section('title', 'تعديل الفرع')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        تعديل الفرع: {{ $branch->branch_name }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('branches.update', $branch->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- رمز الفرع -->
                            <div class="col-md-6 mb-3">
                                <label for="branch_code" class="form-label">
                                    <i class="fas fa-barcode text-primary me-1"></i>
                                    رمز الفرع <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('branch_code') is-invalid @enderror" 
                                       id="branch_code" 
                                       name="branch_code" 
                                       value="{{ old('branch_code', $branch->branch_code) }}" 
                                       required>
                                @error('branch_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- اسم الفرع -->
                            <div class="col-md-6 mb-3">
                                <label for="branch_name" class="form-label">
                                    <i class="fas fa-building text-primary me-1"></i>
                                    اسم الفرع <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('branch_name') is-invalid @enderror" 
                                       id="branch_name" 
                                       name="branch_name" 
                                       value="{{ old('branch_name', $branch->branch_name) }}" 
                                       required>
                                @error('branch_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- المؤسسة -->
                            <div class="col-md-6 mb-3">
                                <label for="company_id" class="form-label">
                                    <i class="fas fa-city text-primary me-1"></i>
                                    المؤسسة <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('company_id') is-invalid @enderror" 
                                        id="company_id" 
                                        name="company_id" 
                                        required>
                                    <option value="">اختر المؤسسة</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" 
                                                {{ old('company_id', $branch->company_id) == $company->id ? 'selected' : '' }}>
                                            {{ $company->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- الوحدة -->
                            <div class="col-md-6 mb-3">
                                <label for="unit_id" class="form-label">
                                    <i class="fas fa-layer-group text-primary me-1"></i>
                                    الوحدة
                                </label>
                                <select class="form-select @error('unit_id') is-invalid @enderror" 
                                        id="unit_id" 
                                        name="unit_id">
                                    <option value="">اختر الوحدة (اختياري)</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" 
                                                {{ old('unit_id', $branch->unit_id) == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->unit_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('unit_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- العنوان -->
                            <div class="col-md-12 mb-3">
                                <label for="address" class="form-label">
                                    <i class="fas fa-map-marker-alt text-primary me-1"></i>
                                    العنوان
                                </label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" 
                                          name="address" 
                                          rows="2">{{ old('address', $branch->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- الهاتف -->
                            <div class="col-md-4 mb-3">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone text-primary me-1"></i>
                                    الهاتف
                                </label>
                                <input type="text" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone', $branch->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- البريد الإلكتروني -->
                            <div class="col-md-4 mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope text-primary me-1"></i>
                                    البريد الإلكتروني
                                </label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $branch->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- اسم المدير -->
                            <div class="col-md-4 mb-3">
                                <label for="manager_name" class="form-label">
                                    <i class="fas fa-user text-primary me-1"></i>
                                    اسم المدير
                                </label>
                                <input type="text" 
                                       class="form-control @error('manager_name') is-invalid @enderror" 
                                       id="manager_name" 
                                       name="manager_name" 
                                       value="{{ old('manager_name', $branch->manager_name) }}">
                                @error('manager_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- الحالة -->
                            <div class="col-md-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1"
                                           {{ old('is_active', $branch->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        <i class="fas fa-toggle-on text-success me-1"></i>
                                        الفرع نشط
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- الأزرار -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save me-1"></i>
                                    حفظ التعديلات
                                </button>
                                <a href="{{ route('branches.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i>
                                    إلغاء
                                </a>
                                <a href="{{ route('branches.show', $branch->id) }}" class="btn btn-info">
                                    <i class="fas fa-eye me-1"></i>
                                    عرض التفاصيل
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
