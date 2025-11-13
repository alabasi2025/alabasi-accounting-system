<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'add':
            // Check if code already exists
            $stmt = $pdo->prepare("SELECT id FROM units WHERE code = ?");
            $stmt->execute([$_POST['code']]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'رمز الوحدة موجود مسبقاً']);
                exit;
            }
            
            $stmt = $pdo->prepare("INSERT INTO units (code, nameAr, nameEn, description, isActive, createdBy) VALUES (?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $_POST['code'],
                $_POST['nameAr'],
                $_POST['nameEn'] ?? null,
                $_POST['description'] ?? null,
                isset($_POST['isActive']) ? 1 : 0,
                $_SESSION['user_id'] ?? 1
            ]);
            
            echo json_encode(['success' => true, 'message' => 'تم إضافة الوحدة بنجاح']);
            break;
            
        case 'edit':
            // Check if code already exists (excluding current unit)
            $stmt = $pdo->prepare("SELECT id FROM units WHERE code = ? AND id != ?");
            $stmt->execute([$_POST['code'], $_POST['id']]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'رمز الوحدة موجود مسبقاً']);
                exit;
            }
            
            $stmt = $pdo->prepare("UPDATE units SET code=?, nameAr=?, nameEn=?, description=?, isActive=? WHERE id=?");
            
            $stmt->execute([
                $_POST['code'],
                $_POST['nameAr'],
                $_POST['nameEn'] ?? null,
                $_POST['description'] ?? null,
                isset($_POST['isActive']) ? 1 : 0,
                $_POST['id']
            ]);
            
            echo json_encode(['success' => true, 'message' => 'تم تحديث الوحدة بنجاح']);
            break;
            
        case 'delete':
            // Check if unit has companies
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM companies WHERE unitId = ?");
            $stmt->execute([$_POST['id']]);
            if ($stmt->fetchColumn() > 0) {
                echo json_encode(['success' => false, 'message' => 'لا يمكن حذف هذه الوحدة لأنها تحتوي على مؤسسات']);
                exit;
            }
            
            $stmt = $pdo->prepare("DELETE FROM units WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            
            echo json_encode(['success' => true, 'message' => 'تم حذف الوحدة بنجاح']);
            break;
            
        case 'get':
            $stmt = $pdo->prepare("SELECT * FROM units WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $unit = $stmt->fetch();
            
            // Get companies for this unit
            $stmt = $pdo->prepare("SELECT id, code, nameAr FROM companies WHERE unitId = ? ORDER BY code");
            $stmt->execute([$_GET['id']]);
            $companies = $stmt->fetchAll();
            
            echo json_encode(['success' => true, 'data' => $unit, 'companies' => $companies]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'إجراء غير صحيح']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'خطأ: ' . $e->getMessage()]);
}
?>
