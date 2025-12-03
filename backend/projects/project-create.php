<?php
/**
 * Create Project (AJAX)
 * API endpoint untuk create project via AJAX
 */

require_once '../database/config.php';
require_once '../database/functions.php';

requireLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method tidak valid']);
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
    } else {
        echo json_encode(['success' => false, 'message' => $uploadResult['message']]);
        exit;
    }
}

// Create project
if (createProject($data)) {
    echo json_encode(['success' => true, 'message' => 'Project berhasil ditambahkan']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menambahkan project']);
}
