<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'add':
            $stmt = $pdo->prepare("INSERT INTO analyticalAccounts (accountId, code, nameAr, nameEn, type, isActive) VALUES (?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $_POST['accountId'],
                $_POST['code'],
                $_POST['nameAr'],
                $_POST['nameEn'] ?? null,
                $_POST['type'],
                isset($_POST['isActive']) ? 1 : 0
            ]);
            
            echo json_encode(['success' => true, 'message' => 'تم إضافة الحساب التحليلي بنجاح']);
            break;
            
        case 'edit':
            $stmt = $pdo->prepare("UPDATE analyticalAccounts SET accountId=?, code=?, nameAr=?, nameEn=?, type=?, isActive=? WHERE id=?");
            
            $stmt->execute([
                $_POST['accountId'],
                $_POST['code'],
                $_POST['nameAr'],
                $_POST['nameEn'] ?? null,
                $_POST['type'],
                isset($_POST['isActive']) ? 1 : 0,
                $_POST['id']
            ]);
            
            echo json_encode(['success' => true, 'message' => 'تم تحديث الحساب التحليلي بنجاح']);
            break;
            
        case 'delete':
            $stmt = $pdo->prepare("DELETE FROM analyticalAccounts WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            
            echo json_encode(['success' => true, 'message' => 'تم حذف الحساب التحليلي بنجاح']);
            break;
            
        case 'get':
            $stmt = $pdo->prepare("SELECT * FROM analyticalAccounts WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $aa = $stmt->fetch();
            
            echo json_encode(['success' => true, 'data' => $aa]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'إجراء غير صحيح']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'خطأ: ' + $e->getMessage()]);
}
?>
