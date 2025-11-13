<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'admin';
    $_SESSION['role'] = 'admin';
}

$pageTitle = 'الإعدادات';
$settings = $pdo->query("SELECT * FROM settings ORDER BY category, keyName")->fetchAll();

require_once 'includes/header.php';
?>

<div class="page-header">
    <h1>⚙️ إعدادات النظام</h1>
</div>

<div class="card">
    <div class="card-header">
        <h2>الإعدادات العامة</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="api/settings.php">
            <?php
            $categories = [];
            foreach ($settings as $setting) {
                $categories[$setting['category']][] = $setting;
            }
            
            foreach ($categories as $category => $items):
            ?>
            <div class="settings-group">
                <h3><?= ucfirst($category) ?></h3>
                <?php foreach ($items as $item): ?>
                <div class="form-group">
                    <label><?= htmlspecialchars($item['description']) ?></label>
                    <input type="text" 
                           name="settings[<?= $item['keyName'] ?>]" 
                           value="<?= htmlspecialchars($item['keyValue']) ?>"
                           placeholder="<?= htmlspecialchars($item['keyName']) ?>">
                    <small><?= $item['keyName'] ?></small>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">حفظ الإعدادات</button>
            </div>
        </form>
    </div>
</div>

<style>
.settings-group {
    margin-bottom: 30px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
}

.settings-group h3 {
    margin-top: 0;
    margin-bottom: 20px;
    color: #333;
    border-bottom: 2px solid #ddd;
    padding-bottom: 10px;
}

.settings-group .form-group {
    margin-bottom: 15px;
}

.settings-group .form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}

.settings-group .form-group input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.settings-group .form-group small {
    display: block;
    margin-top: 5px;
    color: #666;
    font-size: 12px;
}
</style>

<?php require_once 'includes/footer.php'; ?>
