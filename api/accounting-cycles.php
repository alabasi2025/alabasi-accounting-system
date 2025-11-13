<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'يجب تسجيل الدخول أولاً']);
    exit();
}

$userId = $_SESSION['user_id'];
$action = $_REQUEST['action'] ?? '';

// الحصول على معلومات المستخدم
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

try {
    switch ($action) {
        case 'add':
            addCycle($conn, $userId);
            break;
            
        case 'update':
            updateCycle($conn, $userId);
            break;
            
        case 'get':
            getCycle($conn);
            break;
            
        case 'view':
            viewCycle($conn);
            break;
            
        case 'close':
            closeCycle($conn, $userId);
            break;
            
        case 'reopen':
            reopenCycle($conn, $userId, $user);
            break;
            
        case 'delete':
            deleteCycle($conn, $userId);
            break;
            
        case 'create_yearly':
            createYearlyCycles($conn, $userId);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'عملية غير صحيحة']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'خطأ: ' . $e->getMessage()]);
}

// دالة إضافة دورة جديدة
function addCycle($conn, $userId) {
    $name = $_POST['name'] ?? '';
    $nameEn = $_POST['nameEn'] ?? '';
    $companyId = $_POST['companyId'] ?? 0;
    $type = $_POST['type'] ?? 'monthly';
    $status = $_POST['status'] ?? 'open';
    $startDate = $_POST['startDate'] ?? '';
    $endDate = $_POST['endDate'] ?? '';
    $notes = $_POST['notes'] ?? '';
    
    // التحقق من البيانات المطلوبة
    if (empty($name) || empty($companyId) || empty($startDate) || empty($endDate)) {
        echo json_encode(['success' => false, 'message' => 'يجب ملء جميع الحقول المطلوبة']);
        return;
    }
    
    // التحقق من عدم تداخل الفترات
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM account_cycles 
        WHERE companyId = ? 
        AND (
            (startDate <= ? AND endDate >= ?) OR
            (startDate <= ? AND endDate >= ?) OR
            (startDate >= ? AND endDate <= ?)
        )
    ");
    $stmt->bind_param("issssss", $companyId, $startDate, $startDate, $endDate, $endDate, $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result['count'] > 0) {
        echo json_encode(['success' => false, 'message' => 'يوجد تداخل مع دورة أخرى في نفس الفترة']);
        return;
    }
    
    // إضافة الدورة
    $stmt = $conn->prepare("
        INSERT INTO account_cycles 
        (companyId, name, nameEn, type, status, startDate, endDate, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isssssss", $companyId, $name, $nameEn, $type, $status, $startDate, $endDate, $notes);
    
    if ($stmt->execute()) {
        $cycleId = $conn->insert_id;
        
        // تسجيل العملية في السجل
        logCycleOperation($conn, $cycleId, 'created', $userId, 'إنشاء دورة جديدة', null, $status);
        
        echo json_encode([
            'success' => true, 
            'message' => 'تم إضافة الدورة بنجاح',
            'cycleId' => $cycleId
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'فشل في إضافة الدورة: ' . $conn->error]);
    }
}

// دالة تعديل دورة
function updateCycle($conn, $userId) {
    $cycleId = $_POST['cycleId'] ?? 0;
    $name = $_POST['name'] ?? '';
    $nameEn = $_POST['nameEn'] ?? '';
    $companyId = $_POST['companyId'] ?? 0;
    $type = $_POST['type'] ?? 'monthly';
    $status = $_POST['status'] ?? 'open';
    $startDate = $_POST['startDate'] ?? '';
    $endDate = $_POST['endDate'] ?? '';
    $notes = $_POST['notes'] ?? '';
    
    // التحقق من وجود الدورة
    $stmt = $conn->prepare("SELECT * FROM account_cycles WHERE id = ?");
    $stmt->bind_param("i", $cycleId);
    $stmt->execute();
    $cycle = $stmt->get_result()->fetch_assoc();
    
    if (!$cycle) {
        echo json_encode(['success' => false, 'message' => 'الدورة غير موجودة']);
        return;
    }
    
    // التحقق من إمكانية التعديل
    if ($cycle['status'] == 'closed') {
        echo json_encode(['success' => false, 'message' => 'لا يمكن تعديل دورة مقفلة']);
        return;
    }
    
    // التحقق من عدم تداخل الفترات
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM account_cycles 
        WHERE companyId = ? 
        AND id != ?
        AND (
            (startDate <= ? AND endDate >= ?) OR
            (startDate <= ? AND endDate >= ?) OR
            (startDate >= ? AND endDate <= ?)
        )
    ");
    $stmt->bind_param("iissssss", $companyId, $cycleId, $startDate, $startDate, $endDate, $endDate, $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result['count'] > 0) {
        echo json_encode(['success' => false, 'message' => 'يوجد تداخل مع دورة أخرى في نفس الفترة']);
        return;
    }
    
    // تعديل الدورة
    $stmt = $conn->prepare("
        UPDATE account_cycles 
        SET name = ?, nameEn = ?, companyId = ?, type = ?, status = ?, 
            startDate = ?, endDate = ?, notes = ?
        WHERE id = ?
    ");
    $stmt->bind_param("ssssssssi", $name, $nameEn, $companyId, $type, $status, $startDate, $endDate, $notes, $cycleId);
    
    if ($stmt->execute()) {
        // تسجيل العملية
        logCycleOperation($conn, $cycleId, 'modified', $userId, 'تعديل بيانات الدورة', $cycle['status'], $status);
        
        echo json_encode(['success' => true, 'message' => 'تم تعديل الدورة بنجاح']);
    } else {
        echo json_encode(['success' => false, 'message' => 'فشل في تعديل الدورة: ' . $conn->error]);
    }
}

// دالة الحصول على بيانات دورة
function getCycle($conn) {
    $cycleId = $_GET['id'] ?? 0;
    
    $stmt = $conn->prepare("SELECT * FROM account_cycles WHERE id = ?");
    $stmt->bind_param("i", $cycleId);
    $stmt->execute();
    $cycle = $stmt->get_result()->fetch_assoc();
    
    if ($cycle) {
        echo json_encode(['success' => true, 'data' => $cycle]);
    } else {
        echo json_encode(['success' => false, 'message' => 'الدورة غير موجودة']);
    }
}

// دالة عرض تفاصيل الدورة
function viewCycle($conn) {
    $cycleId = $_GET['id'] ?? 0;
    
    // الحصول على بيانات الدورة
    $stmt = $conn->prepare("
        SELECT 
            ac.*,
            c.nameAr as companyName,
            u.fullName as closedByName,
            (SELECT COUNT(*) FROM journals WHERE cycleId = ac.id) as journalCount,
            (SELECT COUNT(*) FROM journals WHERE cycleId = ac.id AND status = 'posted') as postedJournals,
            (SELECT COUNT(*) FROM period_balances WHERE cycleId = ac.id) as accountCount
        FROM account_cycles ac
        LEFT JOIN companies c ON c.id = ac.companyId
        LEFT JOIN users u ON u.id = ac.closedBy
        WHERE ac.id = ?
    ");
    $stmt->bind_param("i", $cycleId);
    $stmt->execute();
    $cycle = $stmt->get_result()->fetch_assoc();
    
    if (!$cycle) {
        echo json_encode(['success' => false, 'message' => 'الدورة غير موجودة']);
        return;
    }
    
    // الحصول على سجل العمليات
    $stmt = $conn->prepare("
        SELECT 
            col.*,
            u.fullName as performedByName
        FROM cycle_operations_log col
        JOIN users u ON u.id = col.performedBy
        WHERE col.cycleId = ?
        ORDER BY col.createdAt DESC
        LIMIT 10
    ");
    $stmt->bind_param("i", $cycleId);
    $stmt->execute();
    $operations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // بناء HTML
    $statusLabels = [
        'open' => 'مفتوح',
        'under_review' => 'قيد المراجعة',
        'closed' => 'مقفل'
    ];
    
    $typeLabels = [
        'monthly' => 'شهري',
        'quarterly' => 'ربع سنوي',
        'yearly' => 'سنوي'
    ];
    
    $operationLabels = [
        'created' => 'إنشاء',
        'opened' => 'فتح',
        'closed' => 'إقفال',
        'reopened' => 'إعادة فتح',
        'reviewed' => 'مراجعة',
        'modified' => 'تعديل',
        'deleted' => 'حذف'
    ];
    
    $html = '
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-info-circle text-primary me-2"></i>المعلومات الأساسية</h5>
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">اسم الدورة:</th>
                            <td>' . htmlspecialchars($cycle['name']) . '</td>
                        </tr>
                        <tr>
                            <th>الاسم الإنجليزي:</th>
                            <td>' . htmlspecialchars($cycle['nameEn'] ?? '-') . '</td>
                        </tr>
                        <tr>
                            <th>المؤسسة:</th>
                            <td>' . htmlspecialchars($cycle['companyName']) . '</td>
                        </tr>
                        <tr>
                            <th>النوع:</th>
                            <td><span class="badge badge-' . $cycle['type'] . '">' . $typeLabels[$cycle['type']] . '</span></td>
                        </tr>
                        <tr>
                            <th>الحالة:</th>
                            <td><span class="badge badge-' . str_replace('_', '', $cycle['status']) . '">' . $statusLabels[$cycle['status']] . '</span></td>
                        </tr>
                        <tr>
                            <th>تاريخ البداية:</th>
                            <td>' . date('Y-m-d', strtotime($cycle['startDate'])) . '</td>
                        </tr>
                        <tr>
                            <th>تاريخ النهاية:</th>
                            <td>' . date('Y-m-d', strtotime($cycle['endDate'])) . '</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-chart-bar text-success me-2"></i>الإحصائيات</h5>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h3 class="mb-1">' . $cycle['journalCount'] . '</h3>
                                <small class="text-muted">إجمالي القيود</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h3 class="mb-1">' . $cycle['postedJournals'] . '</h3>
                                <small class="text-muted">قيود مرحلة</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h3 class="mb-1">' . $cycle['accountCount'] . '</h3>
                                <small class="text-muted">حسابات بأرصدة</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h3 class="mb-1">' . count($operations) . '</h3>
                                <small class="text-muted">عمليات مسجلة</small>
                            </div>
                        </div>
                    </div>
                    
                    ' . ($cycle['status'] == 'closed' ? '
                    <div class="mt-3 p-3 bg-danger bg-opacity-10 rounded">
                        <strong><i class="fas fa-lock me-2"></i>تم الإقفال</strong><br>
                        <small>بواسطة: ' . htmlspecialchars($cycle['closedByName'] ?? 'غير معروف') . '</small><br>
                        <small>التاريخ: ' . date('Y-m-d H:i', strtotime($cycle['closedAt'])) . '</small>
                    </div>
                    ' : '') . '
                </div>
            </div>
        </div>
        
        ' . (!empty($cycle['notes']) ? '
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-sticky-note text-warning me-2"></i>الملاحظات</h5>
                    <p class="mb-0">' . nl2br(htmlspecialchars($cycle['notes'])) . '</p>
                </div>
            </div>
        </div>
        ' : '') . '
        
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-history text-info me-2"></i>سجل العمليات</h5>
                    ' . (empty($operations) ? '
                    <p class="text-muted text-center py-3">لا توجد عمليات مسجلة</p>
                    ' : '
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>العملية</th>
                                    <th>المستخدم</th>
                                    <th>السبب</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                    ');
    
    foreach ($operations as $op) {
        $html .= '
                                <tr>
                                    <td><span class="badge bg-secondary">' . $operationLabels[$op['operation']] . '</span></td>
                                    <td>' . htmlspecialchars($op['performedByName']) . '</td>
                                    <td>' . htmlspecialchars($op['reason'] ?? '-') . '</td>
                                    <td><small>' . date('Y-m-d H:i', strtotime($op['createdAt'])) . '</small></td>
                                </tr>
        ';
    }
    
    $html .= '
                            </tbody>
                        </table>
                    </div>
                    ') . '
                </div>
            </div>
        </div>
    </div>
    ';
    
    echo json_encode(['success' => true, 'html' => $html]);
}

// دالة إقفال دورة
function closeCycle($conn, $userId) {
    $cycleId = $_POST['cycleId'] ?? 0;
    $reason = $_POST['reason'] ?? '';
    
    // التحقق من وجود الدورة
    $stmt = $conn->prepare("SELECT * FROM account_cycles WHERE id = ?");
    $stmt->bind_param("i", $cycleId);
    $stmt->execute();
    $cycle = $stmt->get_result()->fetch_assoc();
    
    if (!$cycle) {
        echo json_encode(['success' => false, 'message' => 'الدورة غير موجودة']);
        return;
    }
    
    if ($cycle['status'] == 'closed') {
        echo json_encode(['success' => false, 'message' => 'الدورة مقفلة بالفعل']);
        return;
    }
    
    // حساب أرصدة الفترة
    try {
        $stmt = $conn->prepare("CALL sp_calculate_period_balances(?)");
        $stmt->bind_param("i", $cycleId);
        $stmt->execute();
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'فشل في حساب الأرصدة: ' . $e->getMessage()]);
        return;
    }
    
    // إقفال الدورة
    $stmt = $conn->prepare("
        UPDATE account_cycles 
        SET status = 'closed', closedBy = ?, closedAt = NOW()
        WHERE id = ?
    ");
    $stmt->bind_param("ii", $userId, $cycleId);
    
    if ($stmt->execute()) {
        // تسجيل العملية
        logCycleOperation($conn, $cycleId, 'closed', $userId, $reason ?: 'إقفال الدورة', $cycle['status'], 'closed');
        
        echo json_encode(['success' => true, 'message' => 'تم إقفال الدورة بنجاح']);
    } else {
        echo json_encode(['success' => false, 'message' => 'فشل في إقفال الدورة: ' . $conn->error]);
    }
}

// دالة إعادة فتح دورة
function reopenCycle($conn, $userId, $user) {
    // التحقق من الصلاحيات
    if ($user['role'] != 'admin') {
        echo json_encode(['success' => false, 'message' => 'غير مصرح لك بإعادة فتح الدورات']);
        return;
    }
    
    $cycleId = $_POST['cycleId'] ?? 0;
    $reason = $_POST['reason'] ?? '';
    
    if (empty($reason)) {
        echo json_encode(['success' => false, 'message' => 'يجب إدخال سبب إعادة الفتح']);
        return;
    }
    
    // التحقق من وجود الدورة
    $stmt = $conn->prepare("SELECT * FROM account_cycles WHERE id = ?");
    $stmt->bind_param("i", $cycleId);
    $stmt->execute();
    $cycle = $stmt->get_result()->fetch_assoc();
    
    if (!$cycle) {
        echo json_encode(['success' => false, 'message' => 'الدورة غير موجودة']);
        return;
    }
    
    if ($cycle['status'] != 'closed') {
        echo json_encode(['success' => false, 'message' => 'الدورة ليست مقفلة']);
        return;
    }
    
    // إعادة فتح الدورة
    $stmt = $conn->prepare("
        UPDATE account_cycles 
        SET status = 'open', closedBy = NULL, closedAt = NULL
        WHERE id = ?
    ");
    $stmt->bind_param("i", $cycleId);
    
    if ($stmt->execute()) {
        // تسجيل العملية
        logCycleOperation($conn, $cycleId, 'reopened', $userId, $reason, 'closed', 'open');
        
        echo json_encode(['success' => true, 'message' => 'تم إعادة فتح الدورة بنجاح']);
    } else {
        echo json_encode(['success' => false, 'message' => 'فشل في إعادة فتح الدورة: ' . $conn->error]);
    }
}

// دالة حذف دورة
function deleteCycle($conn, $userId) {
    $cycleId = $_POST['cycleId'] ?? 0;
    
    // التحقق من وجود الدورة
    $stmt = $conn->prepare("SELECT * FROM account_cycles WHERE id = ?");
    $stmt->bind_param("i", $cycleId);
    $stmt->execute();
    $cycle = $stmt->get_result()->fetch_assoc();
    
    if (!$cycle) {
        echo json_encode(['success' => false, 'message' => 'الدورة غير موجودة']);
        return;
    }
    
    // التحقق من عدم وجود قيود
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM journals WHERE cycleId = ?");
    $stmt->bind_param("i", $cycleId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result['count'] > 0) {
        echo json_encode(['success' => false, 'message' => 'لا يمكن حذف دورة تحتوي على قيود']);
        return;
    }
    
    // حذف الدورة
    $stmt = $conn->prepare("DELETE FROM account_cycles WHERE id = ?");
    $stmt->bind_param("i", $cycleId);
    
    if ($stmt->execute()) {
        // تسجيل العملية
        logCycleOperation($conn, $cycleId, 'deleted', $userId, 'حذف الدورة', $cycle['status'], null);
        
        echo json_encode(['success' => true, 'message' => 'تم حذف الدورة بنجاح']);
    } else {
        echo json_encode(['success' => false, 'message' => 'فشل في حذف الدورة: ' . $conn->error]);
    }
}

// دالة إنشاء دورات سنوية
function createYearlyCycles($conn, $userId) {
    $year = $_POST['year'] ?? date('Y');
    $companyId = $_POST['companyId'] ?? 0;
    
    if (empty($companyId)) {
        echo json_encode(['success' => false, 'message' => 'يجب اختيار المؤسسة']);
        return;
    }
    
    try {
        $stmt = $conn->prepare("CALL sp_create_yearly_cycles(?, ?)");
        $stmt->bind_param("ii", $year, $companyId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        echo json_encode(['success' => true, 'message' => $result['result']]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'فشل في إنشاء الدورات: ' . $e->getMessage()]);
    }
}

// دالة تسجيل العملية في السجل
function logCycleOperation($conn, $cycleId, $operation, $userId, $reason, $oldStatus, $newStatus) {
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $stmt = $conn->prepare("
        INSERT INTO cycle_operations_log 
        (cycleId, operation, performedBy, reason, oldStatus, newStatus, ipAddress, userAgent)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isissss", $cycleId, $operation, $userId, $reason, $oldStatus, $newStatus, $ipAddress, $userAgent);
    $stmt->execute();
}
?>
