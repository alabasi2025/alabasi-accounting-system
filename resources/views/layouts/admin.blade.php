<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'الوحدة المركزية') - نظام الأباسي</title>
    
    <!-- Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #06b6d4;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .admin-sidebar {
            width: 280px;
            background: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, #1e40af 100%);
            color: white;
            text-align: center;
        }

        .sidebar-header h3 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .sidebar-header p {
            margin: 0.5rem 0 0 0;
            font-size: 0.875rem;
            opacity: 0.9;
        }

        .sidebar-menu {
            padding: 1rem 0;
        }

        .menu-section {
            margin-bottom: 1.5rem;
        }

        .menu-section-title {
            padding: 0.5rem 1.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--secondary-color);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: var(--dark-color);
            text-decoration: none;
            transition: all 0.3s ease;
            border-right: 3px solid transparent;
        }

        .menu-item:hover {
            background: var(--light-color);
            color: var(--primary-color);
            border-right-color: var(--primary-color);
        }

        .menu-item.active {
            background: var(--light-color);
            color: var(--primary-color);
            border-right-color: var(--primary-color);
            font-weight: 600;
        }

        .menu-item i {
            width: 24px;
            margin-left: 0.75rem;
            font-size: 1.1rem;
        }

        .menu-item .badge {
            margin-right: auto;
        }

        /* Main Content */
        .admin-content {
            margin-right: 280px;
            flex: 1;
            padding: 2rem;
        }

        .content-header {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .content-header h1 {
            margin: 0;
            font-size: 1.75rem;
            color: var(--dark-color);
        }

        .content-header .breadcrumb {
            margin: 0.5rem 0 0 0;
            background: none;
            padding: 0;
        }

        /* Cards */
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        .stat-card .icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-card.primary .icon {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-color);
        }

        .stat-card.success .icon {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .stat-card.warning .icon {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }

        .stat-card.danger .icon {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }

        .stat-card h3 {
            font-size: 2rem;
            font-weight: bold;
            margin: 0;
            color: var(--dark-color);
        }

        .stat-card p {
            margin: 0.5rem 0 0 0;
            color: var(--secondary-color);
        }

        /* Feature Card */
        .feature-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateX(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        .feature-card .feature-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-left: 1rem;
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-color);
        }

        .feature-card .feature-info {
            flex: 1;
        }

        .feature-card h5 {
            margin: 0;
            font-size: 1.1rem;
            color: var(--dark-color);
        }

        .feature-card p {
            margin: 0.25rem 0 0 0;
            font-size: 0.875rem;
            color: var(--secondary-color);
        }

        .feature-card .feature-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-badge.active {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .status-badge.pending {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(100%);
            }

            .admin-sidebar.show {
                transform: translateX(0);
            }

            .admin-content {
                margin-right: 0;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-crown"></i> الوحدة المركزية</h3>
                <p>نظام الأباسي المحاسبي</p>
            </div>

            <nav class="sidebar-menu">
                <!-- Dashboard -->
                <div class="menu-section">
                    <div class="menu-section-title">لوحة التحكم</div>
                    <a href="{{ route('admin.dashboard') }}" class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i>
                        <span>الرئيسية</span>
                    </a>
                </div>

                <!-- Laravel Tools -->
                <div class="menu-section">
                    <div class="menu-section-title">أدوات Laravel</div>
                    
                    <a href="/telescope" class="menu-item" target="_blank">
                        <i class="fas fa-telescope"></i>
                        <span>Telescope</span>
                        <span class="badge bg-success">نشط</span>
                    </a>
                    
                    <a href="{{ route('admin.pint.index') }}" class="menu-item {{ request()->routeIs('admin.pint.*') ? 'active' : '' }}">
                        <i class="fas fa-code"></i>
                        <span>Pint - جودة الكود</span>
                    </a>
                    
                    <a href="{{ route('admin.pennant.index') }}" class="menu-item {{ request()->routeIs('admin.pennant.*') ? 'active' : '' }}">
                        <i class="fas fa-flag"></i>
                        <span>Pennant - الميزات</span>
                    </a>
                    
                    <a href="{{ route('admin.auth.index') }}" class="menu-item {{ request()->routeIs('admin.auth.*') ? 'active' : '' }}">
                        <i class="fas fa-shield-alt"></i>
                        <span>Sanctum - المصادقة</span>
                    </a>
                    
                    <a href="{{ route('admin.components.index') }}" class="menu-item {{ request()->routeIs('admin.components.*') ? 'active' : '' }}">
                        <i class="fas fa-bolt"></i>
                        <span>Livewire - المكونات</span>
                    </a>
                </div>

                <!-- System Management -->
                <div class="menu-section">
                    <div class="menu-section-title">إدارة النظام</div>
                    
                    <a href="{{ route('admin.cache') }}" class="menu-item {{ request()->routeIs('admin.cache') ? 'active' : '' }}">
                        <i class="fas fa-memory"></i>
                        <span>الذاكرة المؤقتة</span>
                    </a>
                    
                    <a href="{{ route('admin.database') }}" class="menu-item {{ request()->routeIs('admin.database') ? 'active' : '' }}">
                        <i class="fas fa-database"></i>
                        <span>قواعد البيانات</span>
                    </a>
                    
                    <a href="{{ route('admin.scheduler') }}" class="menu-item {{ request()->routeIs('admin.scheduler') ? 'active' : '' }}">
                        <i class="fas fa-clock"></i>
                        <span>جدولة المهام</span>
                    </a>
                    
                    <a href="{{ route('admin.queues.index') }}" class="menu-item {{ request()->routeIs('admin.queues.*') ? 'active' : '' }}">
                        <i class="fas fa-tasks"></i>
                        <span>قوائم الانتظار</span>
                    </a>
                    
                    <a href="{{ route('admin.storage') }}" class="menu-item {{ request()->routeIs('admin.storage') ? 'active' : '' }}">
                        <i class="fas fa-folder"></i>
                        <span>التخزين</span>
                    </a>
                </div>

                <!-- Development -->
                <div class="menu-section">
                    <div class="menu-section-title">التطوير</div>
                    
                    <a href="{{ route('admin.api') }}" class="menu-item {{ request()->routeIs('admin.api') ? 'active' : '' }}">
                        <i class="fas fa-plug"></i>
                        <span>API</span>
                    </a>
                    
                    <a href="{{ route('admin.events.index') }}" class="menu-item {{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
                        <i class="fas fa-bell"></i>
                        <span>الأحداث</span>
                    </a>
                    
                    <a href="{{ route('admin.testing.index') }}" class="menu-item {{ request()->routeIs('admin.testing.*') ? 'active' : '' }}">
                        <i class="fas fa-vial"></i>
                        <span>الاختبارات</span>
                    </a>
                    
                    <a href="{{ route('admin.middleware.index') }}" class="menu-item {{ request()->routeIs('admin.middleware.*') ? 'active' : '' }}">
                        <i class="fas fa-filter"></i>
                        <span>Middleware</span>
                    </a>
                </div>

                <!-- Settings -->
                <div class="menu-section">
                    <div class="menu-section-title">الإعدادات</div>
                    
                    <a href="{{ route('admin.localization.index') }}" class="menu-item {{ request()->routeIs('admin.localization.*') ? 'active' : '' }}">
                        <i class="fas fa-language"></i>
                        <span>اللغات</span>
                    </a>
                    
                    <a href="{{ route('admin.notifications') }}" class="menu-item {{ request()->routeIs('admin.notifications') ? 'active' : '' }}">
                        <i class="fas fa-envelope"></i>
                        <span>الإشعارات</span>
                    </a>
                    
                    <a href="{{ route('admin.deployment.index') }}" class="menu-item {{ request()->routeIs('admin.deployment.*') ? 'active' : '' }}">
                        <i class="fas fa-rocket"></i>
                        <span>النشر</span>
                    </a>
                </div>

                <!-- Back to Main -->
                <div class="menu-section">
                    <a href="{{ route('dashboard') }}" class="menu-item">
                        <i class="fas fa-arrow-right"></i>
                        <span>العودة للنظام</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-content">
            <div class="content-header">
                <h1>@yield('page-title', 'لوحة التحكم')</h1>
                @hasSection('breadcrumb')
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            @yield('breadcrumb')
                        </ol>
                    </nav>
                @endif
            </div>

            @yield('content')
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (optional) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    @stack('scripts')
</body>
</html>
