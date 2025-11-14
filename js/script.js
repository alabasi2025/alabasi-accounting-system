
// القائمة الجانبية اليسرى (دليل البناء)
function toggleLeftSidebar() {
    const leftSidebar = document.getElementById('leftSidebar');
    const isActive = leftSidebar.classList.contains('active');
    
    if (!isActive) {
        // فتح القائمة وتحميل المحتوى
        leftSidebar.classList.add('active');
        loadBuildGuideContent();
    } else {
        // إغلاق القائمة
        leftSidebar.classList.remove('active');
    }
}

// تحميل محتوى دليل البناء
function loadBuildGuideContent() {
    const contentDiv = document.getElementById('leftSidebarContent');
    
    // عرض رسالة التحميل
    contentDiv.innerHTML = '<p style="text-align: center; color: #6c757d; padding: 20px;">🔄 جاري التحميل...</p>';
    
    // جلب البيانات من API
    fetch('api/build-guide-summary.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                contentDiv.innerHTML = renderBuildGuide(data);
            } else {
                contentDiv.innerHTML = '<p style="text-align: center; color: #dc3545; padding: 20px;">❌ خطأ في التحميل</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            contentDiv.innerHTML = '<p style="text-align: center; color: #dc3545; padding: 20px;">❌ خطأ في الاتصال</p>';
        });
}

// عرض محتوى دليل البناء
function renderBuildGuide(data) {
    let html = '';
    
    // الإحصائيات
    html += `
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; text-align: center;">
                <div>
                    <div style="font-size: 24px; font-weight: bold; color: #667eea;">${data.stats.completed}</div>
                    <div style="font-size: 12px; color: #6c757d;">✅ منجز</div>
                </div>
                <div>
                    <div style="font-size: 24px; font-weight: bold; color: #6c757d;">${data.stats.pending}</div>
                    <div style="font-size: 12px; color: #6c757d;">⏳ متبقي</div>
                </div>
            </div>
            <div style="margin-top: 10px;">
                <div style="background: #e9ecef; height: 20px; border-radius: 10px; overflow: hidden;">
                    <div style="background: linear-gradient(90deg, #667eea 0%, #764ba2 100%); height: 100%; width: ${data.stats.progress}%; transition: width 0.3s;"></div>
                </div>
                <div style="text-align: center; margin-top: 5px; font-size: 12px; color: #6c757d;">${data.stats.progress}% مكتمل</div>
            </div>
        </div>
    `;
    
    // قائمة المهام حسب الفئة
    for (const category in data.tasks) {
        const tasks = data.tasks[category];
        const completed = tasks.filter(t => t.isCompleted).length;
        
        html += `
            <div style="margin-bottom: 15px;">
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 10px; border-radius: 5px; font-weight: bold; font-size: 14px; display: flex; justify-content: space-between;">
                    <span>${category}</span>
                    <span>${completed}/${tasks.length}</span>
                </div>
                <div style="padding: 10px; background: #f8f9fa; border-radius: 0 0 5px 5px;">
        `;
        
        tasks.forEach(task => {
            const checkIcon = task.isCompleted ? '✅' : '⬜';
            const textStyle = task.isCompleted ? 'text-decoration: line-through; color: #6c757d;' : 'color: #495057;';
            
            html += `
                <div style="padding: 5px 0; font-size: 13px; ${textStyle}">
                    ${checkIcon} ${task.taskName}
                </div>
            `;
        });
        
        html += `
                </div>
            </div>
        `;
    }
    
    // رابط لفتح الصفحة الكاملة
    html += `
        <div style="text-align: center; margin-top: 20px;">
            <a href="build-guide.php" style="display: inline-block; background: #667eea; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold;">
                📋 عرض التفاصيل الكاملة
            </a>
        </div>
    `;
    
    return html;
}
