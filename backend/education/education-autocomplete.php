<?php
/**
 * Education Autocomplete API
 * Provides autocomplete suggestions for education search
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

// Search for education
$query = "SELECT DISTINCT institution, degree, field_of_study 
          FROM education 
          WHERE institution LIKE :term1 OR degree LIKE :term2 OR field_of_study LIKE :term3
          ORDER BY institution ASC 
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
    $suggestions[] = [
        'value' => $row['institution'],
        'label' => $row['institution'] . ' - ' . $row['degree'] . ' in ' . $row['field_of_study'],
        'degree' => $row['degree'],
        'field' => $row['field_of_study']
    ];
}

echo json_encode($suggestions);
