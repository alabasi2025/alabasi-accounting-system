<?php
/**
 * اختبار تنفيذ SQL مباشرة
 */

// منع الوصول من غير localhost
if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1') {
    die('Access denied');
}

require_once 'includes/db.php';

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>اختبار SQL</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .success { color: green; padding: 10px; background: #d4edda; margin: 5px 0; border-radius: 5px; }
        .error { color: red; padding: 10px; background: #f8d7da; margin: 5px 0; border-radius: 5px; }
        pre { background: #333; color: #0f0; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>🔧 اختبار تنفيذ SQL مباشرة</h1>
    
    <?php
    if (isset($_POST['execute'])) {
        echo '<h2>📊 نتائج التنفيذ:</h2>';
        
        $sqlFile = __DIR__ . '/install_updates_simple.sql';
        $sql = file_get_contents($sqlFile);
        
        // تنظيف التعليقات
        $lines = explode("\n", $sql);
        $cleanedLines = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || substr($line, 0, 2) === '--') {
                continue;
            }
            $cleanedLines[] = $line;
        }
        $sql = implode(" ", $cleanedLines);
        
        // تقسيم الاستعلامات
        $statements = explode(';', $sql);
        
        $success_count = 0;
        $error_count = 0;
        
        foreach ($statements as $index => $statement) {
            $statement = trim($statement);
            if (empty($statement) || preg_match('/^--/', $statement)) {
                continue;
            }
            
            echo '<div style="border: 1px solid #ddd; padding: 10px; margin: 10px 0; background: white;">';
            echo '<strong>استعلام #' . ($index + 1) . ':</strong><br>';
            echo '<pre style="background: #f8f9fa; color: #333; padding: 10px; font-size: 12px;">' . htmlspecialchars(substr($statement, 0, 200)) . (strlen($statement) > 200 ? '...' : '') . '</pre>';
            
            try {
                $result = $pdo->exec($statement);
                echo '<div class="success">✅ نجح! ';
                if ($result !== false) {
                    echo '(' . $result . ' صف متأثر)';
                }
                echo '</div>';
                $success_count++;
            } catch (PDOException $e) {
                echo '<div class="error">❌ فشل: ' . htmlspecialchars($e->getMessage()) . '</div>';
                $error_count++;
            }
            
            echo '</div>';
        }
        
        echo '<hr>';
        echo '<h3>📈 الإحصائيات:</h3>';
        echo '<div class="success">✅ نجح: ' . $success_count . '</div>';
        echo '<div class="error">❌ فشل: ' . $error_count . '</div>';
        
        // التحقق من الجداول
        echo '<hr>';
        echo '<h3>🔍 التحقق من الجداول:</h3>';
        
        $tables = ['auto_update_settings', 'system_updates', 'update_files_log', 'update_notifications'];
        foreach ($tables as $table) {
            try {
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo '<div class="success">✅ جدول ' . $table . ' موجود (' . $result['count'] . ' صف)</div>';
            } catch (PDOException $e) {
                echo '<div class="error">❌ جدول ' . $table . ' غير موجود</div>';
            }
        }
        
        echo '<hr>';
        echo '<a href="backup-manager.php" class="btn">📦 صفحة التحديثات</a>';
        echo ' <a href="test-github-connection.php" class="btn">🔍 اختبار الاتصال</a>';
        
    } else {
        ?>
        <p>هذه الصفحة ستنفذ ملف <code>install_updates_simple.sql</code> وتعرض نتيجة كل استعلام بالتفصيل.</p>
        <form method="POST">
            <button type="submit" name="execute" class="btn">🚀 تنفيذ SQL الآن</button>
        </form>
        <?php
    }
    ?>
</body>
</html>
