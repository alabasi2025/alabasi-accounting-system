<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'غير مصرح']);
    exit;
}

try {
    // Get all tasks
    $tasksQuery = "
        SELECT 
            category,
            taskName,
            isCompleted
        FROM project_tasks
        ORDER BY orderIndex ASC
    ";
    
    $tasks = $pdo->query($tasksQuery)->fetchAll(PDO::FETCH_ASSOC);
    
    // Group tasks by category
    $groupedTasks = [];
    foreach ($tasks as $task) {
        $groupedTasks[$task['category']][] = [
            'taskName' => $task['taskName'],
            'isCompleted' => (bool)$task['isCompleted']
        ];
    }
    
    // Calculate statistics
    $totalTasks = count($tasks);
    $completedTasks = count(array_filter($tasks, function($t) { return $t['isCompleted']; }));
    $pendingTasks = $totalTasks - $completedTasks;
    $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total' => $totalTasks,
            'completed' => $completedTasks,
            'pending' => $pendingTasks,
            'progress' => $progress
        ],
        'tasks' => $groupedTasks
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
