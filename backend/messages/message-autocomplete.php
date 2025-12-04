<?php
/**
 * Message Autocomplete API
 * Provides autocomplete suggestions for message search
 */

require_once '../database/config.php';
require_once '../database/functions.php';

// Set JSON header
header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode([]);
    exit;
}

// Get search term
$term = isset($_GET['term']) ? trim($_GET['term']) : '';

if (empty($term)) {
    echo json_encode([]);
    exit;
}

global $pdo;

// Search for messages - name, email, subject
$query = "SELECT DISTINCT name, email, subject 
          FROM contact_messages 
          WHERE name LIKE :term1 OR email LIKE :term2 OR subject LIKE :term3
          ORDER BY created_at DESC 
          LIMIT 10";

$stmt = $pdo->prepare($query);
$stmt->bindValue(':term1', "%$term%");
$stmt->bindValue(':term2', "%$term%");
$stmt->bindValue(':term3', "%$term%");
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format results
$suggestions = [];
foreach ($results as $row) {
    // Add name as primary suggestion
    if (!empty($row['name'])) {
        $suggestions[] = [
            'value' => $row['name'],
            'label' => $row['name'] . ' (' . $row['email'] . ')',
            'email' => $row['email'],
            'subject' => $row['subject']
        ];
    }
}

echo json_encode($suggestions);


