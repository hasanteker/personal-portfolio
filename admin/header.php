<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | <?= e(SITE_NAME) ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono&display=swap" rel="stylesheet">
</head>
<body class="admin-body">

<aside class="admin-sidebar">
    <div class="sidebar-logo">
        <span class="logo-tag">~/admin</span> · console
    </div>
    <nav class="sidebar-nav">
        <a href="index.php"    class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">▸ dashboard</a>
        <a href="projects.php" class="<?= $currentPage === 'projects.php' ? 'active' : '' ?>">▸ projects</a>
        <a href="blog.php"     class="<?= $currentPage === 'blog.php' ? 'active' : '' ?>">▸ blog</a>
        <a href="messages.php" class="<?= $currentPage === 'messages.php' ? 'active' : '' ?>">▸ messages</a>
    </nav>
    <div class="sidebar-footer">
        <p class="sidebar-user">// logged in as <strong><?= e($_SESSION['admin_username']) ?></strong></p>
        <a href="../index.php" class="btn btn-outline btn-sm">↗ view site</a>
        <a href="logout.php" class="btn btn-danger btn-sm">⏻ logout</a>
    </div>
</aside>

<main class="admin-main">
