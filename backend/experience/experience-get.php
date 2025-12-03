<?php
/**
 * Get Experience Handler
 * API untuk mendapatkan data experience by ID (untuk edit modal)
 */

require_once '../database/config.php';
require_once '../database/functions.php';

// Proteksi API - harus login
requireLogin();

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM experience WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $experience = $stmt->fetch();
        
        if ($experience) {
            echo json_encode([
                'success' => true,
                'data' => $experience
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Experience not found'
            ]);
        }
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
        'message' => 'ID parameter required'
    ]);
}
