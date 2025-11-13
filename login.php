<?php
/**
 * صفحة تسجيل الدخول - دخول مباشر بدون بيانات
 * Login Page - Direct Login Without Credentials
 */

session_start();

require_once 'includes/db.php';
require_once 'includes/functions.php';

// إذا كان المستخدم مسجل دخول مسبقاً، اذهب إلى لوحة التحكم
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

// تسجيل دخول تلقائي
try {
    // جلب أول مستخدم من قاعدة البيانات
    $stmt = $pdo->query("SELECT * FROM users WHERE isActive = 1 LIMIT 1");
    $user = $stmt->fetch();
    
    if ($user) {
        // تسجيل الدخول تلقائياً
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_name'] = $user['nameAr'];
        $_SESSION['user_role'] = $user['role'];
        
        // تحديث آخر تسجيل دخول
        $updateStmt = $pdo->prepare("UPDATE users SET lastLogin = NOW() WHERE id = ?");
        $updateStmt->execute([$user['id']]);
        
        // الذهاب إلى لوحة التحكم
        header("Location: dashboard.php");
        exit;
    }
} catch (PDOException $e) {
    $error = "خطأ في الاتصال بقاعدة البيانات";
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - نظام العباسي الموحد</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .auto-login-message {
            text-align: center;
            padding: 40px;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h1>نظام العباسي الموحد</h1>
                <p>نظام محاسبي متكامل</p>
            </div>
            
            <div class="auto-login-message">
                <div class="spinner"></div>
                <h3>جاري تسجيل الدخول تلقائياً...</h3>
                <p style="color: #666; margin-top: 10px;">يرجى الانتظار...</p>
            </div>
            
            <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // إعادة تحميل الصفحة بعد ثانية واحدة إذا لم يتم التوجيه
        setTimeout(function() {
            window.location.href = 'dashboard.php';
        }, 1000);
    </script>
</body>
</html>
