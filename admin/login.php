<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if ($username === '' || $password === '') {
        $error = 'Please fill in both fields.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :u LIMIT 1');
        $stmt->execute([':u' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id']       = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['login_time']     = time();

            // "Remember me" cookie - stores username for 30 days
            if ($remember) {
                setcookie('remembered_user', $username, time() + (86400 * 30), '/', '', false, true);
            } else {
                setcookie('remembered_user', '', time() - 3600, '/');
            }

            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}

$rememberedUser = $_COOKIE['remembered_user'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | <?= e(SITE_NAME) ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono&display=swap" rel="stylesheet">
</head>
<body class="admin-body">

<div class="login-wrapper">
    <form method="POST" class="login-card" autocomplete="on">
        <a href="../index.php" class="back-link">../ back to site</a>

        <div class="login-header">
            <div class="login-logo">
                <span class="logo-tag">~/admin</span> · auth.php
            </div>
            <h1>Sign In</h1>
            <p>Access the editor's console</p>
        </div>

        <?php if ($error): ?>
            <div class="form-response error" style="display:block;margin-bottom:1rem">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username"
                   value="<?= e($rememberedUser) ?>" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group checkbox-group">
            <label>
                <input type="checkbox" name="remember" <?= $rememberedUser ? 'checked' : '' ?>>
                Remember my username (cookie)
            </label>
        </div>

        <button type="submit" class="btn btn-primary btn-full">▶ Sign In</button>

        <p class="login-hint">
            // default credentials: <code>admin</code> / <code>admin123</code>
        </p>
    </form>
</div>

</body>
</html>
