<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'غير مصرح']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

try {
    if ($action === 'toggle_task') {
        $taskId = $input['taskId'] ?? 0;
        $isCompleted = $input['isCompleted'] ?? 0;
        
        $completedAt = $isCompleted ? date('Y-m-d H:i:s') : null;
        
        $stmt = $pdo->prepare('UPDATE project_tasks SET isCompleted = ?, completedAt = ? WHERE id = ?');
        $stmt->execute([$isCompleted, $completedAt, $taskId]);
        
        echo json_encode(['success' => true, 'message' => 'تم التحديث بنجاح']);
        
    } elseif ($action === 'toggle_test') {
        $testId = $input['testId'] ?? 0;
        $isCompleted = $input['isCompleted'] ?? 0;
        
        $completedAt = $isCompleted ? date('Y-m-d H:i:s') : null;
        
        $stmt = $pdo->prepare('UPDATE task_tests SET isCompleted = ?, completedAt = ? WHERE id = ?');
        $stmt->execute([$isCompleted, $completedAt, $testId]);
        
        echo json_encode(['success' => true, 'message' => 'تم التحديث بنجاح']);
        
    } else {
        echo json_encode(['success' => false, 'message' => 'إجراء غير معروف']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
