<?php
/**
 * ملف الاتصال بقاعدة البيانات
 * Database Connection File
 */

// إعدادات قاعدة البيانات
define('DB_HOST', 'gateway02.us-east-1.prod.aws.tidbcloud.com');
define('DB_PORT', '4000');
define('DB_NAME', 'A4DdztQ8bpbgLe3RMyTHSs');
define('DB_USER', '6MJuaMCAQ7CCKTD.859d494ff864');
define('DB_PASS', 'D7CoXsNip5cUvNT10z38');
define('DB_CHARSET', 'utf8mb4');

// الاتصال بقاعدة البيانات
try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
        PDO::MYSQL_ATTR_SSL_CA => true
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    // تعطيل ONLY_FULL_GROUP_BY
    $pdo->exec("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
    
} catch (PDOException $e) {
    die("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
}
?>
