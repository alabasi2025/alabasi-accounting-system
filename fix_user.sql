-- إصلاح بيانات المستخدم
-- تغيير اسم المستخدم إلى root وحذف كلمة السر

UPDATE users SET 
    username = 'root',
    password = '',
    nameAr = 'الجذر',
    nameEn = 'Root',
    email = 'root@alabasi.com',
    isActive = TRUE
WHERE id = 1;
