<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'add':
            $stmt = $pdo->prepare("INSERT INTO companies (unitId, code, nameAr, nameEn, taxNumber, commercialRegister, phone, email, website, address, city, country, postalCode, fiscalYearStart, fiscalYearEnd, currency, isActive) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $_POST['unitId'] ?? null,
                $_POST['code'],
                $_POST['nameAr'],
                $_POST['nameEn'] ?? null,
                $_POST['taxNumber'] ?? null,
                $_POST['commercialRegister'] ?? null,
                $_POST['phone'] ?? null,
                $_POST['email'] ?? null,
                $_POST['website'] ?? null,
                $_POST['address'] ?? null,
                $_POST['city'] ?? null,
                $_POST['country'] ?? 'السعودية',
                $_POST['postalCode'] ?? null,
                $_POST['fiscalYearStart'] ?? null,
                $_POST['fiscalYearEnd'] ?? null,
                $_POST['currency'] ?? 'SAR',
                isset($_POST['isActive']) ? 1 : 0
            ]);
            
            echo json_encode(['success' => true, 'message' => 'تم إضافة المؤسسة بنجاح']);
            break;
            
        case 'edit':
            $stmt = $pdo->prepare("UPDATE companies SET unitId=?, code=?, nameAr=?, nameEn=?, taxNumber=?, commercialRegister=?, phone=?, email=?, website=?, address=?, city=?, country=?, postalCode=?, fiscalYearStart=?, fiscalYearEnd=?, currency=?, isActive=? WHERE id=?");
            
            $stmt->execute([
                $_POST['unitId'] ?? null,
                $_POST['code'],
                $_POST['nameAr'],
                $_POST['nameEn'] ?? null,
                $_POST['taxNumber'] ?? null,
                $_POST['commercialRegister'] ?? null,
                $_POST['phone'] ?? null,
                $_POST['email'] ?? null,
                $_POST['website'] ?? null,
                $_POST['address'] ?? null,
                $_POST['city'] ?? null,
                $_POST['country'] ?? 'السعودية',
                $_POST['postalCode'] ?? null,
                $_POST['fiscalYearStart'] ?? null,
                $_POST['fiscalYearEnd'] ?? null,
                $_POST['currency'] ?? 'SAR',
                isset($_POST['isActive']) ? 1 : 0,
                $_POST['id']
            ]);
            
            echo json_encode(['success' => true, 'message' => 'تم تحديث المؤسسة بنجاح']);
            break;
            
        case 'delete':
            $stmt = $pdo->prepare("DELETE FROM companies WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            
            echo json_encode(['success' => true, 'message' => 'تم حذف المؤسسة بنجاح']);
            break;
            
        case 'get':
            $stmt = $pdo->prepare("SELECT * FROM companies WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $company = $stmt->fetch();
            
            echo json_encode(['success' => true, 'data' => $company]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'إجراء غير صحيح']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'خطأ: ' . $e->getMessage()]);
}
?>
