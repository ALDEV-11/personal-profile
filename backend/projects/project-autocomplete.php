<?php
/**
 * Projects Autocomplete API
 * Provides autocomplete suggestions for projects search
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

// Search for projects
$query = "SELECT DISTINCT project_title, technologies 
          FROM projects 
          WHERE project_title LIKE :term1 OR technologies LIKE :term2 OR description LIKE :term3
          ORDER BY project_title ASC 
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
    $techs = !empty($row['technologies']) ? ' - ' . substr($row['technologies'], 0, 30) . '...' : '';
    $suggestions[] = [
        'value' => $row['project_title'],
        'label' => $row['project_title'] . $techs,
        'technologies' => $row['technologies']
    ];
}

echo json_encode($suggestions);
