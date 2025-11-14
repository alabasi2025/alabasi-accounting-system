<?php
// منع الوصول المباشر
if (!defined('ALABASI_SYSTEM')) {
    die('Access denied');
}

// إعدادات قاعدة البيانات
define('DB_HOST', 'localhost');
define('DB_NAME', 'alabasi_unified_test');
define('DB_USER', 'root');
define('DB_PASS', '');

// إعدادات النظام
define('SITE_NAME', 'نظام الأباسي المحاسبي الموحد');
define('SITE_URL', 'http://localhost:8080');
define('TIMEZONE', 'Asia/Riyadh');

// إعدادات الجلسة
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

// المنطقة الزمنية
date_default_timezone_set(TIMEZONE);

// عرض الأخطاء للتطوير
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
