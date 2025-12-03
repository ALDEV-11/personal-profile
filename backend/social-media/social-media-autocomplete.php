<?php
/**
 * Social Media Autocomplete API
 * Return autocomplete suggestions for social media search
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
        
        $query = "SELECT DISTINCT platform, url, icon 
                  FROM social_media 
                  WHERE platform LIKE :term1 
                     OR url LIKE :term2 
                  ORDER BY platform ASC 
                  LIMIT 10";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':term1' => $searchTerm,
            ':term2' => $searchTerm
        ]);
        
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Truncate URL if too long
            $displayUrl = strlen($row['url']) > 50 ? substr($row['url'], 0, 47) . '...' : $row['url'];
            
            $results[] = [
                'value' => $row['platform'],
                'label' => $row['platform'],
                'subtitle' => $displayUrl
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
