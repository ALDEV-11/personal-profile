<?php
/**
 * Message Get API
 * Endpoint untuk mengambil data message by ID
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
        echo json_encode(['success' => false, 'message' => 'Invalid message ID']);
        exit;
    }
    
    // Get message by ID
    global $pdo;
    $query = "SELECT * FROM contact_messages WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $id]);
    $message = $stmt->fetch();
    
    if (!$message) {
        echo json_encode(['success' => false, 'message' => 'Message not found']);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $message
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
