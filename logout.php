<?php
/**
 * تسجيل الخروج
 * Logout
 */

session_start();
session_destroy();

header("Location: login.php");
exit;
?>
