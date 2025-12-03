<?php
/**
 * Create Experience Handler (AJAX)
 * API endpoint untuk create experience via AJAX
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
    $company = trim($_POST['company'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date = trim($_POST['end_date'] ?? '');
    $is_current = isset($_POST['is_current']) ? 1 : 0;
    $description = trim($_POST['description'] ?? '');
    $display_order = (int)($_POST['display_order'] ?? 0);
    
    // Validasi required fields
    if (empty($company) || empty($position) || empty($start_date)) {
        echo json_encode(['success' => false, 'message' => 'Company, position, and start date are required!']);
        exit;
    }
    
    // Jika currently working, set end_date to NULL
    if ($is_current) {
        $end_date = null;
    }
    
    // Insert ke database
    $sql = "INSERT INTO experience (company, position, start_date, end_date, is_current, description, display_order) 
            VALUES (:company, :position, :start_date, :end_date, :is_current, :description, :display_order)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':company' => $company,
        ':position' => $position,
        ':start_date' => $start_date,
        ':end_date' => $end_date,
        ':is_current' => $is_current,
        ':description' => $description,
        ':display_order' => $display_order
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Experience added successfully!']);
    exit;
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred!']);
    exit;
}
