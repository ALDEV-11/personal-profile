<?php
/**
 * Education Update API
 * Endpoint untuk update education via AJAX
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
    // Validasi ID
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid education ID']);
        exit;
    }
    
    // Cek apakah education exists
    $query = "SELECT id FROM education WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $id]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Education not found']);
        exit;
    }
    
    // Validasi input
    $institution = trim($_POST['institution'] ?? '');
    $degree = trim($_POST['degree'] ?? '');
    $field_of_study = trim($_POST['field_of_study'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date = trim($_POST['end_date'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $display_order = isset($_POST['display_order']) ? intval($_POST['display_order']) : 0;
    
    // Validasi required fields
    if (empty($institution)) {
        echo json_encode(['success' => false, 'message' => 'Institution name is required']);
        exit;
    }
    
    if (empty($degree)) {
        echo json_encode(['success' => false, 'message' => 'Degree is required']);
        exit;
    }
    
    if (empty($field_of_study)) {
        echo json_encode(['success' => false, 'message' => 'Field of study is required']);
        exit;
    }
    
    if (empty($start_date)) {
        echo json_encode(['success' => false, 'message' => 'Start year is required']);
        exit;
    }
    
    // Validasi format tahun
    if (!is_numeric($start_date) || $start_date < 1900 || $start_date > 2100) {
        echo json_encode(['success' => false, 'message' => 'Invalid start year format']);
        exit;
    }
    
    // Validasi end_date jika diisi
    if (!empty($end_date)) {
        if (!is_numeric($end_date) || $end_date < 1900 || $end_date > 2100) {
            echo json_encode(['success' => false, 'message' => 'Invalid end year format']);
            exit;
        }
        if ($end_date < $start_date) {
            echo json_encode(['success' => false, 'message' => 'End year cannot be before start year']);
            exit;
        }
    }
    
    // Convert empty end_date to NULL
    if (empty($end_date)) {
        $end_date = null;
    }
    
    // Convert empty description to NULL
    if (empty($description)) {
        $description = null;
    }
    
    // Update database
    $query = "UPDATE education 
              SET institution = :institution, 
                  degree = :degree, 
                  field_of_study = :field_of_study,
                  start_date = :start_date,
                  end_date = :end_date,
                  description = :description,
                  display_order = :display_order
              WHERE id = :id";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':institution' => $institution,
        ':degree' => $degree,
        ':field_of_study' => $field_of_study,
        ':start_date' => $start_date,
        ':end_date' => $end_date,
        ':description' => $description,
        ':display_order' => $display_order,
        ':id' => $id
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Education updated successfully!'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
