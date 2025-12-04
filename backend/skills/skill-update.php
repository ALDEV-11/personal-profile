<?php
/**
 * Update Skill (AJAX)
 * API endpoint untuk update skill via AJAX
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

// Get existing skill
$skill = getSkillById($id);
if (!$skill) {
    echo json_encode(['success' => false, 'message' => 'Skill tidak ditemukan']);
    exit;
}

$data = [
    'skill_name' => sanitize($_POST['skill_name'] ?? ''),
    'category' => sanitize($_POST['category'] ?? ''),
    'skill_level' => strtolower(sanitize($_POST['skill_level'] ?? '')),
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

// Validasi skill level
$valid_levels = ['beginner', 'intermediate', 'advanced'];
if (empty($data['skill_level']) || !in_array($data['skill_level'], $valid_levels)) {
    echo json_encode(['success' => false, 'message' => 'Skill level harus dipilih (beginner, intermediate, atau advanced)']);
    exit;
}

// Update skill
if (updateSkill($id, $data)) {
    echo json_encode(['success' => true, 'message' => 'Skill berhasil diupdate']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal mengupdate skill']);
}
