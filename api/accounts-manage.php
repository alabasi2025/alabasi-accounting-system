<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'add':
            $stmt = $pdo->prepare("INSERT INTO accounts (code, nameAr, nameEn, parentId, type, level, isParent, isActive, allowPosting, description, createdBy) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $_POST['code'],
                $_POST['nameAr'],
                $_POST['nameEn'] ?? null,
                $_POST['parentId'] ?: null,
                $_POST['type'],
                $_POST['level'],
                isset($_POST['isParent']) ? 1 : 0,
                isset($_POST['isActive']) ? 1 : 0,
                isset($_POST['allowPosting']) ? 1 : 0,
                $_POST['description'] ?? null,
                $_SESSION['user_id'] ?? 1
            ]);
            
            echo json_encode(['success' => true, 'message' => 'تم إضافة الحساب بنجاح']);
            break;
            
        case 'edit':
            $stmt = $pdo->prepare("UPDATE accounts SET code=?, nameAr=?, nameEn=?, parentId=?, type=?, level=?, isParent=?, isActive=?, allowPosting=?, description=? WHERE id=?");
            
            $stmt->execute([
                $_POST['code'],
                $_POST['nameAr'],
                $_POST['nameEn'] ?? null,
                $_POST['parentId'] ?: null,
                $_POST['type'],
                $_POST['level'],
                isset($_POST['isParent']) ? 1 : 0,
                isset($_POST['isActive']) ? 1 : 0,
                isset($_POST['allowPosting']) ? 1 : 0,
                $_POST['description'] ?? null,
                $_POST['id']
            ]);
            
            echo json_encode(['success' => true, 'message' => 'تم تحديث الحساب بنجاح']);
            break;
            
        case 'delete':
            // Check if account has children
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM accounts WHERE parentId = ?");
            $stmt->execute([$_POST['id']]);
            if ($stmt->fetchColumn() > 0) {
                echo json_encode(['success' => false, 'message' => 'لا يمكن حذف هذا الحساب لأنه يحتوي على حسابات فرعية']);
                exit;
            }
            
            // Check if account has transactions
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM journalEntries WHERE accountId = ?");
            $stmt->execute([$_POST['id']]);
            if ($stmt->fetchColumn() > 0) {
                echo json_encode(['success' => false, 'message' => 'لا يمكن حذف هذا الحساب لأنه يحتوي على قيود محاسبية']);
                exit;
            }
            
            $stmt = $pdo->prepare("DELETE FROM accounts WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            
            echo json_encode(['success' => true, 'message' => 'تم حذف الحساب بنجاح']);
            break;
            
        case 'get':
            $stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $account = $stmt->fetch();
            
            echo json_encode(['success' => true, 'data' => $account]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'إجراء غير صحيح']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'خطأ: ' . $e->getMessage()]);
}
?>
