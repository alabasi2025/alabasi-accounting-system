<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'admin';
    $_SESSION['role'] = 'admin';
}

$pageTitle = 'إدارة المخازن';
$stmt = $pdo->query("
    SELECT w.*, 
           b.nameAr as branchName,
           u.name as managerName
    FROM warehouses w
    LEFT JOIN branches b ON w.branchId = b.id
    LEFT JOIN users u ON w.managerId = u.id
    ORDER BY w.id DESC
");
$warehouses = $stmt->fetchAll();
$branches = $pdo->query("SELECT id, code, nameAr FROM branches WHERE isActive = 1")->fetchAll();

require_once 'includes/header.php';
?>

<div class="page-header">
    <h1>📦 إدارة المخازن</h1>
    <button class="btn btn-primary" onclick="alert('قريباً: إضافة فرع جديد')">+ إضافة فرع</button>
</div>

<div class="card">
    <div class="card-header">
        <h2>قائمة المخازن (<?= count($warehouses) ?>)</h2>
    </div>
    <div class="card-body">
        <table class="data-table">
            <thead>
                <tr>
                    <th>الرمز</th>
                    <th>اسم الفرع</th>
                    <th>المؤسسة</th>
                    <th>المدينة</th>
                    <th>الهاتف</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($warehouses as $branch): ?>
                <tr>
                    <td><?= htmlspecialchars($branch['code']) ?></td>
                    <td><?= htmlspecialchars($branch['nameAr']) ?></td>
                    <td><?= htmlspecialchars($branch['companyName']) ?></td>
                    <td><?= htmlspecialchars($branch['city'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($branch['phone'] ?? '-') ?></td>
                    <td>
                        <span class="badge badge-<?= $branch['isActive'] ? 'success' : 'danger' ?>">
                            <?= $branch['isActive'] ? 'نشط' : 'غير نشط' ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="alert('قريباً: تعديل')">تعديل</button>
                        <button class="btn btn-sm btn-danger" onclick="alert('قريباً: حذف')">حذف</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
