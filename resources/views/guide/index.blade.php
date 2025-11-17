@extends('layouts.app')

@section('title', 'دليل النظام')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0"><i class="fas fa-book"></i> دليل نظام الأباسي المحاسبي</h3>
                </div>
                
                <div class="card-body">
                    <!-- Nav Tabs -->
                    <ul class="nav nav-tabs" id="guideTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="guide-tab" data-bs-toggle="tab" 
                                    data-bs-target="#guide" type="button" role="tab">
                                <i class="fas fa-info-circle"></i> دليل النظام
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="changelog-tab" data-bs-toggle="tab" 
                                    data-bs-target="#changelog" type="button" role="tab">
                                <i class="fas fa-history"></i> تحديثات النظام
                            </button>
                        </li>
                    </ul>
                    
                    <!-- Tab Content -->
                    <div class="tab-content mt-4" id="guideTabContent">
                        <!-- دليل النظام -->
                        <div class="tab-pane fade show active" id="guide" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4><i class="fas fa-book-open"></i> دليل النظام</h4>
                                <a href="{{ route('guide.download-pdf') }}" class="btn btn-primary">
                                    <i class="fas fa-download"></i> تحميل PDF
                                </a>
                            </div>
                            
                            <div class="guide-content p-4 bg-light rounded">
                                <div class="markdown-content">
                                    {!! \Illuminate\Support\Str::markdown($guideContent) !!}
                                </div>
                            </div>
                        </div>
                        
                        <!-- تحديثات النظام -->
                        <div class="tab-pane fade" id="changelog" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4><i class="fas fa-list-alt"></i> سجل التحديثات</h4>
                                <a href="{{ route('guide.download-changelog-pdf') }}" class="btn btn-success">
                                    <i class="fas fa-download"></i> تحميل PDF
                                </a>
                            </div>
                            
                            <div class="changelog-content p-4 bg-light rounded">
                                <div class="markdown-content">
                                    {!! \Illuminate\Support\Str::markdown($changelogContent) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .markdown-content {
        font-size: 1rem;
        line-height: 1.8;
    }
    
    .markdown-content h1 {
        font-size: 2rem;
        font-weight: bold;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        color: #2c3e50;
        border-bottom: 2px solid #3498db;
        padding-bottom: 0.5rem;
    }
    
    .markdown-content h2 {
        font-size: 1.5rem;
        font-weight: bold;
        margin-top: 1.5rem;
        margin-bottom: 0.8rem;
        color: #34495e;
    }
    
    .markdown-content h3 {
        font-size: 1.25rem;
        font-weight: bold;
        margin-top: 1rem;
        margin-bottom: 0.6rem;
        color: #7f8c8d;
    }
    
    .markdown-content p {
        margin-bottom: 1rem;
        text-align: justify;
    }
    
    .markdown-content ul, .markdown-content ol {
        margin-bottom: 1rem;
        padding-right: 2rem;
    }
    
    .markdown-content li {
        margin-bottom: 0.5rem;
    }
    
    .markdown-content table {
        width: 100%;
        margin-bottom: 1rem;
        border-collapse: collapse;
    }
    
    .markdown-content table th,
    .markdown-content table td {
        border: 1px solid #ddd;
        padding: 0.75rem;
        text-align: right;
    }
    
    .markdown-content table th {
        background-color: #3498db;
        color: white;
        font-weight: bold;
    }
    
    .markdown-content table tr:nth-child(even) {
        background-color: #f8f9fa;
    }
    
    .markdown-content code {
        background-color: #f4f4f4;
        padding: 0.2rem 0.4rem;
        border-radius: 3px;
        font-family: 'Courier New', monospace;
        font-size: 0.9rem;
    }
    
    .markdown-content pre {
        background-color: #2c3e50;
        color: #ecf0f1;
        padding: 1rem;
        border-radius: 5px;
        overflow-x: auto;
        margin-bottom: 1rem;
    }
    
    .markdown-content pre code {
        background-color: transparent;
        color: inherit;
        padding: 0;
    }
    
    .markdown-content blockquote {
        border-right: 4px solid #3498db;
        padding-right: 1rem;
        margin: 1rem 0;
        color: #7f8c8d;
        font-style: italic;
    }
    
    .markdown-content hr {
        border: none;
        border-top: 2px solid #ecf0f1;
        margin: 2rem 0;
    }
    
    .nav-tabs .nav-link {
        color: #495057;
        font-weight: 500;
    }
    
    .nav-tabs .nav-link.active {
        color: #3498db;
        font-weight: bold;
    }
    
    .guide-content,
    .changelog-content {
        max-height: 70vh;
        overflow-y: auto;
    }
</style>
@endsection
