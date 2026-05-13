<?php require __DIR__ . '/header.php'; ?>

<?php
$flash = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'mark_read' && !empty($_POST['id'])) {
        $stmt = $pdo->prepare('UPDATE messages SET is_read = 1 WHERE id = :id');
        $stmt->execute([':id' => $_POST['id']]);
        $flash = 'Marked as read.';
    } elseif ($_POST['action'] === 'delete' && !empty($_POST['id'])) {
        $stmt = $pdo->prepare('DELETE FROM messages WHERE id = :id');
        $stmt->execute([':id' => $_POST['id']]);
        $flash = 'Message deleted.';
    }
}

$messages = $pdo->query('SELECT * FROM messages ORDER BY created_at DESC')->fetchAll();
?>

<div class="admin-header">
    <div>
        <h1>Messages</h1>
        <p class="text-muted">Contact form submissions stored in the database</p>
    </div>
</div>

<?php if ($flash): ?>
    <div class="form-response success" style="display:block;margin-bottom:1.5rem">
        <?= e($flash) ?>
    </div>
<?php endif; ?>

<section class="admin-section">
    <?php if (!$messages): ?>
        <div class="empty-state" style="padding:3rem;border:1px solid var(--border-soft);border-radius:6px;background:var(--bg-elev)">
            <p>No messages yet.</p>
        </div>
    <?php else: ?>
        <div class="messages-list">
            <?php foreach ($messages as $msg): ?>
                <article class="message-card <?= $msg['is_read'] ? '' : 'unread' ?>">
                    <header class="message-head">
                        <div>
                            <strong><?= e($msg['name']) ?></strong>
                            <a href="mailto:<?= e($msg['email']) ?>" class="message-email"><?= e($msg['email']) ?></a>
                        </div>
                        <span class="message-date"><?= e($msg['created_at']) ?></span>
                    </header>

                    <?php if ($msg['subject']): ?>
                        <p class="message-subject"><strong>Subject:</strong> <?= e($msg['subject']) ?></p>
                    <?php endif; ?>

                    <p class="message-body"><?= nl2br(e($msg['message'])) ?></p>

                    <footer class="message-foot">
                        <span class="badge <?= $msg['is_read'] ? '' : 'badge-warning' ?>">
                            <?= $msg['is_read'] ? 'Read' : 'Unread' ?>
                        </span>
                        <span class="text-muted">IP: <?= e($msg['ip_address'] ?? '—') ?></span>

                        <div class="message-actions">
                            <?php if (!$msg['is_read']): ?>
                                <form method="POST" style="display:inline">
                                    <input type="hidden" name="action" value="mark_read">
                                    <input type="hidden" name="id" value="<?= (int)$msg['id'] ?>">
                                    <button type="submit" class="btn btn-outline btn-sm">Mark as read</button>
                                </form>
                            <?php endif; ?>
                            <form method="POST" style="display:inline" data-confirm="Delete this message?">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int)$msg['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </footer>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/footer.php'; ?>
