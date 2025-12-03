<?php
/**
 * Get Project Data (AJAX)
 * API endpoint untuk mengambil data project by ID
 */

require_once '../database/config.php';
require_once '../database/functions.php';

requireLogin();

header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
    exit;
}

$project = getProjectById($id);

if ($project) {
    echo json_encode(['success' => true, 'project' => $project]);
} else {
    echo json_encode(['success' => false, 'message' => 'Project tidak ditemukan']);
}
