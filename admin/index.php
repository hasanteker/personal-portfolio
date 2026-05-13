<?php require __DIR__ . '/header.php'; ?>

<?php
$projectCount = (int)$pdo->query('SELECT COUNT(*) FROM projects')->fetchColumn();
$blogCount    = (int)$pdo->query('SELECT COUNT(*) FROM blog_posts')->fetchColumn();
$msgCount     = (int)$pdo->query('SELECT COUNT(*) FROM messages')->fetchColumn();
$unreadMsg    = (int)$pdo->query('SELECT COUNT(*) FROM messages WHERE is_read = 0')->fetchColumn();

$recentMessages = $pdo->query(
    'SELECT * FROM messages ORDER BY created_at DESC LIMIT 5'
)->fetchAll();

$loginTime = $_SESSION['login_time'] ?? time();
?>

<div class="admin-header">
    <div>
        <h1>Dashboard</h1>
        <p class="text-muted">
            Logged in as <strong><?= e($_SESSION['admin_username']) ?></strong>
            · Session started <?= date('H:i:s', $loginTime) ?>
        </p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <span class="stat-label">projects.count</span>
        <span class="stat-value"><?= $projectCount ?></span>
        <a href="projects.php" class="stat-link">→ manage</a>
    </div>
    <div class="stat-card">
        <span class="stat-label">blog.count</span>
        <span class="stat-value purple"><?= $blogCount ?></span>
        <a href="blog.php" class="stat-link">→ manage</a>
    </div>
    <div class="stat-card">
        <span class="stat-label">messages.total</span>
        <span class="stat-value success"><?= $msgCount ?></span>
        <a href="messages.php" class="stat-link">→ view all</a>
    </div>
    <div class="stat-card">
        <span class="stat-label">messages.unread</span>
        <span class="stat-value warning"><?= $unreadMsg ?></span>
        <a href="messages.php" class="stat-link">→ read now</a>
    </div>
</div>

<section class="admin-section">
    <h2>Recent Messages</h2>
    <?php if (!$recentMessages): ?>
        <p class="empty-state">No messages yet. They will appear here when visitors contact you.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>From</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Received</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentMessages as $msg): ?>
                <tr>
                    <td><strong><?= e($msg['name']) ?></strong></td>
                    <td><?= e($msg['email']) ?></td>
                    <td><?= e($msg['subject'] ?? '—') ?></td>
                    <td><?= e($msg['created_at']) ?></td>
                    <td>
                        <?php if ($msg['is_read']): ?>
                            <span class="badge">Read</span>
                        <?php else: ?>
                            <span class="badge badge-warning">New</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/footer.php'; ?>
