@extends('layouts.app')

@section('title', 'دليل الاستخدام')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-book"></i>
                        دليل الاستخدام - نظام العباسي المحاسبي
                    </h4>
                    <div>
                        <button class="btn btn-light btn-sm" onclick="window.print()">
                            <i class="fas fa-print"></i>
                            طباعة
                        </button>
                        <a href="{{ route('manual.export') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-file-pdf"></i>
                            تصدير PDF
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div id="manual-content" class="markdown-content">
                        {!! \Illuminate\Support\Str::markdown($manualContent) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.markdown-content {
    font-size: 1.1rem;
    line-height: 1.8;
}

.markdown-content h1 {
    color: #2c3e50;
    border-bottom: 3px solid #3498db;
    padding-bottom: 10px;
    margin-top: 30px;
    margin-bottom: 20px;
}

.markdown-content h2 {
    color: #34495e;
    border-bottom: 2px solid #95a5a6;
    padding-bottom: 8px;
    margin-top: 25px;
    margin-bottom: 15px;
}

.markdown-content h3 {
    color: #555;
    margin-top: 20px;
    margin-bottom: 10px;
}

.markdown-content ul, .markdown-content ol {
    margin-right: 20px;
}

.markdown-content code {
    background-color: #f4f4f4;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
}

.markdown-content pre {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 15px;
    overflow-x: auto;
}

@media print {
    .card-header button,
    .card-header a {
        display: none;
    }
}
</style>
@endsection
