<?php
/**
 * إصلاح ترميز UTF-8 في قاعدة البيانات
 * Fix UTF-8 Encoding in Database
 */

require_once 'includes/db.php';

echo "<!DOCTYPE html>";
echo "<html lang='ar' dir='rtl'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<title>إصلاح الترميز</title>";
echo "<style>body{font-family:Arial;padding:40px;background:#f5f5f5;}";
echo ".success{color:green;padding:10px;background:#d4edda;margin:10px 0;border-radius:5px;}";
echo ".error{color:red;padding:10px;background:#f8d7da;margin:10px 0;border-radius:5px;}";
echo "</style></head><body>";

echo "<h1>🔧 إصلاح ترميز UTF-8</h1>";

try {
    // تعيين ترميز الاتصال
    $pdo->exec("SET NAMES utf8mb4");
    $pdo->exec("SET CHARACTER SET utf8mb4");
    $pdo->exec("SET character_set_connection=utf8mb4");
    
    echo "<div class='success'>✅ تم تعيين ترميز الاتصال إلى UTF-8</div>";
    
    // تحديث ترميز قاعدة البيانات
    $pdo->exec("ALTER DATABASE alabasi_unified CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci");
    echo "<div class='success'>✅ تم تحديث ترميز قاعدة البيانات</div>";
    
    // تحديث ترميز الجداول
    $tables = ['users', 'branches', 'accounts', 'analyticalAccounts', 'journals', 'journalEntries'];
    
    foreach ($tables as $table) {
        $pdo->exec("ALTER TABLE `$table` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "<div class='success'>✅ تم تحديث ترميز جدول: $table</div>";
    }
    
    echo "<div class='success'><h2>🎉 تم إصلاح الترميز بنجاح!</h2></div>";
    echo "<p><a href='dashboard.php' style='padding:10px 20px;background:#667eea;color:white;text-decoration:none;border-radius:5px;'>العودة للنظام</a></p>";
    
    // حذف هذا الملف بعد التنفيذ
    echo "<p style='color:#999;font-size:12px;'>يمكنك حذف ملف fix-encoding.php الآن</p>";
    
} catch (PDOException $e) {
    echo "<div class='error'>❌ خطأ: " . $e->getMessage() . "</div>";
}

echo "</body></html>";
?>
