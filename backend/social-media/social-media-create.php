<?php
/**
 * Create Social Media Handler (AJAX)
 * API endpoint untuk create social media via AJAX
 */

require_once '../database/config.php';
require_once '../database/functions.php';

// Proteksi halaman - harus login
requireLogin();

// Set header JSON
header('Content-Type: application/json');

// Hanya terima POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Validasi input
    $platform = trim($_POST['platform'] ?? '');
    $url = trim($_POST['url'] ?? '');
    $icon = trim($_POST['icon'] ?? '');
    $display_order = (int)($_POST['display_order'] ?? 0);
    
    // Validasi required fields
    if (empty($platform) || empty($url) || empty($icon)) {
        echo json_encode(['success' => false, 'message' => 'Platform, URL, and icon are required!']);
        exit;
    }
    
    // Validasi URL format
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid URL format!']);
        exit;
    }
    
    // Insert ke database
    $sql = "INSERT INTO social_media (platform, url, icon, display_order) 
            VALUES (:platform, :url, :icon, :display_order)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':platform' => $platform,
        ':url' => $url,
        ':icon' => $icon,
        ':display_order' => $display_order
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Social media link added successfully!']);
    exit;
        
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred!']);
    exit;
}
