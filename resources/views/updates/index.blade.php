@extends('layouts.app')

@section('title', 'سجل التحديثات')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-history"></i>
                        سجل التحديثات - نظام العباسي المحاسبي
                    </h4>
                </div>
                <div class="card-body">
                    <div id="changelog-content" class="markdown-content">
                        {!! \Illuminate\Support\Str::markdown($changelogContent) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.markdown-content {
    font-size: 1.05rem;
    line-height: 1.7;
}

.markdown-content h2 {
    color: #27ae60;
    border-bottom: 2px solid #27ae60;
    padding-bottom: 8px;
    margin-top: 30px;
    margin-bottom: 20px;
}

.markdown-content h3 {
    color: #2980b9;
    margin-top: 20px;
    margin-bottom: 10px;
}

.markdown-content ul {
    margin-right: 20px;
}

.markdown-content li {
    margin-bottom: 8px;
}
</style>
@endsection
