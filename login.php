<?php
/**
 * صفحة تسجيل الدخول - دخول مباشر بدون كلمة سر
 * Login Page - Direct Login Without Password
 * Version: 2.0 - Fixed for root user
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
    // جلب أول مستخدم admin
    $stmt = $pdo->query("SELECT * FROM users WHERE role = 'admin' ORDER BY id ASC LIMIT 1");
    $user = $stmt->fetch();
    
    if ($user) {
        // تسجيل الدخول تلقائياً
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['email'] ?? 'admin';
        $_SESSION['user_name'] = $user['name'] ?? 'admin';
        $_SESSION['user_role'] = $user['role'];
        
        // تحديث آخر تسجيل دخول
        $updateStmt = $pdo->prepare("UPDATE users SET lastSignedIn = NOW() WHERE id = ?");
        $updateStmt->execute([$user['id']]);
        
        // الذهاب إلى لوحة التحكم
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "لا يوجد مستخدمين نشطين في النظام";
    }
} catch (PDOException $e) {
    $error = "خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - نظام الأباسي الموحد</title>
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
        .error-box {
            background: #fee;
            border: 2px solid #c33;
            color: #c33;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h1>🏛️ نظام الأباسي الموحد</h1>
                <p>نظام محاسبي متكامل</p>
            </div>
            
            <?php if (!isset($error)): ?>
            <div class="auto-login-message">
                <div class="spinner"></div>
                <h3>جاري تسجيل الدخول تلقائياً...</h3>
                <p style="color: #666; margin-top: 10px;">يرجى الانتظار...</p>
            </div>
            <?php else: ?>
            <div class="error-box">
                <h3>⚠️ خطأ</h3>
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // إعادة تحميل الصفحة بعد ثانية واحدة إذا لم يتم التوجيه
        setTimeout(function() {
            if (!<?php echo isset($error) ? 'true' : 'false'; ?>) {
                window.location.href = 'dashboard.php';
            }
        }, 1000);
    </script>
</body>
</html>
