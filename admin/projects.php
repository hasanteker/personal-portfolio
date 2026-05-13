<?php require __DIR__ . '/header.php'; ?>

<?php
$flash = '';
$flashType = '';

// Handle save (create or update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'save') {
        $id          = $_POST['id'] ?? '';
        $title       = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $tech_stack  = trim($_POST['tech_stack'] ?? '');
        $github_url  = trim($_POST['github_url'] ?? '');
        $demo_url    = trim($_POST['demo_url'] ?? '');
        $featured    = isset($_POST['featured']) ? 1 : 0;

        if ($title && $description) {
            if ($id) {
                $stmt = $pdo->prepare(
                    'UPDATE projects
                     SET title=:title, description=:description, tech_stack=:tech,
                         github_url=:gh, demo_url=:demo, featured=:f
                     WHERE id=:id'
                );
                $stmt->execute([
                    ':title' => $title, ':description' => $description, ':tech' => $tech_stack,
                    ':gh' => $github_url, ':demo' => $demo_url, ':f' => $featured, ':id' => $id,
                ]);
                $flash = 'Project updated successfully.';
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO projects (title, description, tech_stack, github_url, demo_url, featured)
                     VALUES (:title, :description, :tech, :gh, :demo, :f)'
                );
                $stmt->execute([
                    ':title' => $title, ':description' => $description, ':tech' => $tech_stack,
                    ':gh' => $github_url, ':demo' => $demo_url, ':f' => $featured,
                ]);
                $flash = 'New project created.';
            }
            $flashType = 'success';
        } else {
            $flash = 'Title and description are required.';
            $flashType = 'error';
        }
    } elseif ($_POST['action'] === 'delete' && !empty($_POST['id'])) {
        $stmt = $pdo->prepare('DELETE FROM projects WHERE id = :id');
        $stmt->execute([':id' => $_POST['id']]);
        $flash = 'Project deleted.';
        $flashType = 'success';
    }
}

$editing = null;
if (!empty($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM projects WHERE id = :id');
    $stmt->execute([':id' => $_GET['edit']]);
    $editing = $stmt->fetch();
}

$projects = $pdo->query('SELECT * FROM projects ORDER BY created_at DESC')->fetchAll();
?>

<div class="admin-header">
    <div>
        <h1>Projects</h1>
        <p class="text-muted">Add, edit and remove portfolio projects</p>
    </div>
</div>

<?php if ($flash): ?>
    <div class="form-response <?= $flashType ?>" style="display:block;margin-bottom:1.5rem">
        <?= e($flash) ?>
    </div>
<?php endif; ?>

<section class="admin-section">
    <h2><?= $editing ? 'Edit Project' : 'Add New Project' ?></h2>

    <form method="POST" class="admin-form">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= $editing ? (int)$editing['id'] : '' ?>">

        <div class="form-row">
            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" required maxlength="150"
                       value="<?= $editing ? e($editing['title']) : '' ?>">
            </div>
            <div class="form-group">
                <label for="tech_stack">Tech Stack (comma-separated)</label>
                <input type="text" id="tech_stack" name="tech_stack" maxlength="255"
                       placeholder="PHP, MySQL, JavaScript"
                       value="<?= $editing ? e($editing['tech_stack']) : '' ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="description">Description *</label>
            <textarea id="description" name="description" rows="4" required><?= $editing ? e($editing['description']) : '' ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="github_url">GitHub URL</label>
                <input type="url" id="github_url" name="github_url" maxlength="255"
                       value="<?= $editing ? e($editing['github_url']) : '' ?>">
            </div>
            <div class="form-group">
                <label for="demo_url">Live Demo URL</label>
                <input type="url" id="demo_url" name="demo_url" maxlength="255"
                       value="<?= $editing ? e($editing['demo_url']) : '' ?>">
            </div>
        </div>

        <div class="form-group checkbox-group">
            <label>
                <input type="checkbox" name="featured" <?= ($editing && $editing['featured']) ? 'checked' : '' ?>>
                Mark as Featured
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $editing ? 'Update Project' : 'Create Project' ?></button>
            <?php if ($editing): ?>
                <a href="projects.php" class="btn btn-outline">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</section>

<section class="admin-section">
    <h2>All Projects (<?= count($projects) ?>)</h2>

    <?php if (!$projects): ?>
        <p class="empty-state">No projects yet. Use the form above to add one.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Tech Stack</th>
                    <th>Featured</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $p): ?>
                <tr>
                    <td><strong><?= e($p['title']) ?></strong></td>
                    <td><?= e($p['tech_stack'] ?? '—') ?></td>
                    <td><?= $p['featured'] ? '★' : '—' ?></td>
                    <td><?= e($p['created_at']) ?></td>
                    <td class="actions-cell">
                        <a href="?edit=<?= (int)$p['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                        <form method="POST" style="display:inline" data-confirm="Delete this project?">
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
