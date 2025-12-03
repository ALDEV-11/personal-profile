<?php
/**
 * Delete Message Handler
 */

require_once '../database/config.php';
require_once '../database/functions.php';

requireLogin();

// Log untuk debug tapi tidak tampilkan error
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
    header('Location: ./');
    exit;
}

error_log("DELETE: Attempting to delete message ID: " . $id);

// Cek apakah message ada
$message = getMessageById($id);
if (!$message) {
    error_log("DELETE: Message not found for ID: " . $id);
    setFlashMessage('error', 'Pesan tidak ditemukan (ID: ' . $id . ')');
    header('Location: ./');
    exit;
}

error_log("DELETE: Message found, proceeding to delete ID: " . $id);

// Delete message
$result = deleteMessage($id);

if ($result) {
    error_log("DELETE: Successfully deleted message ID: " . $id);
    setFlashMessage('success', 'Pesan berhasil dihapus!');
} else {
    error_log("DELETE: Failed to delete message ID: " . $id);
    setFlashMessage('error', 'Gagal menghapus pesan (ID: ' . $id . ')');
}

header('Location: ./');
exit;
