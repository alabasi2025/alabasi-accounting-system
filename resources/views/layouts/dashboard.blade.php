<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'نظام الأباسي المحاسبي')</title>
    
    <!-- Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
        }
        
        body {
            background: #f5f7fa;
            overflow-x: hidden;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            right: 0;
            top: 0;
            width: 280px;
            height: 100vh;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            color: white;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: -2px 0 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 3px;
        }
        
        .sidebar-header {
            padding: 25px 20px;
            background: rgba(0,0,0,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h2 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .sidebar-header p {
            font-size: 13px;
            opacity: 0.9;
            margin: 0;
        }
        
        .sidebar-menu {
            padding: 15px 0;
        }
        
        .menu-section {
            margin-bottom: 20px;
        }
        
        .menu-section-title {
            padding: 10px 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            opacity: 0.7;
            letter-spacing: 1px;
        }
        
        .menu-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
            border-right: 3px solid transparent;
        }
        
        .menu-item:hover {
            background: rgba(255,255,255,0.1);
            border-right-color: white;
        }
        
        .menu-item.active {
            background: rgba(255,255,255,0.15);
            border-right-color: white;
        }
        
        .menu-item i {
            width: 25px;
            font-size: 16px;
            margin-left: 12px;
        }
        
        .menu-item span {
            font-size: 14px;
            font-weight: 500;
        }
        
        /* Main Content */
        .main-content {
            margin-right: 280px;
            min-height: 100vh;
        }
        
        /* Top Bar */
        .top-bar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .top-bar-left h1 {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin: 0;
        }
        
        .top-bar-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .unit-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        
        .company-badge {
            background: #28a745;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        
        .logout-btn {
            background: #dc3545;
            color: white;
            padding: 8px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .logout-btn:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
        
        /* Content Area */
        .content-area {
            padding: 30px;
        }
        
        /* Mobile Toggle */
        .mobile-toggle {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1001;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-right: 0;
            }
            
            .mobile-toggle {
                display: block;
            }
        }
        
        @media (max-width: 576px) {
            .top-bar {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .top-bar-right {
                flex-wrap: wrap;
                width: 100%;
            }
            
            .content-area {
                padding: 15px;
            }
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Mobile Toggle -->
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-calculator"></i> نظام الأباسي</h2>
            <p>نظام محاسبي متكامل</p>
        </div>
        
        <div class="sidebar-menu">
            <!-- الرئيسية -->
            <div class="menu-section">
                <div class="menu-section-title">الرئيسية</div>
                <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>لوحة التحكم</span>
                </a>
            </div>
            
            <!-- الإعدادات الأساسية -->
            <div class="menu-section">
                <div class="menu-section-title">الإعدادات الأساسية</div>
                <a href="{{ route('units.index') }}" class="menu-item {{ request()->routeIs('units.*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i>
                    <span>الوحدات</span>
                </a>
                <a href="{{ route('companies.index') }}" class="menu-item {{ request()->routeIs('companies.*') ? 'active' : '' }}">
                    <i class="fas fa-briefcase"></i>
                    <span>المؤسسات</span>
                </a>
                <a href="{{ route('branches.index') }}" class="menu-item {{ request()->routeIs('branches.*') ? 'active' : '' }}">
                    <i class="fas fa-code-branch"></i>
                    <span>الفروع</span>
                </a>
            </div>
            
            <!-- دليل الحسابات -->
            <div class="menu-section">
                <div class="menu-section-title">دليل الحسابات</div>
                <a href="{{ route('accounts.index') }}" class="menu-item {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                    <i class="fas fa-list"></i>
                    <span>دليل الحسابات</span>
                </a>
                <a href="{{ route('account-types.index') }}" class="menu-item {{ request()->routeIs('account-types.*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i>
                    <span>أنواع الحسابات</span>
                </a>
                <a href="{{ route('analytical-accounts.index') }}" class="menu-item {{ request()->routeIs('analytical-accounts.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i>
                    <span>الحسابات التحليلية</span>
                </a>
            </div>
            
            <!-- العمليات المحاسبية -->
            <div class="menu-section">
                <div class="menu-section-title">العمليات المحاسبية</div>
                <a href="{{ route('journal-entries.index') }}" class="menu-item {{ request()->routeIs('journal-entries.*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i>
                    <span>القيود اليومية</span>
                </a>
                <a href="{{ route('vouchers.index') }}" class="menu-item {{ request()->routeIs('vouchers.*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice"></i>
                    <span>السندات</span>
                </a>
                <a href="{{ route('clearing-transactions.index') }}" class="menu-item {{ request()->routeIs('clearing-transactions.*') ? 'active' : '' }}">
                    <i class="fas fa-exchange-alt"></i>
                    <span>التحويلات</span>
                </a>
            </div>
            
            <!-- الأطراف -->
            <div class="menu-section">
                <div class="menu-section-title">الأطراف</div>
                <a href="{{ route('customers.index') }}" class="menu-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>العملاء</span>
                </a>
                <a href="{{ route('suppliers.index') }}" class="menu-item {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                    <i class="fas fa-truck"></i>
                    <span>الموردون</span>
                </a>
                <a href="{{ route('employees.index') }}" class="menu-item {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                    <i class="fas fa-user-tie"></i>
                    <span>الموظفون</span>
                </a>
            </div>
            
            <!-- الخزائن والبنوك -->
            <div class="menu-section">
                <div class="menu-section-title">الخزائن والبنوك</div>
                <a href="{{ route('cashboxes.index') }}" class="menu-item {{ request()->routeIs('cashboxes.*') ? 'active' : '' }}">
                    <i class="fas fa-cash-register"></i>
                    <span>الصناديق</span>
                </a>
                <a href="{{ route('bank-accounts.index') }}" class="menu-item {{ request()->routeIs('bank-accounts.*') ? 'active' : '' }}">
                    <i class="fas fa-university"></i>
                    <span>الحسابات البنكية</span>
                </a>
            </div>
            
            <!-- المساعدة -->
            <div class="menu-section">
                <div class="menu-section-title">المساعدة</div>
                <a href="{{ route('guide.index') }}" class="menu-item {{ request()->routeIs('guide.*') ? 'active' : '' }}">
                    <i class="fas fa-book-open"></i>
                    <span>دليل النظام</span>
                </a>
                <a href="{{ route('manual.index') }}" class="menu-item {{ request()->routeIs('manual.*') ? 'active' : '' }}">
                    <i class="fas fa-question-circle"></i>
                    <span>دليل الاستخدام</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="top-bar-left">
                <h1>@yield('page-title', 'لوحة التحكم')</h1>
            </div>
            <div class="top-bar-right">
                @if(session('unit_id'))
                    <span class="unit-badge">
                        <i class="fas fa-building"></i>
                        {{ session('unit_name') ?? 'الوحدة المركزية' }}
                    </span>
                @endif
                @if(session('company_id'))
                    <span class="company-badge">
                        <i class="fas fa-briefcase"></i>
                        {{ session('company_name') ?? 'مؤسسة' }}
                    </span>
                @endif
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        خروج
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Content Area -->
        <div class="content-area">
            @yield('content')
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.mobile-toggle');
            
            if (window.innerWidth <= 992) {
                if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>
