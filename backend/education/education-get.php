<?php
/**
 * Education Get API
 * Endpoint untuk mengambil data education by ID untuk edit modal
 */

require_once '../database/config.php';
require_once '../database/functions.php';

// Proteksi halaman - harus login
requireLogin();

// Set header JSON
header('Content-Type: application/json');

// Hanya terima GET request
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Validasi ID
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid education ID']);
        exit;
    }
    
    // Get education by ID
    $query = "SELECT * FROM education WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $id]);
    $education = $stmt->fetch();
    
    if (!$education) {
        echo json_encode(['success' => false, 'message' => 'Education not found']);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $education
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
