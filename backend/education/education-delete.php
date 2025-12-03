<?php
/**
 * Education Delete Handler
 * Endpoint untuk delete education dengan POST method
 */

require_once '../database/config.php';
require_once '../database/functions.php';

// Proteksi halaman - harus login
requireLogin();

// Hanya terima POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlashMessage('error', 'Invalid request method');
    redirect(BACKEND_URL . 'education/');
}

try {
    // Validasi ID
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
    if ($id <= 0) {
        setFlashMessage('error', 'Invalid education ID');
        redirect(BACKEND_URL . 'education/');
    }
    
    // Cek apakah education exists
    $query = "SELECT id FROM education WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $id]);
    
    if (!$stmt->fetch()) {
        setFlashMessage('error', 'Education not found');
        redirect(BACKEND_URL . 'education/');
    }
    
    // Delete education
    $query = "DELETE FROM education WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $id]);
    
    setFlashMessage('success', 'Education deleted successfully!');
    redirect(BACKEND_URL . 'education/');
    
} catch (Exception $e) {
    setFlashMessage('error', 'Error: ' . $e->getMessage());
    redirect(BACKEND_URL . 'education/');
}
