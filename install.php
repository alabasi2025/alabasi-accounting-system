<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تثبيت نظام الأباسي المحاسبي</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .install-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 600px;
            width: 100%;
        }
        .step {
            display: none;
        }
        .step.active {
            display: block;
        }
        .progress-bar {
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="install-card p-5">
            <h2 class="text-center mb-4">🏛️ تثبيت نظام الأباسي المحاسبي</h2>
            
            <!-- شريط التقدم -->
            <div class="progress mb-4">
                <div class="progress-bar" role="progressbar" style="width: 25%" id="progressBar"></div>
            </div>

            <?php
            session_start();
            
            // الخطوة 1: فحص المتطلبات
            if (!isset($_POST['step']) || $_POST['step'] == '1') {
                $phpVersion = phpversion();
                $phpOk = version_compare($phpVersion, '7.4.0', '>=');
                $pdoOk = extension_loaded('pdo_mysql');
                $mbstringOk = extension_loaded('mbstring');
                $curlOk = extension_loaded('curl');
                
                echo '<div class="step active">';
                echo '<h4>الخطوة 1: فحص المتطلبات</h4>';
                echo '<ul class="list-group">';
                echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                echo 'إصدار PHP (7.4+)';
                echo '<span class="badge ' . ($phpOk ? 'bg-success' : 'bg-danger') . '">' . $phpVersion . '</span>';
                echo '</li>';
                echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                echo 'PDO MySQL';
                echo '<span class="badge ' . ($pdoOk ? 'bg-success' : 'bg-danger') . '">' . ($pdoOk ? 'متوفر' : 'غير متوفر') . '</span>';
                echo '</li>';
                echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                echo 'mbstring';
                echo '<span class="badge ' . ($mbstringOk ? 'bg-success' : 'bg-danger') . '">' . ($mbstringOk ? 'متوفر' : 'غير متوفر') . '</span>';
                echo '</li>';
                echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                echo 'cURL';
                echo '<span class="badge ' . ($curlOk ? 'bg-success' : 'bg-danger') . '">' . ($curlOk ? 'متوفر' : 'غير متوفر') . '</span>';
                echo '</li>';
                echo '</ul>';
                
                if ($phpOk && $pdoOk && $mbstringOk && $curlOk) {
                    echo '<form method="post" class="mt-4">';
                    echo '<input type="hidden" name="step" value="2">';
                    echo '<button type="submit" class="btn btn-primary w-100">التالي →</button>';
                    echo '</form>';
                } else {
                    echo '<div class="alert alert-danger mt-4">يرجى تثبيت المتطلبات الناقصة قبل المتابعة</div>';
                }
                echo '</div>';
            }
            
            // الخطوة 2: إعدادات قاعدة البيانات
            elseif ($_POST['step'] == '2') {
                echo '<div class="step active">';
                echo '<h4>الخطوة 2: إعدادات قاعدة البيانات</h4>';
                echo '<form method="post" class="mt-4">';
                echo '<input type="hidden" name="step" value="3">';
                echo '<div class="mb-3">';
                echo '<label class="form-label">عنوان الخادم</label>';
                echo '<input type="text" name="db_host" class="form-control" value="localhost" required>';
                echo '</div>';
                echo '<div class="mb-3">';
                echo '<label class="form-label">اسم قاعدة البيانات</label>';
                echo '<input type="text" name="db_name" class="form-control" value="alabasi_unified" required>';
                echo '</div>';
                echo '<div class="mb-3">';
                echo '<label class="form-label">اسم المستخدم</label>';
                echo '<input type="text" name="db_user" class="form-control" required>';
                echo '</div>';
                echo '<div class="mb-3">';
                echo '<label class="form-label">كلمة المرور</label>';
                echo '<input type="password" name="db_pass" class="form-control">';
                echo '</div>';
                echo '<button type="submit" class="btn btn-primary w-100">التالي →</button>';
                echo '</form>';
                echo '</div>';
                echo '<script>document.getElementById("progressBar").style.width = "50%";</script>';
            }
            
            // الخطوة 3: اختبار الاتصال والتثبيت
            elseif ($_POST['step'] == '3') {
                $dbHost = $_POST['db_host'];
                $dbName = $_POST['db_name'];
                $dbUser = $_POST['db_user'];
                $dbPass = $_POST['db_pass'];
                
                echo '<div class="step active">';
                echo '<h4>الخطوة 3: التثبيت</h4>';
                
                try {
                    // اختبار الاتصال
                    $pdo = new PDO("mysql:host=$dbHost", $dbUser, $dbPass);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    echo '<div class="alert alert-success">✓ تم الاتصال بقاعدة البيانات بنجاح</div>';
                    
                    // إنشاء قاعدة البيانات
                    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    echo '<div class="alert alert-success">✓ تم إنشاء قاعدة البيانات</div>';
                    
                    // الاتصال بقاعدة البيانات
                    $pdo->exec("USE `$dbName`");
                    
                    // استيراد ملف SQL
                    if (file_exists('database.sql')) {
                        $sql = file_get_contents('database.sql');
                        $pdo->exec($sql);
                        echo '<div class="alert alert-success">✓ تم استيراد البيانات</div>';
                    }
                    
                    // إنشاء ملف الإعدادات
                    $configContent = "<?php\n";
                    $configContent .= "define('DB_HOST', '$dbHost');\n";
                    $configContent .= "define('DB_NAME', '$dbName');\n";
                    $configContent .= "define('DB_USER', '$dbUser');\n";
                    $configContent .= "define('DB_PASS', '$dbPass');\n";
                    $configContent .= "define('DB_CHARSET', 'utf8mb4');\n";
                    $configContent .= "?>";
                    
                    file_put_contents('includes/db.php', $configContent);
                    echo '<div class="alert alert-success">✓ تم حفظ إعدادات الاتصال</div>';
                    
                    echo '<div class="alert alert-info mt-4">';
                    echo '<h5>تم التثبيت بنجاح! 🎉</h5>';
                    echo '<p><strong>بيانات الدخول الافتراضية:</strong></p>';
                    echo '<p>اسم المستخدم: <code>admin</code></p>';
                    echo '<p>كلمة المرور: <code>admin123</code></p>';
                    echo '<p class="text-danger">⚠️ يرجى تغيير كلمة المرور بعد أول تسجيل دخول!</p>';
                    echo '</div>';
                    
                    echo '<a href="login.php" class="btn btn-success w-100 mt-3">الذهاب لتسجيل الدخول</a>';
                    
                    // حذف ملف التثبيت
                    echo '<div class="alert alert-warning mt-3">';
                    echo '<small>⚠️ يُنصح بحذف ملف install.php بعد التثبيت لأسباب أمنية</small>';
                    echo '</div>';
                    
                } catch (PDOException $e) {
                    echo '<div class="alert alert-danger">خطأ: ' . $e->getMessage() . '</div>';
                    echo '<a href="install.php" class="btn btn-secondary w-100 mt-3">العودة</a>';
                }
                
                echo '</div>';
                echo '<script>document.getElementById("progressBar").style.width = "100%";</script>';
            }
            ?>
        </div>
    </div>
</body>
</html>
