        </div>
        <!-- نهاية محتوى الصفحة -->
        
        <footer class="footer">
            <p>&copy; 2025 نظام العباسي الموحد - جميع الحقوق محفوظة</p>
        </footer>
    </div>
    <!-- نهاية المحتوى الرئيسي -->
    
    <script>
    // Toggle Sidebar - فتح وإغلاق القائمة الجانبية
    function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        
        if (sidebar && mainContent) {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            
            // حفظ الحالة في localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        }
    }
    
    // استرجاع حالة القائمة عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        
        // للشاشات الصغيرة، أغلق القائمة تلقائياً
        if (window.innerWidth < 768) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
        } else {
            // للشاشات الكبيرة، استرجع الحالة المحفوظة
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
            }
        }
    });
    
    // تحديث عند تغيير حجم الشاشة
    window.addEventListener('resize', function() {
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        
        if (window.innerWidth < 768) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
        }
    });
    </script>
</body>
</html>
