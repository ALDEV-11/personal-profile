<?php
/**
 * Get Social Media Handler
 * API untuk mendapatkan data social media by ID (untuk edit modal)
 */

require_once '../database/config.php';
require_once '../database/functions.php';

// Proteksi API - harus login
requireLogin();

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM social_media WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $socialMedia = $stmt->fetch();
        
        if ($socialMedia) {
            echo json_encode([
                'success' => true,
                'data' => $socialMedia
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Social media not found'
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
