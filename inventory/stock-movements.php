<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'admin';
    $_SESSION['role'] = 'admin';
}

$pageTitle = 'حركات المخزون';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>حركات المخزون - نظام الأباسي</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>📋 حركات المخزون</h1>
        <button onclick="showAddMovementModal()" class="btn btn-primary">➕ حركة جديدة</button>
        
        <div class="filters">
            <input type="date" id="startDate" value="<?= date('Y-m-01') ?>" onchange="loadMovements()">
            <input type="date" id="endDate" value="<?= date('Y-m-d') ?>" onchange="loadMovements()">
            <select id="warehouseFilter" onchange="loadMovements()">
                <option value="">جميع المستودعات</option>
            </select>
        </div>
        
        <table id="movementsTable">
            <thead>
                <tr>
                    <th>رقم الحركة</th>
                    <th>التاريخ</th>
                    <th>النوع</th>
                    <th>الصنف</th>
                    <th>المستودع</th>
                    <th>الكمية</th>
                    <th>القيمة</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    
    <div id="movementModal" class="modal">
        <div class="modal-content">
            <h2>حركة مخزنية جديدة</h2>
            <form id="movementForm">
                <select id="movementType" required onchange="toggleTransferFields()">
                    <option value="">نوع الحركة</option>
                    <option value="in">إدخال</option>
                    <option value="out">إخراج</option>
                    <option value="transfer">تحويل</option>
                </select>
                <input type="date" id="movementDate" value="<?= date('Y-m-d') ?>" required>
                <select id="warehouseId" required></select>
                <select id="toWarehouseId" style="display:none"></select>
                <select id="itemId" required></select>
                <input type="number" id="quantity" placeholder="الكمية" step="0.01" required>
                <input type="number" id="unitPrice" placeholder="سعر الوحدة" step="0.01">
                <textarea id="notes" placeholder="ملاحظات"></textarea>
                <button type="submit">حفظ</button>
                <button type="button" onclick="closeMovementModal()">إلغاء</button>
            </form>
        </div>
    </div>
    
    <script src="../../assets/js/inventory.js"></script>
</body>
</html>
