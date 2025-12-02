<?php
/**
 * Delete Skill Handler
 */

require_once 'config.php';
require_once 'functions.php';

requireLogin();

// Disable display errors untuk mencegah output sebelum header
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Support both GET and POST
$id = 0;
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
} elseif (isset($_GET['id'])) {
    $id = intval($_GET['id']);
}

if ($id <= 0) {
    setFlashMessage('error', 'ID tidak valid atau tidak ditemukan');
    header('Location: skills.php');
    exit;
}

error_log("DELETE SKILL: Attempting to delete skill ID: " . $id);

// Cek apakah skill ada
$skill = getSkillById($id);
if (!$skill) {
    error_log("DELETE SKILL: Skill not found for ID: " . $id);
    setFlashMessage('error', 'Skill tidak ditemukan');
    header('Location: skills.php');
    exit;
}

error_log("DELETE SKILL: Skill found, proceeding to delete ID: " . $id);

// Delete skill
if (deleteSkill($id)) {
    error_log("DELETE SKILL: Successfully deleted skill ID: " . $id);
    setFlashMessage('success', 'Skill berhasil dihapus!');
} else {
    error_log("DELETE SKILL: Failed to delete skill ID: " . $id);
    setFlashMessage('error', 'Gagal menghapus skill');
}

header('Location: skills.php');
exit;
