<?php
/**
 * Mark Message as Read Handler (AJAX)
 */

require_once '../database/config.php';
require_once '../database/functions.php';

// Set header untuk JSON response
header('Content-Type: application/json');

// Cek login
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Validasi ID
if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID tidak ditemukan']);
    exit;
}

$id = intval($_GET['id']);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
    exit;
}

// Mark as read
if (markMessageAsRead($id)) {
    echo json_encode([
        'success' => true, 
        'message' => 'Pesan ditandai sebagai sudah dibaca',
        'id' => $id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menandai pesan']);
}
