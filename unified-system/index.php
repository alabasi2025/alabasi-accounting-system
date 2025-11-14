<?php
define('ALABASI_SYSTEM', true);
require_once 'config/config.php';
require_once 'includes/database.php';

// تسجيل دخول تلقائي للاختبار
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['user_role'] = 'admin';

// إعادة التوجيه إلى صفحة الأصناف
header('Location: modules/inventory/items.php');
exit;
?>
