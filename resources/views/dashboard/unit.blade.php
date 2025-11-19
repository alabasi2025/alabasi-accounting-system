@extends('layouts.dashboard')

@section('title', 'لوحة التحكم - وحدة أعمال الحديدة')

@section('page-title', 'وحدة أعمال الحديدة')

@section('styles')
<style>
    .welcome-banner {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
    }
    
    .welcome-banner h2 {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .welcome-banner p {
        font-size: 16px;
        opacity: 0.9;
        margin: 0;
    }
</style>
@endsection

@section('content')
<!-- Welcome Banner -->
<div class="welcome-banner">
    <h2><i class="fas fa-building"></i> مرحباً بك في وحدة أعمال الحديدة</h2>
    <p>نظام محاسبي متكامل لإدارة جميع العمليات المالية والمحاسبية</p>
</div>

<div class="alert alert-success">
    <h4>✅ النظام جاهز!</h4>
    <p>جميع التبويبات متاحة في القائمة الجانبية</p>
</div>
@endsection
