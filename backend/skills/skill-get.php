<?php
/**
 * Get Skill Data (AJAX)
 * API endpoint untuk mengambil data skill by ID
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

$skill = getSkillById($id);

if ($skill) {
    echo json_encode(['success' => true, 'skill' => $skill]);
} else {
    echo json_encode(['success' => false, 'message' => 'Skill tidak ditemukan']);
}
