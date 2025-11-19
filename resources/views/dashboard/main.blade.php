@extends('layouts.dashboard')

@section('title', 'لوحة التحكم - القاعدة المركزية')

@section('page-title', 'لوحة التحكم')

@section('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.3s;
        border-right: 4px solid;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    
    .stat-card.primary {
        border-right-color: #667eea;
    }
    
    .stat-card.success {
        border-right-color: #28a745;
    }
    
    .stat-card.info {
        border-right-color: #17a2b8;
    }
    
    .stat-card.warning {
        border-right-color: #ffc107;
    }
    
    .stat-card.danger {
        border-right-color: #dc3545;
    }
    
    .stat-card h3 {
        color: #666;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .stat-card .number {
        font-size: 32px;
        font-weight: 700;
        color: #333;
        margin-bottom: 5px;
    }
    
    .stat-card .label {
        color: #999;
        font-size: 13px;
    }
    
    .quick-actions {
        background: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 30px;
    }
    
    .quick-actions h2 {
        font-size: 20px;
        font-weight: 700;
        color: #333;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .action-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }
    
    .action-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 15px 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        text-decoration: none;
        border-radius: 10px;
        transition: all 0.3s;
        font-weight: 600;
    }
    
    .action-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    .action-btn i {
        font-size: 20px;
    }
    
    .recent-activity {
        background: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .recent-activity h2 {
        font-size: 20px;
        font-weight: 700;
        color: #333;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .activity-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .activity-item {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
    
    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: white;
    }
    
    .activity-icon.primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .activity-icon.success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }
    
    .activity-icon.info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    }
    
    .activity-content {
        flex: 1;
    }
    
    .activity-content h4 {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }
    
    .activity-content p {
        font-size: 13px;
        color: #999;
        margin: 0;
    }
</style>
@endsection

@section('content')
<!-- الإحصائيات الرئيسية -->
<div class="stats-grid">
    <div class="stat-card primary">
        <h3><i class="fas fa-building"></i> إجمالي الوحدات</h3>
        <div class="number">{{ $total_units ?? 0 }}</div>
        <div class="label">وحدة نشطة</div>
    </div>
    
    <div class="stat-card success">
        <h3><i class="fas fa-briefcase"></i> إجمالي المؤسسات</h3>
        <div class="number">{{ $total_companies ?? 0 }}</div>
        <div class="label">مؤسسة مسجلة</div>
    </div>
    
    <div class="stat-card info">
        <h3><i class="fas fa-exchange-alt"></i> إجمالي التحويلات</h3>
        <div class="number">{{ $total_transactions ?? 0 }}</div>
        <div class="label">تحويل</div>
    </div>
    
    <div class="stat-card warning">
        <h3><i class="fas fa-clock"></i> التحويلات المعلقة</h3>
        <div class="number">{{ $pending_transactions ?? 0 }}</div>
        <div class="label">تحويل معلق</div>
    </div>
    
    <div class="stat-card success">
        <h3><i class="fas fa-check-circle"></i> التحويلات المكتملة</h3>
        <div class="number">{{ $completed_transactions ?? 0 }}</div>
        <div class="label">تحويل مكتمل</div>
    </div>
    
    <div class="stat-card danger">
        <h3><i class="fas fa-dollar-sign"></i> إجمالي المبالغ</h3>
        <div class="number">{{ number_format($total_amount ?? 0) }}</div>
        <div class="label">ريال يمني</div>
    </div>
</div>

<!-- الإجراءات السريعة -->
<div class="quick-actions">
    <h2><i class="fas fa-bolt"></i> الإجراءات السريعة</h2>
    <div class="action-buttons">
        <a href="{{ route('clearing-transactions.create') }}" class="action-btn">
            <i class="fas fa-plus-circle"></i>
            <span>تحويل جديد</span>
        </a>
        <a href="{{ route('journal-entries.create') }}" class="action-btn">
            <i class="fas fa-book"></i>
            <span>قيد يومي جديد</span>
        </a>
        <a href="{{ route('vouchers.create') }}" class="action-btn">
            <i class="fas fa-file-invoice"></i>
            <span>سند جديد</span>
        </a>
        <a href="{{ route('customers.create') }}" class="action-btn">
            <i class="fas fa-user-plus"></i>
            <span>عميل جديد</span>
        </a>
    </div>
</div>

<!-- النشاط الأخير -->
<div class="recent-activity">
    <h2><i class="fas fa-history"></i> النشاط الأخير</h2>
    <ul class="activity-list">
        <li class="activity-item">
            <div class="activity-icon primary">
                <i class="fas fa-user"></i>
            </div>
            <div class="activity-content">
                <h4>تسجيل دخول جديد</h4>
                <p>{{ auth()->user()->email ?? 'مستخدم' }} - منذ دقائق</p>
            </div>
        </li>
        <li class="activity-item">
            <div class="activity-icon success">
                <i class="fas fa-check"></i>
            </div>
            <div class="activity-content">
                <h4>تم إنشاء النظام بنجاح</h4>
                <p>جميع الجداول والبيانات جاهزة</p>
            </div>
        </li>
        <li class="activity-item">
            <div class="activity-icon info">
                <i class="fas fa-database"></i>
            </div>
            <div class="activity-content">
                <h4>قاعدة البيانات محدثة</h4>
                <p>آخر تحديث: {{ date('Y-m-d H:i') }}</p>
            </div>
        </li>
    </ul>
</div>
@endsection

@section('scripts')
<script>
    console.log('Dashboard loaded successfully!');
</script>
@endsection
