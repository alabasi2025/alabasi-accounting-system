<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'دليل البناء';

// Get all tasks grouped by category
$tasksQuery = "
    SELECT 
        id,
        category,
        taskName,
        isCompleted,
        completedAt,
        orderIndex
    FROM project_tasks
    ORDER BY orderIndex ASC
";

$tasks = $pdo->query($tasksQuery)->fetchAll(PDO::FETCH_ASSOC);

// Group tasks by category
$groupedTasks = [];
foreach ($tasks as $task) {
    $groupedTasks[$task['category']][] = $task;
}

// Get tests for each task
$testsQuery = "
    SELECT 
        id,
        taskId,
        testName,
        testType,
        isCompleted,
        completedAt,
        notes
    FROM task_tests
    ORDER BY taskId, orderIndex ASC
";

$allTests = $pdo->query($testsQuery)->fetchAll(PDO::FETCH_ASSOC);

// Group tests by task
$groupedTests = [];
foreach ($allTests as $test) {
    $groupedTests[$test['taskId']][] = $test;
}

// Calculate statistics
$totalTasks = count($tasks);
$completedTasks = count(array_filter($tasks, function($t) { return $t['isCompleted']; }));
$totalTests = count($allTests);
$completedTests = count(array_filter($allTests, function($t) { return $t['isCompleted']; }));
$progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

include 'includes/header.php';
?>

<link rel="stylesheet" href="css/professional.css">

<style>
.guide-container {
    padding: 20px;
}

.guide-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 10px 10px 0 0;
    margin-bottom: 0;
}

.guide-header h1 {
    margin: 0 0 10px 0;
    font-size: 28px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.guide-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 14px;
}

.breadcrumb {
    background: #f8f9fa;
    padding: 10px 20px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.breadcrumb a {
    color: #667eea;
    text-decoration: none;
}

.breadcrumb a:hover {
    text-decoration: underline;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-align: center;
}

.stat-card .number {
    font-size: 36px;
    font-weight: bold;
    color: #667eea;
    margin: 10px 0;
}

.stat-card .label {
    color: #6c757d;
    font-size: 14px;
}

.progress-bar-container {
    background: #e9ecef;
    height: 30px;
    border-radius: 15px;
    overflow: hidden;
    margin: 20px 0;
}

.progress-bar {
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    transition: width 0.3s ease;
}

.category-section {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    overflow: hidden;
}

.category-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 20px;
    font-weight: bold;
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.category-body {
    padding: 20px;
}

.task-item {
    padding: 15px;
    margin: 10px 0;
    background: #f8f9fa;
    border-radius: 8px;
    border-right: 4px solid #667eea;
    transition: all 0.2s;
}

.task-item:hover {
    background: #e9ecef;
    transform: translateX(-5px);
}

.task-item.completed {
    background: #d4edda;
    border-right-color: #28a745;
}

.task-header {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
}

.task-checkbox {
    width: 24px;
    height: 24px;
    cursor: pointer;
}

.task-name {
    flex: 1;
    font-weight: bold;
    color: #495057;
}

.task-name.completed {
    text-decoration: line-through;
    color: #6c757d;
}

.task-date {
    font-size: 12px;
    color: #6c757d;
}

.task-tests {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #dee2e6;
    display: none;
}

.task-tests.show {
    display: block;
}

.test-item {
    padding: 10px;
    margin: 5px 0;
    background: white;
    border-radius: 5px;
    border-right: 3px solid #17a2b8;
    display: flex;
    align-items: center;
    gap: 10px;
}

.test-item.completed {
    background: #d1ecf1;
    border-right-color: #138496;
}

.test-checkbox {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.test-name {
    flex: 1;
    font-size: 14px;
    color: #495057;
}

.test-name.completed {
    text-decoration: line-through;
    color: #6c757d;
}

.test-type-badge {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: bold;
    color: white;
}

.test-type-add { background: #28a745; }
.test-type-edit { background: #ffc107; color: #000; }
.test-type-delete { background: #dc3545; }
.test-type-save { background: #007bff; }
.test-type-search { background: #6c757d; }
.test-type-filter { background: #6610f2; }
.test-type-view { background: #17a2b8; }
.test-type-other { background: #6c757d; }

.toggle-tests-btn {
    background: #667eea;
    color: white;
    border: none;
    padding: 5px 15px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 12px;
}

.toggle-tests-btn:hover {
    background: #764ba2;
}
</style>

<div class="guide-container">
    <div class="guide-header">
        <h1>📋 دليل البناء</h1>
        <p>دليل شامل لجميع المهام والاختبارات - يتم التحديث تلقائياً</p>
    </div>

    <div class="breadcrumb">
        <a href="dashboard.php">🏠 الرئيسية</a> › 
        <span>دليل البناء</span>
    </div>

    <!-- إحصائيات -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="label">📋 إجمالي المهام</div>
            <div class="number"><?= $totalTasks ?></div>
        </div>
        <div class="stat-card">
            <div class="label">✅ مهام منجزة</div>
            <div class="number"><?= $completedTasks ?></div>
        </div>
        <div class="stat-card">
            <div class="label">⏳ مهام متبقية</div>
            <div class="number"><?= $totalTasks - $completedTasks ?></div>
        </div>
        <div class="stat-card">
            <div class="label">🧪 اختبارات منجزة</div>
            <div class="number"><?= $completedTests ?> / <?= $totalTests ?></div>
        </div>
    </div>

    <!-- شريط التقدم -->
    <div class="progress-bar-container">
        <div class="progress-bar" style="width: <?= $progress ?>%">
            <?= $progress ?>% مكتمل
        </div>
    </div>

    <!-- قائمة المهام -->
    <?php foreach ($groupedTasks as $category => $categoryTasks): ?>
        <?php
        $categoryCompleted = count(array_filter($categoryTasks, function($t) { return $t['isCompleted']; }));
        $categoryTotal = count($categoryTasks);
        ?>
        <div class="category-section">
            <div class="category-header">
                <span><?= $category ?></span>
                <span><?= $categoryCompleted ?> / <?= $categoryTotal ?></span>
            </div>
            <div class="category-body">
                <?php foreach ($categoryTasks as $task): ?>
                    <div class="task-item <?= $task['isCompleted'] ? 'completed' : '' ?>" data-task-id="<?= $task['id'] ?>">
                        <div class="task-header" onclick="toggleTests(<?= $task['id'] ?>)">
                            <input 
                                type="checkbox" 
                                class="task-checkbox" 
                                <?= $task['isCompleted'] ? 'checked' : '' ?>
                                onclick="event.stopPropagation(); toggleTask(<?= $task['id'] ?>, this.checked)"
                            >
                            <span class="task-name <?= $task['isCompleted'] ? 'completed' : '' ?>">
                                <?= $task['taskName'] ?>
                            </span>
                            <?php if ($task['completedAt']): ?>
                                <span class="task-date">
                                    ✅ <?= date('Y-m-d', strtotime($task['completedAt'])) ?>
                                </span>
                            <?php endif; ?>
                            <?php if (isset($groupedTests[$task['id']])): ?>
                                <button class="toggle-tests-btn">
                                    <?= count($groupedTests[$task['id']]) ?> اختبار
                                </button>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (isset($groupedTests[$task['id']])): ?>
                            <div class="task-tests" id="tests-<?= $task['id'] ?>">
                                <strong>🧪 الاختبارات:</strong>
                                <?php foreach ($groupedTests[$task['id']] as $test): ?>
                                    <div class="test-item <?= $test['isCompleted'] ? 'completed' : '' ?>">
                                        <input 
                                            type="checkbox" 
                                            class="test-checkbox"
                                            <?= $test['isCompleted'] ? 'checked' : '' ?>
                                            onclick="toggleTest(<?= $test['id'] ?>, this.checked)"
                                        >
                                        <span class="test-name <?= $test['isCompleted'] ? 'completed' : '' ?>">
                                            <?= $test['testName'] ?>
                                        </span>
                                        <span class="test-type-badge test-type-<?= $test['testType'] ?>">
                                            <?= strtoupper($test['testType']) ?>
                                        </span>
                                        <?php if ($test['completedAt']): ?>
                                            <span class="task-date">
                                                ✅ <?= date('Y-m-d', strtotime($test['completedAt'])) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
function toggleTests(taskId) {
    const testsDiv = document.getElementById('tests-' + taskId);
    if (testsDiv) {
        testsDiv.classList.toggle('show');
    }
}

function toggleTask(taskId, isCompleted) {
    fetch('api/build-guide.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'toggle_task',
            taskId: taskId,
            isCompleted: isCompleted ? 1 : 0
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('❌ خطأ: ' + data.message);
        }
    });
}

function toggleTest(testId, isCompleted) {
    fetch('api/build-guide.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'toggle_test',
            testId: testId,
            isCompleted: isCompleted ? 1 : 0
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('❌ خطأ: ' + data.message);
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>
