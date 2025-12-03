<?php
/**
 * Experience Autocomplete API
 * Return autocomplete suggestions for experience search
 */

require_once '../database/config.php';
require_once '../database/functions.php';

// Proteksi API - harus login
requireLogin();

header('Content-Type: application/json');

$term = isset($_GET['term']) ? trim($_GET['term']) : '';

if (strlen($term) >= 2) {
    try {
        $searchTerm = "%$term%";
        
        $query = "SELECT DISTINCT company, position 
                  FROM experience 
                  WHERE company LIKE :term1 
                     OR position LIKE :term2 
                  ORDER BY company ASC 
                  LIMIT 10";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':term1' => $searchTerm,
            ':term2' => $searchTerm
        ]);
        
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = [
                'value' => $row['company'],
                'label' => $row['company'] . ' - ' . $row['position'],
                'subtitle' => $row['position']
            ];
        }
        
        echo json_encode($results);
        
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}
