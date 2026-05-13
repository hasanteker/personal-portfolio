<?php require __DIR__ . '/header.php'; ?>

<?php
$flash = '';
$flashType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'save') {
        $id       = $_POST['id'] ?? '';
        $title    = trim($_POST['title'] ?? '');
        $excerpt  = trim($_POST['excerpt'] ?? '');
        $content  = trim($_POST['content'] ?? '');
        $category = trim($_POST['category'] ?? 'General');

        if ($title && $content) {
            if ($id) {
                $stmt = $pdo->prepare(
                    'UPDATE blog_posts
                     SET title=:t, excerpt=:e, content=:c, category=:cat
                     WHERE id=:id'
                );
                $stmt->execute([
                    ':t' => $title, ':e' => $excerpt, ':c' => $content,
                    ':cat' => $category, ':id' => $id,
                ]);
                $flash = 'Post updated.';
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO blog_posts (title, excerpt, content, category)
                     VALUES (:t, :e, :c, :cat)'
                );
                $stmt->execute([
                    ':t' => $title, ':e' => $excerpt, ':c' => $content, ':cat' => $category,
                ]);
                $flash = 'New post created.';
            }
            $flashType = 'success';
        } else {
            $flash = 'Title and content are required.';
            $flashType = 'error';
        }
    } elseif ($_POST['action'] === 'delete' && !empty($_POST['id'])) {
        $stmt = $pdo->prepare('DELETE FROM blog_posts WHERE id = :id');
        $stmt->execute([':id' => $_POST['id']]);
        $flash = 'Post deleted.';
        $flashType = 'success';
    }
}

$editing = null;
if (!empty($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM blog_posts WHERE id = :id');
    $stmt->execute([':id' => $_GET['edit']]);
    $editing = $stmt->fetch();
}

$posts = $pdo->query('SELECT * FROM blog_posts ORDER BY created_at DESC')->fetchAll();
?>

<div class="admin-header">
    <div>
        <h1>Blog Posts</h1>
        <p class="text-muted">Write and manage articles displayed on the homepage</p>
    </div>
</div>

<?php if ($flash): ?>
    <div class="form-response <?= $flashType ?>" style="display:block;margin-bottom:1.5rem">
        <?= e($flash) ?>
    </div>
<?php endif; ?>

<section class="admin-section">
    <h2><?= $editing ? 'Edit Post' : 'New Post' ?></h2>

    <form method="POST" class="admin-form">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= $editing ? (int)$editing['id'] : '' ?>">

        <div class="form-row">
            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" required maxlength="200"
                       value="<?= $editing ? e($editing['title']) : '' ?>">
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <input type="text" id="category" name="category" maxlength="50"
                       value="<?= $editing ? e($editing['category']) : 'General' ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="excerpt">Excerpt (short summary)</label>
            <input type="text" id="excerpt" name="excerpt" maxlength="300"
                   value="<?= $editing ? e($editing['excerpt']) : '' ?>">
        </div>

        <div class="form-group">
            <label for="content">Content *</label>
            <textarea id="content" name="content" rows="8" required><?= $editing ? e($editing['content']) : '' ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $editing ? 'Update Post' : 'Publish Post' ?></button>
            <?php if ($editing): ?>
                <a href="blog.php" class="btn btn-outline">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</section>

<section class="admin-section">
    <h2>All Posts (<?= count($posts) ?>)</h2>

    <?php if (!$posts): ?>
        <p class="empty-state">No posts yet.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $p): ?>
                <tr>
                    <td><strong><?= e($p['title']) ?></strong></td>
                    <td><span class="badge"><?= e($p['category']) ?></span></td>
                    <td><?= e($p['created_at']) ?></td>
                    <td class="actions-cell">
                        <a href="?edit=<?= (int)$p['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                        <form method="POST" style="display:inline" data-confirm="Delete this post?">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/footer.php'; ?>
