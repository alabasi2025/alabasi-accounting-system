<?php
/**
 * إدارة الجلسات
 * Session Management
 */

// بدء الجلسة
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * التحقق من تسجيل الدخول
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * الحصول على معلومات المستخدم الحالي
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'name' => $_SESSION['name'] ?? null,
        'role' => $_SESSION['role'] ?? 'user'
    ];
}

/**
 * تسجيل دخول مستخدم
 */
function loginUser($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['name'] = $user['nameAr'] ?? $user['username'];
    $_SESSION['role'] = $user['role'] ?? 'user';
}

/**
 * تسجيل خروج
 */
function logoutUser() {
    session_unset();
    session_destroy();
}

// تسجيل دخول تلقائي للاختبار
if (!isLoggedIn()) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'admin';
    $_SESSION['name'] = 'المدير';
    $_SESSION['role'] = 'admin';
}
?>
