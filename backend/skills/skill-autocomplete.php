<?php
/**
 * Skills Autocomplete API
 * Provides autocomplete suggestions for skills search
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

// Search for skills
$query = "SELECT DISTINCT skill_name, category 
          FROM skills 
          WHERE skill_name LIKE :term1 OR category LIKE :term2 
          ORDER BY skill_name ASC 
          LIMIT 10";

$stmt = $pdo->prepare($query);
$stmt->bindValue(':term1', "%$term%");
$stmt->bindValue(':term2', "%$term%");
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format results
$suggestions = [];
foreach ($results as $row) {
    $suggestions[] = [
        'value' => $row['skill_name'],
        'label' => $row['skill_name'] . ' (' . $row['category'] . ')',
        'category' => $row['category']
    ];
}

echo json_encode($suggestions);
