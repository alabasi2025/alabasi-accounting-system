<?php
/**
 * API لإدارة الأصناف
 * Items Management API
 */

session_start();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'غير مصرح']);
    exit;
}

require_once '../../includes/db.php';

// تحديد نوع المحتوى
header('Content-Type: application/json; charset=utf-8');

// الحصول على الإجراء المطلوب
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'list':
            listItems();
            break;
            
        case 'get':
            getItem();
            break;
            
        case 'create':
            createItem();
            break;
            
        case 'update':
            updateItem();
            break;
            
        case 'delete':
            deleteItem();
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'إجراء غير صحيح']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

/**
 * عرض قائمة الأصناف
 */
function listItems() {
    global $pdo;
    
    $sql = "SELECT id, code, nameAr as name_ar, unit, purchasePrice as purchase_price, salePrice as sale_price, minStock as min_stock, isActive as is_active FROM items WHERE isActive = 1 ORDER BY code ASC";
    $stmt = $pdo->query($sql);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'items' => $items
    ]);
}

/**
 * الحصول على صنف محدد
 */
function getItem() {
    global $pdo;
    
    $id = $_GET['id'] ?? 0;
    
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'معرف الصنف مطلوب']);
        return;
    }
    
    $sql = "SELECT id, code, nameAr as name_ar, unit, purchasePrice as purchase_price, salePrice as sale_price, minStock as min_stock, isActive as is_active FROM items WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($item) {
        echo json_encode([
            'success' => true,
            'item' => $item
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'الصنف غير موجود']);
    }
}

/**
 * إنشاء صنف جديد
 */
function createItem() {
    global $pdo;
    
    // التحقق من البيانات المطلوبة
    $required = ['code', 'name', 'unit', 'purchase_price', 'sale_price'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => "الحقل $field مطلوب"]);
            return;
        }
    }
    
    // التحقق من عدم تكرار الكود
    $checkSql = "SELECT COUNT(*) FROM items WHERE code = ?";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$_POST['code']]);
    
    if ($checkStmt->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'كود الصنف موجود مسبقاً']);
        return;
    }
    
    // إدراج الصنف الجديد
    $sql = "INSERT INTO items (
        code, nameAr, nameEn, unit, 
        purchasePrice, salePrice, minStock, 
        isActive
    ) VALUES (?, ?, ?, ?, ?, ?, ?, 1)";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        $_POST['code'],
        $_POST['name'],
        $_POST['name'] ?? '',
        $_POST['unit'],
        $_POST['purchase_price'],
        $_POST['sale_price'],
        $_POST['min_stock'] ?? 0
    ]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'تم إضافة الصنف بنجاح',
            'id' => $pdo->lastInsertId()
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'فشل في إضافة الصنف']);
    }
}

/**
 * تحديث صنف موجود
 */
function updateItem() {
    global $pdo;
    
    $id = $_POST['id'] ?? 0;
    
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'معرف الصنف مطلوب']);
        return;
    }
    
    // التحقق من البيانات المطلوبة
    $required = ['code', 'name', 'unit', 'purchase_price', 'sale_price'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => "الحقل $field مطلوب"]);
            return;
        }
    }
    
    // التحقق من عدم تكرار الكود
    $checkSql = "SELECT COUNT(*) FROM items WHERE code = ? AND id != ?";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$_POST['code'], $id]);
    
    if ($checkStmt->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'كود الصنف موجود مسبقاً']);
        return;
    }
    
    // تحديث الصنف
    $sql = "UPDATE items SET 
        code = ?, 
        nameAr = ?, 
        nameEn = ?, 
        unit = ?, 
        purchasePrice = ?, 
        salePrice = ?, 
        minStock = ?
    WHERE id = ?";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        $_POST['code'],
        $_POST['name'],
        $_POST['name'] ?? '',
        $_POST['unit'],
        $_POST['purchase_price'],
        $_POST['sale_price'],
        $_POST['min_stock'] ?? 0,
        $id
    ]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'تم تحديث الصنف بنجاح'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'فشل في تحديث الصنف']);
    }
}

/**
 * حذف صنف (حذف منطقي)
 */
function deleteItem() {
    global $pdo;
    
    $id = $_POST['id'] ?? 0;
    
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'معرف الصنف مطلوب']);
        return;
    }
    
    // التحقق من عدم وجود حركات مخزون للصنف
    $checkSql = "SELECT COUNT(*) FROM stock_movements WHERE item_id = ?";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$id]);
    
    if ($checkStmt->fetchColumn() > 0) {
        echo json_encode([
            'success' => false, 
            'message' => 'لا يمكن حذف الصنف لوجود حركات مخزون مرتبطة به'
        ]);
        return;
    }
    
    // حذف منطقي (تعطيل)
    $sql = "UPDATE items SET 
        isActive = 0
    WHERE id = ?";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$id]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'تم حذف الصنف بنجاح'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'فشل في حذف الصنف']);
    }
}
?>
