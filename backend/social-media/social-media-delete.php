<?php
/**
 * Delete Social Media Handler
 * API untuk menghapus social media
 */

require_once '../database/config.php';
require_once '../database/functions.php';

// Proteksi API - harus login
requireLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    try {
        // Check if social media exists
        $stmt = $pdo->prepare("SELECT platform FROM social_media WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $socialMedia = $stmt->fetch();
        
        if (!$socialMedia) {
            echo json_encode([
                'success' => false,
                'message' => 'Social media not found'
            ]);
            exit;
        }
        
        // Delete social media
        $stmt = $pdo->prepare("DELETE FROM social_media WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Social media deleted successfully'
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
