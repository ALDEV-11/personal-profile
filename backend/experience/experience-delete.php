<?php
/**
 * Delete Experience Handler
 * API untuk menghapus experience
 */

require_once '../database/config.php';
require_once '../database/functions.php';

// Proteksi API - harus login
requireLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    try {
        // Check if experience exists
        $stmt = $pdo->prepare("SELECT company FROM experience WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $experience = $stmt->fetch();
        
        if (!$experience) {
            echo json_encode([
                'success' => false,
                'message' => 'Experience not found'
            ]);
            exit;
        }
        
        // Delete experience
        $stmt = $pdo->prepare("DELETE FROM experience WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Experience deleted successfully'
        ]);
        
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Database error occurred'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}
