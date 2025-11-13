<?php
/**
 * الصفحة الرئيسية - توجيه إلى لوحة التحكم
 * Main Page - Redirect to Dashboard
 */

require_once 'includes/functions.php';

// إذا كان المستخدم مسجل دخول، اذهب إلى لوحة التحكم
if (isLoggedIn()) {
    header("Location: dashboard.php");
} else {
    // إذا لم يكن مسجل دخول، اذهب إلى صفحة تسجيل الدخول
    header("Location: login.php");
}
exit;
?>
