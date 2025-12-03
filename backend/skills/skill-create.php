<?php
/**
 * Create Skill (AJAX)
 * API endpoint untuk create skill via AJAX
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
    'skill_name' => sanitize($_POST['skill_name'] ?? ''),
    'category' => sanitize($_POST['category'] ?? ''),
    'skill_level' => (int)($_POST['skill_level'] ?? 0),
    'icon' => sanitize($_POST['icon'] ?? ''),
    'display_order' => (int)($_POST['display_order'] ?? 0)
];

// Validasi
if (empty($data['skill_name'])) {
    echo json_encode(['success' => false, 'message' => 'Skill name harus diisi']);
    exit;
}

if (empty($data['category'])) {
    echo json_encode(['success' => false, 'message' => 'Category harus diisi']);
    exit;
}

if ($data['skill_level'] < 0 || $data['skill_level'] > 100) {
    echo json_encode(['success' => false, 'message' => 'Skill level harus antara 0-100']);
    exit;
}

// Create skill
if (createSkill($data)) {
    echo json_encode(['success' => true, 'message' => 'Skill berhasil ditambahkan']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menambahkan skill']);
}
