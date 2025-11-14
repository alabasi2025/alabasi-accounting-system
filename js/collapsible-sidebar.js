/**
 * القائمة الجانبية القابلة للطي
 * Collapsible Sidebar with Submenus
 */

// تهيئة عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    initSidebar();
});

/**
 * تهيئة القائمة الجانبية
 */
function initSidebar() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.getElementById('mainContent');
    
    // استرجاع حالة القائمة من localStorage
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed) {
        sidebar.classList.add('collapsed');
    }
    
    // زر toggle
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            
            // حفظ الحالة
            const collapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', collapsed);
        });
    }
    
    // فتح القائمة الفرعية النشطة تلقائياً
    const activeItem = document.querySelector('.submenu-item.active');
    if (activeItem) {
        const submenu = activeItem.closest('.submenu');
        const menuSection = submenu ? submenu.previousElementSibling : null;
        if (submenu && menuSection) {
            submenu.classList.add('open');
            menuSection.classList.add('active');
        }
    }
}

/**
 * تبديل القائمة الفرعية
 */
function toggleSubmenu(element) {
    const sidebar = document.getElementById('sidebar');
    
    // إذا كانت القائمة مطوية، لا تفتح القوائم الفرعية
    if (sidebar.classList.contains('collapsed')) {
        return;
    }
    
    const submenu = element.nextElementSibling;
    const isOpen = submenu.classList.contains('open');
    
    // إغلاق جميع القوائم الفرعية الأخرى
    const allSubmenus = document.querySelectorAll('.submenu');
    const allSections = document.querySelectorAll('.menu-section');
    
    allSubmenus.forEach(s => s.classList.remove('open'));
    allSections.forEach(s => s.classList.remove('active'));
    
    // فتح/إغلاق القائمة الحالية
    if (!isOpen) {
        submenu.classList.add('open');
        element.classList.add('active');
    }
}

/**
 * إغلاق القائمة الجانبية (للشاشات الصغيرة)
 */
function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (window.innerWidth <= 768) {
        sidebar.classList.remove('open');
    }
}

// إغلاق القائمة عند النقر على رابط (للشاشات الصغيرة)
document.addEventListener('click', function(e) {
    if (e.target.closest('.menu-item') && !e.target.closest('.menu-section')) {
        closeSidebar();
    }
});

// التعامل مع تغيير حجم الشاشة
window.addEventListener('resize', function() {
    const sidebar = document.getElementById('sidebar');
    if (window.innerWidth > 768) {
        sidebar.classList.remove('open');
    }
});
