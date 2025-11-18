<?php
session_start();

// مسح جميع بيانات الجلسة
$_SESSION = array();

// حذف ملف cookie الخاص بالجلسة
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// تدمير الجلسة
session_destroy();

// الانتقال إلى صفحة تسجيل الدخول
header('Location: login.php');
exit;
