<?php
/**
 * API للحسابات الوسيطة
 * Intermediate Accounts API
 */

header('Content-Type: application/json');
require_once '../includes/db.php';
require_once '../includes/functions.php';

// التحقق من تسجيل الدخول
requireLogin();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($method) {
        case 'GET':
            if ($action === 'list') {
                handleList();
            } elseif ($action === 'get' && isset($_GET['id'])) {
                handleGet($_GET['id']);
            } elseif ($action === 'accounts') {
                handleGetAccounts();
            } else {
                throw new Exception('Invalid action');
            }
            break;
            
        case 'POST':
            handleSave();
            break;
            
        case 'DELETE':
            if (isset($_GET['id'])) {
                handleDelete($_GET['id']);
            } else {
                throw new Exception('ID is required');
            }
            break;
            
        default:
            throw new Exception('Method not allowed');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * جلب جميع الربطات
 */
function handleList() {
    global $pdo;
    
    $entityType = $_GET['entityType'] ?? null;
    
    $query = "
        SELECT m.*,
               sa.code as sourceAccountCode, sa.nameAr as sourceAccountName,
               ta.code as targetAccountCode, ta.nameAr as targetAccountName
        FROM intermediate_accounts_mapping m
        LEFT JOIN accounts sa ON m.sourceAccountId = sa.id
        LEFT JOIN accounts ta ON m.targetAccountId = ta.id
    ";
    
    if ($entityType) {
        $query .= " WHERE m.entityType = :entityType";
    }
    
    $query .= " ORDER BY m.entityType, m.id DESC";
    
    $stmt = $pdo->prepare($query);
    if ($entityType) {
        $stmt->execute(['entityType' => $entityType]);
    } else {
        $stmt->execute();
    }
    
    echo json_encode([
        'success' => true,
        'data' => $stmt->fetchAll()
    ]);
}

/**
 * جلب ربط واحد
 */
function handleGet($id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT m.*,
               sa.code as sourceAccountCode, sa.nameAr as sourceAccountName,
               ta.code as targetAccountCode, ta.nameAr as targetAccountName
        FROM intermediate_accounts_mapping m
        LEFT JOIN accounts sa ON m.sourceAccountId = sa.id
        LEFT JOIN accounts ta ON m.targetAccountId = ta.id
        WHERE m.id = ?
    ");
    $stmt->execute([$id]);
    $mapping = $stmt->fetch();
    
    if (!$mapping) {
        throw new Exception('Mapping not found');
    }
    
    echo json_encode([
        'success' => true,
        'data' => $mapping
    ]);
}

/**
 * جلب الحسابات حسب الكيان
 */
function handleGetAccounts() {
    global $pdo;
    
    $entityType = $_GET['entityType'] ?? null;
    $entityId = $_GET['entityId'] ?? null;
    
    if (!$entityType || !$entityId) {
        throw new Exception('entityType and entityId are required');
    }
    
    // تحديد companyId حسب نوع الكيان
    $companyId = null;
    
    if ($entityType === 'company') {
        $companyId = $entityId;
    } elseif ($entityType === 'branch') {
        // جلب companyId من الفرع
        $stmt = $pdo->prepare("SELECT organizationId FROM branches WHERE id = ?");
        $stmt->execute([$entityId]);
        $branch = $stmt->fetch();
        $companyId = $branch['organizationId'] ?? null;
    } elseif ($entityType === 'unit') {
        // جلب أول مؤسسة في الوحدة
        $stmt = $pdo->prepare("SELECT id FROM companies WHERE unitId = ? LIMIT 1");
        $stmt->execute([$entityId]);
        $company = $stmt->fetch();
        $companyId = $company['id'] ?? null;
    }
    
    if (!$companyId) {
        echo json_encode([
            'success' => true,
            'data' => []
        ]);
        return;
    }
    
    // جلب الحسابات
    $stmt = $pdo->prepare("
        SELECT id, code, nameAr, nameEn
        FROM accounts
        WHERE companyId = ? AND isActive = 1
        ORDER BY code
    ");
    $stmt->execute([$companyId]);
    
    echo json_encode([
        'success' => true,
        'data' => $stmt->fetchAll()
    ]);
}

/**
 * حفظ (إضافة/تعديل) ربط
 */
function handleSave() {
    global $pdo;
    
    $id = $_POST['id'] ?? null;
    $entityType = $_POST['entityType'] ?? null;
    $sourceEntityId = $_POST['sourceEntityId'] ?? null;
    $targetEntityId = $_POST['targetEntityId'] ?? null;
    $sourceAccountId = $_POST['sourceAccountId'] ?? null;
    $targetAccountId = $_POST['targetAccountId'] ?? null;
    $isActive = isset($_POST['isActive']) ? 1 : 0;
    
    // التحقق من البيانات المطلوبة
    if (!$entityType || !$sourceEntityId || !$targetEntityId || !$sourceAccountId || !$targetAccountId) {
        throw new Exception('جميع الحقول مطلوبة');
    }
    
    // التحقق من عدم تكرار الربط
    if ($id) {
        $checkStmt = $pdo->prepare("
            SELECT id FROM intermediate_accounts_mapping
            WHERE entityType = ? AND sourceEntityId = ? AND targetEntityId = ? AND id != ?
        ");
        $checkStmt->execute([$entityType, $sourceEntityId, $targetEntityId, $id]);
    } else {
        $checkStmt = $pdo->prepare("
            SELECT id FROM intermediate_accounts_mapping
            WHERE entityType = ? AND sourceEntityId = ? AND targetEntityId = ?
        ");
        $checkStmt->execute([$entityType, $sourceEntityId, $targetEntityId]);
    }
    
    if ($checkStmt->fetch()) {
        throw new Exception('هذا الربط موجود بالفعل');
    }
    
    if ($id) {
        // تعديل
        $stmt = $pdo->prepare("
            UPDATE intermediate_accounts_mapping
            SET entityType = ?,
                sourceEntityId = ?,
                targetEntityId = ?,
                sourceAccountId = ?,
                targetAccountId = ?,
                isActive = ?,
                updatedAt = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        $stmt->execute([
            $entityType,
            $sourceEntityId,
            $targetEntityId,
            $sourceAccountId,
            $targetAccountId,
            $isActive,
            $id
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'تم التعديل بنجاح',
            'id' => $id
        ]);
    } else {
        // إضافة
        $stmt = $pdo->prepare("
            INSERT INTO intermediate_accounts_mapping
            (entityType, sourceEntityId, targetEntityId, sourceAccountId, targetAccountId, isActive, createdBy)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $entityType,
            $sourceEntityId,
            $targetEntityId,
            $sourceAccountId,
            $targetAccountId,
            $isActive,
            $_SESSION['user_id'] ?? null
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'تم الإضافة بنجاح',
            'id' => $pdo->lastInsertId()
        ]);
    }
}

/**
 * حذف ربط
 */
function handleDelete($id) {
    global $pdo;
    
    $stmt = $pdo->prepare("DELETE FROM intermediate_accounts_mapping WHERE id = ?");
    $stmt->execute([$id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'تم الحذف بنجاح'
    ]);
}
