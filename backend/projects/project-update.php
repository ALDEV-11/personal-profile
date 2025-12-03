<?php
/**
 * Update Project (AJAX)
 * API endpoint untuk update project via AJAX
 */

require_once '../database/config.php';
require_once '../database/functions.php';

requireLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method tidak valid']);
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
    exit;
}

// Get existing project
$project = getProjectById($id);
if (!$project) {
    echo json_encode(['success' => false, 'message' => 'Project tidak ditemukan']);
    exit;
}

$data = [
    'project_title' => sanitize($_POST['project_title'] ?? ''),
    'description' => sanitize($_POST['description'] ?? ''),
    'technologies' => sanitize($_POST['technologies'] ?? ''),
    'project_url' => sanitize($_POST['project_url'] ?? ''),
    'github_url' => sanitize($_POST['github_url'] ?? ''),
    'is_featured' => isset($_POST['is_featured']) ? 1 : 0
];

// Validasi
if (empty($data['project_title'])) {
    echo json_encode(['success' => false, 'message' => 'Project title harus diisi']);
    exit;
}

if (empty($data['description'])) {
    echo json_encode(['success' => false, 'message' => 'Description harus diisi']);
    exit;
}

// Handle image upload
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadResult = uploadImage($_FILES['image'], 'projects');
    if ($uploadResult['success']) {
        $data['image'] = $uploadResult['filename'];
        
        // Delete old image
        if (!empty($project['image'])) {
            $oldImagePath = UPLOAD_DIR . 'projects/' . $project['image'];
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }
    } else {
        echo json_encode(['success' => false, 'message' => $uploadResult['message']]);
        exit;
    }
} else {
    // Keep existing image
    $data['image'] = $project['image'];
}

// Update project
if (updateProject($id, $data)) {
    echo json_encode(['success' => true, 'message' => 'Project berhasil diupdate']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal mengupdate project']);
}
