<?php
/**
 * API لقائمة الحسابات الوسيطة
 * Intermediate Accounts List API
 */

header('Content-Type: application/json; charset=utf-8');
require_once '../includes/db.php';

try {
    // جلب جميع الحسابات الوسيطة
    $stmt = $pdo->query("
        SELECT 
            id,
            accountId,
            entityType,
            entityId,
            balance,
            isActive,
            createdAt,
            updatedAt
        FROM intermediate_accounts
        ORDER BY createdAt DESC
    ");
    
    $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'accounts' => $accounts,
        'total' => count($accounts)
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'خطأ في قاعدة البيانات: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
