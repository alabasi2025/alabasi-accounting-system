<?php
/**
 * الدوال المساعدة
 * Helper Functions
 */

// بدء الجلسة إذا لم تكن قد بدأت
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * التحقق من تسجيل الدخول
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * الحصول على معرف المستخدم الحالي
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * الحصول على اسم المستخدم الحالي
 */
function getCurrentUserName() {
    return $_SESSION['user_name'] ?? 'مستخدم';
}

/**
 * إعادة التوجيه
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * تنظيف المدخلات
 */
function clean($data) {
    if (is_array($data)) {
        return array_map('clean', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * عرض رسالة
 */
function setMessage($message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

/**
 * الحصول على الرسالة
 */
function getMessage() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $type = $_SESSION['message_type'] ?? 'success';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

/**
 * تنسيق التاريخ
 */
function formatDate($date) {
    if (empty($date)) return '';
    return date('Y-m-d', strtotime($date));
}

/**
 * تنسيق التاريخ والوقت
 */
function formatDateTime($datetime) {
    if (empty($datetime)) return '';
    return date('Y-m-d H:i', strtotime($datetime));
}

/**
 * تنسيق الأرقام
 */
function formatNumber($number, $decimals = 2) {
    return number_format($number, $decimals, '.', ',');
}

/**
 * حماية الصفحات - يتطلب تسجيل الدخول
 */
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}
?>
