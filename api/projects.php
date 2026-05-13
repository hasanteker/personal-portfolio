<?php
/**
 * AJAX endpoint: fetch all projects as JSON.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

try {
    $stmt = $pdo->query('SELECT * FROM projects ORDER BY featured DESC, created_at DESC');
    $projects = $stmt->fetchAll();
    jsonResponse($projects);
} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error'], 500);
}
