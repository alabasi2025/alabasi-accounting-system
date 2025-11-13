<?php
/**
 * Reset Admin Password
 * This file will reset the admin password to: admin123
 * 
 * Instructions:
 * 1. Copy this file to: D:\AAAAAA\xampp\htdocs\alabasi\
 * 2. Open in browser: http://localhost/alabasi/reset-password.php
 * 3. Delete this file after use for security
 */

require_once 'includes/db.php';

echo "<h1>Reset Admin Password</h1>";
echo "<hr>";

try {
    // New password
    $newPassword = 'admin123';
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    
    // Update admin password
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
    $result = $stmt->execute([$hashedPassword]);
    
    if ($result) {
        echo "<p style='color: green; font-size: 18px;'><b>✅ SUCCESS!</b></p>";
        echo "<p>Admin password has been reset successfully.</p>";
        echo "<hr>";
        echo "<p><b>Login Details:</b></p>";
        echo "<ul>";
        echo "<li><b>URL:</b> <a href='login.php'>http://localhost/alabasi/login.php</a></li>";
        echo "<li><b>Username:</b> admin</li>";
        echo "<li><b>Password:</b> admin123</li>";
        echo "</ul>";
        echo "<hr>";
        echo "<p style='color: red;'><b>⚠️ IMPORTANT:</b> Delete this file (reset-password.php) for security!</p>";
    } else {
        echo "<p style='color: red;'>❌ ERROR: Could not update password</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ ERROR: " . $e->getMessage() . "</p>";
}
?>
