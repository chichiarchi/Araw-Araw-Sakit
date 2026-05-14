<?php
// api/heart.php
require_once '../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $today = date('Y-m-d');
        
        // Increment hearts for today
        $stmt = $pdo->prepare("UPDATE analytics SET hearts = hearts + 1 WHERE date = ?");
        $stmt->execute([$today]);
        
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
