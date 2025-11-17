@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-edit"></i> تعديل الحساب التحليلي
                    </h3>
                </div>

                <form action="{{ route('analytical-accounts.update', $analyticalAccount) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        <div class="row">
                            {{-- Analytical Account Type --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="analytical_account_type_id">النوع التحليلي <span class="text-danger">*</span></label>
                                    <select name="analytical_account_type_id" 
                                            id="analytical_account_type_id" 
                                            class="form-control @error('analytical_account_type_id') is-invalid @enderror"
                                            required
                                            onchange="loadAccounts()">
                                        <option value="">-- اختر النوع التحليلي --</option>
                                        @foreach($analyticalAccountTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('analytical_account_type_id', $analyticalAccount->analytical_account_type_id) == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('analytical_account_type_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Code --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">الرمز <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="code" 
                                           id="code" 
                                           class="form-control @error('code') is-invalid @enderror" 
                                           value="{{ old('code', $analyticalAccount->code) }}"
                                           required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Name --}}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">الاسم <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $analyticalAccount->name) }}"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Linked Account --}}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="account_id">الحساب المرتبط <span class="text-danger">*</span></label>
                                    <select name="account_id" 
                                            id="account_id" 
                                            class="form-control @error('account_id') is-invalid @enderror"
                                            required>
                                        <option value="">-- اختر الحساب المرتبط --</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" {{ old('account_id', $analyticalAccount->account_id) == $account->id ? 'selected' : '' }}>
                                                {{ $account->account_code }} - {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('account_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="form-group">
                            <label for="description">الوصف (اختياري)</label>
                            <textarea name="description" 
                                      id="description" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      rows="2">{{ old('description', $analyticalAccount->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Contact Info --}}
                        <div class="form-group">
                            <label for="contact_info">معلومات الاتصال (اختياري)</label>
                            <textarea name="contact_info" 
                                      id="contact_info" 
                                      class="form-control @error('contact_info') is-invalid @enderror" 
                                      rows="2">{{ old('contact_info', $analyticalAccount->contact_info) }}</textarea>
                            @error('contact_info')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Is Active --}}
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', $analyticalAccount->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">
                                    <i class="fas fa-check-circle text-success"></i> نشط
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> حفظ التعديلات
                        </button>
                        <a href="{{ route('analytical-accounts.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function loadAccounts() {
    const typeId = document.getElementById('analytical_account_type_id').value;
    const accountSelect = document.getElementById('account_id');
    const currentAccountId = {{ $analyticalAccount->account_id }};
    
    if (!typeId) {
        return;
    }
    
    const originalValue = accountSelect.value;
    accountSelect.innerHTML = '<option value="">جاري التحميل...</option>';
    
    fetch(`/analytical-accounts/get-accounts-by-type?analytical_account_type_id=${typeId}`)
        .then(response => response.json())
        .then(accounts => {
            accountSelect.innerHTML = '<option value="">-- اختر الحساب المرتبط --</option>';
            accounts.forEach(account => {
                const option = document.createElement('option');
                option.value = account.id;
                option.textContent = `${account.account_code} - ${account.name}`;
                if (account.id == currentAccountId) {
                    option.selected = true;
                }
                accountSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error:', error);
            accountSelect.innerHTML = '<option value="">حدث خطأ في تحميل الحسابات</option>';
        });
}
</script>

@endsection
