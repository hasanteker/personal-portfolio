<?php
/**
 * AJAX endpoint: fetch all blog posts as JSON.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

try {
    $stmt = $pdo->query('SELECT * FROM blog_posts ORDER BY created_at DESC');
    $posts = $stmt->fetchAll();
    jsonResponse($posts);
} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error'], 500);
}
