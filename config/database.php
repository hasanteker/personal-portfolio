<?php
/**
 * Database Connection (PDO)
 * --------------------------------------------
 * Update credentials below to match your MySQL setup.
 * Default values work with XAMPP / MAMP out of the box.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'portfolio');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('SITE_NAME', 'Portfolio | Full Stack Developer');
define('SITE_URL', 'http://localhost/portfolio');

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // In production, log the error instead of printing it.
    http_response_code(500);
    die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
}
