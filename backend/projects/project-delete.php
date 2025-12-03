<?php
/**
 * Delete Project Handler
 * Handler untuk hapus project
 */

require_once '../database/config.php';
require_once '../database/functions.php';

requireLogin();

// Support both POST and GET for backward compatibility
$id = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);

if ($id <= 0) {
    setFlashMessage('error', 'ID project tidak valid!');
    header('Location: ./');
    exit;
}

// Get project data
$project = getProjectById($id);

if (!$project) {
    setFlashMessage('error', 'Project tidak ditemukan!');
    header('Location: ./');
    exit;
}

// Delete image file if exists
if (!empty($project['image'])) {
    $imagePath = UPLOAD_DIR . 'projects/' . $project['image'];
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
}

// Delete project from database
if (deleteProject($id)) {
    setFlashMessage('success', 'Project berhasil dihapus!');
} else {
    setFlashMessage('error', 'Gagal menghapus project!');
}

header('Location: ./');
exit;
