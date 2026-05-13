<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Server-rendered skills (small dataset, no AJAX needed)
$skills = $pdo->query('SELECT * FROM skills ORDER BY level DESC')->fetchAll();

// "Last visit" cookie — demonstrates cookie usage on public site
$lastVisit = $_COOKIE['last_visit'] ?? null;
setcookie('last_visit', date('Y-m-d H:i:s'), time() + (86400 * 30), '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portfolio — Software Engineering student focused on cybersecurity. Portfolio of projects, writings and contact.">
    <meta name="author" content="Your Name">
    <title><?= e(SITE_NAME) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- ========== IDE WINDOW FRAME ========== -->
<div class="ide">

    <!-- TITLE BAR -->
    <header class="title-bar">
        <div class="window-controls" aria-hidden="true">
            <span class="dot dot-red"></span>
            <span class="dot dot-yellow"></span>
            <span class="dot dot-green"></span>
        </div>
        <div class="title-text">
            <span class="title-folder">my-portfolio</span>
            <span class="title-sep">—</span>
            <span class="title-file" id="titleFile">index.tsx</span>
        </div>
        <div class="title-actions">
            <button id="themeToggle" class="icon-btn" title="Toggle theme">
                <span class="theme-icon">◐</span>
            </button>
            <button id="sidebarToggle" class="icon-btn" title="Toggle sidebar">≡</button>
        </div>
    </header>

    <!-- WORKBENCH (sidebar + editor) -->
    <div class="workbench">

        <!-- ACTIVITY BAR (left strip) -->
        <nav class="activity-bar" aria-label="Activity bar">
            <button class="activity-btn active" title="Explorer" data-icon="files">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 4h6l2 2h8v14H4z"/>
                </svg>
            </button>
            <button class="activity-btn" title="Search" data-icon="search">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
                </svg>
            </button>
            <button class="activity-btn" title="Source Control" data-icon="git">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="6" cy="6" r="2.5"/><circle cx="18" cy="6" r="2.5"/><circle cx="12" cy="18" r="2.5"/>
                    <path d="M6 8.5v3a3 3 0 0 0 3 3h3M18 8.5v3a3 3 0 0 1-3 3h0"/>
                </svg>
            </button>
            <button class="activity-btn" title="Extensions" data-icon="ext">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="13" width="8" height="8" rx="1"/>
                    <path d="M13 3h5a3 3 0 0 1 3 3v5M3 13v5a3 3 0 0 0 3 3h5"/>
                </svg>
            </button>
            <span class="activity-spacer"></span>
            <a class="activity-btn" href="admin/login.php" title="Admin login">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>
                </svg>
            </a>
        </nav>

        <!-- SIDEBAR (file explorer) -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">EXPLORER</div>

            <div class="folder open">
                <div class="folder-name">▾ my-portfolio</div>
                <ul class="file-list">
                    <li class="file" data-target="home">
                        <span class="file-icon icon-tsx">▦</span>index.tsx
                    </li>
                    <li class="file" data-target="about">
                        <span class="file-icon icon-md">M↓</span>about.md
                    </li>
                    <li class="file" data-target="skills">
                        <span class="file-icon icon-json">{ }</span>skills.json
                    </li>
                    <li class="file" data-target="projects">
                        <span class="file-icon icon-folder">📁</span>projects/
                    </li>
                    <li class="file" data-target="blog">
                        <span class="file-icon icon-folder">📁</span>blog/
                    </li>
                    <li class="file" data-target="contact">
                        <span class="file-icon icon-php">&lt;?&gt;</span>contact.php
                    </li>
                </ul>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-header">OUTLINE</div>
                <ul class="outline-list">
                    <li><a href="#home">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#skills">Skills</a></li>
                    <li><a href="#projects">Projects</a></li>
                    <li><a href="#blog">Blog</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
        </aside>

        <!-- EDITOR -->
        <main class="editor">

            <!-- TAB BAR -->
            <div class="tab-bar">
                <button class="tab active" data-target="home">
                    <span class="file-icon icon-tsx">▦</span>index.tsx
                    <span class="tab-dirty" aria-hidden="true">●</span>
                </button>
                <button class="tab" data-target="about">
                    <span class="file-icon icon-md">M↓</span>about.md
                </button>
                <button class="tab" data-target="skills">
                    <span class="file-icon icon-json">{ }</span>skills.json
                </button>
                <button class="tab" data-target="projects">
                    <span class="file-icon icon-folder">📁</span>projects
                </button>
                <button class="tab" data-target="blog">
                    <span class="file-icon icon-folder">📁</span>blog
                </button>
                <button class="tab" data-target="contact">
                    <span class="file-icon icon-php">&lt;?&gt;</span>contact.php
                </button>
            </div>

            <!-- EDITOR BODY (scrolls) -->
            <div class="editor-body" id="editorBody">

                <!-- ============== SECTION: HOME ============== -->
                <section id="home" class="page">
                    <p class="breadcrumb">
                        <span class="bc-folder">my-portfolio</span>
                        <span class="bc-sep">›</span>
                        <span class="bc-file">index.tsx</span>
                    </p>

                    <p class="kicker">// HELLO WORLD</p>
                    <h1 class="hero-title">
                        Hi, I'm <span class="accent-purple">Your Name</span>.<br>
                        I break, build, and <span class="accent-blue">secure</span> the web.
                    </h1>

                    <p class="hero-sub">
                        Software Engineering student · Cybersecurity enthusiast ·
                        <span class="text-mono">Linux · Networks · Web Security</span>
                    </p>

                    <p class="hero-description">
                        Welcome to my personal portfolio — designed to feel like the
                        IDE I spend most of my day in. My focus is offensive and
                        defensive web security: I write code with the mindset of
                        someone trying to break it. Every project, post and form on
                        this site is built with that mindset in mind.
                    </p>

                    <div class="hero-actions">
                        <a href="#projects" class="btn btn-primary">
                            <span class="btn-icon">▶</span> View Projects
                        </a>
                        <a href="#contact" class="btn btn-outline">
                            <span class="btn-icon">✉</span> Contact Me
                        </a>
                    </div>

                    <?php if ($lastVisit): ?>
                    <div class="terminal-line">
                        <span class="prompt">~/my-portfolio $</span>
                        <span class="terminal-text">welcome back · last visit: <?= e($lastVisit) ?></span>
                    </div>
                    <?php endif; ?>

                    <pre class="code-block" aria-hidden="true"><span class="c-keyword">const</span> <span class="c-var">me</span> = {
  <span class="c-prop">name</span>: <span class="c-str">'Your Name'</span>,
  <span class="c-prop">role</span>: <span class="c-str">'SecDev · Student'</span>,
  <span class="c-prop">school</span>: <span class="c-str">'Your University'</span>,
  <span class="c-prop">focus</span>: [<span class="c-str">'Web Security'</span>, <span class="c-str">'Networks'</span>, <span class="c-str">'Linux'</span>],
  <span class="c-prop">available</span>: <span class="c-bool">true</span>,
};</pre>
                </section>

                <!-- ============== SECTION: ABOUT ============== -->
                <section id="about" class="page">
                    <p class="breadcrumb">
                        <span class="bc-folder">my-portfolio</span>
                        <span class="bc-sep">›</span>
                        <span class="bc-file">about.md</span>
                    </p>

                    <h2 class="page-title"># About Me</h2>
                    <p class="md-meta">Last edited 2 days ago · 3 min read</p>

                    <div class="about-grid">
                        <article class="about-text">
                            <p>I'm a <strong>Software Engineering</strong> student at
                            <strong>Your University</strong>, with a strong
                            interest in <strong>cybersecurity</strong> — particularly web
                            security, authentication, and secure coding practices.</p>

                            <p>To build a solid foundation, I'm currently mastering the
                            fundamentals of full-stack web development: semantic HTML,
                            responsive CSS, vanilla JavaScript, server-side PHP, and
                            MySQL. The mindset I bring to every project is simple —
                            "what would an attacker do with this?"</p>

                            <p>This portfolio reflects that mindset:</p>
                            <ul class="md-list">
                                <li>Every form is validated both client-side and server-side.</li>
                                <li>Every SQL query uses <code>PDO</code> prepared statements (no string concat).</li>
                                <li>Every password is hashed with <code>bcrypt</code> via <code>password_hash()</code>.</li>
                                <li>Every user input is escaped with <code>htmlspecialchars()</code> to prevent XSS.</li>
                                <li>Sessions are regenerated on login to prevent fixation attacks.</li>
                            </ul>

                            <p>Outside of class I'm reading writeups, solving CTF
                            challenges, and exploring the deeper layers of how the web
                            actually works.</p>
                        </article>

                        <aside class="about-card">
                            <h4>Quick Info</h4>
                            <ul class="kv-list">
                                <li><span class="kv-key">location</span><span class="kv-val">Your City</span></li>
                                <li><span class="kv-key">studying</span><span class="kv-val">Software Eng.</span></li>
                                <li><span class="kv-key">school</span><span class="kv-val">Your University</span></li>
                                <li><span class="kv-key">focus</span><span class="kv-val">Cybersecurity</span></li>
                                <li><span class="kv-key">interests</span><span class="kv-val">Web Sec · CTF · Linux</span></li>
                                <li><span class="kv-key">status</span><span class="kv-val accent-green">● open to work</span></li>
                            </ul>
                        </aside>
                    </div>
                </section>

                <!-- ============== SECTION: SKILLS ============== -->
                <section id="skills" class="page">
                    <p class="breadcrumb">
                        <span class="bc-folder">my-portfolio</span>
                        <span class="bc-sep">›</span>
                        <span class="bc-file">skills.json</span>
                    </p>

                    <h2 class="page-title">{ } skills.json</h2>
                    <p class="md-meta">Technologies I work with daily · scored 0–100</p>

                    <table class="skills-table">
                        <thead>
                            <tr>
                                <th>Technology</th>
                                <th>Category</th>
                                <th>Proficiency</th>
                                <th class="num-col">Level</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($skills as $skill): ?>
                            <tr>
                                <td>
                                    <span class="skill-mark"><?= e($skill['icon'] ?? '?') ?></span>
                                    <strong><?= e($skill['name']) ?></strong>
                                </td>
                                <td><span class="badge"><?= e($skill['category']) ?></span></td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar" data-level="<?= (int)$skill['level'] ?>" style="width:0%"></div>
                                    </div>
                                </td>
                                <td class="num-col text-mono"><?= (int)$skill['level'] ?>%</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>

                <!-- ============== SECTION: PROJECTS ============== -->
                <section id="projects" class="page">
                    <p class="breadcrumb">
                        <span class="bc-folder">my-portfolio</span>
                        <span class="bc-sep">›</span>
                        <span class="bc-folder">projects</span>
                    </p>

                    <h2 class="page-title">📁 projects/</h2>
                    <p class="md-meta">Live from the database via <code>fetch('api/projects.php')</code></p>

                    <div class="filter-bar" role="tablist">
                        <button class="filter-btn active" data-filter="all">All</button>
                        <button class="filter-btn" data-filter="featured">★ Featured</button>
                        <button class="filter-btn" data-filter="latest">⏱ Latest</button>
                    </div>

                    <div id="projectsGrid" class="projects-grid">
                        <p class="loader">// loading projects…</p>
                    </div>
                </section>

                <!-- ============== SECTION: BLOG ============== -->
                <section id="blog" class="page">
                    <p class="breadcrumb">
                        <span class="bc-folder">my-portfolio</span>
                        <span class="bc-sep">›</span>
                        <span class="bc-folder">blog</span>
                    </p>

                    <h2 class="page-title">📁 blog/</h2>
                    <p class="md-meta">Recent writings · fetched via AJAX</p>

                    <div id="blogGrid" class="blog-grid">
                        <p class="loader">// loading posts…</p>
                    </div>
                </section>

                <!-- ============== SECTION: CONTACT ============== -->
                <section id="contact" class="page">
                    <p class="breadcrumb">
                        <span class="bc-folder">my-portfolio</span>
                        <span class="bc-sep">›</span>
                        <span class="bc-file">contact.php</span>
                    </p>

                    <h2 class="page-title">&lt;?&gt; contact.php</h2>
                    <p class="md-meta">Saves directly to the <code>messages</code> table in MySQL</p>

                    <div class="contact-grid">
                        <aside class="contact-info">
                            <h4>Get in touch</h4>
                            <ul class="kv-list">
                                <li><span class="kv-key">email</span><a class="kv-val" href="mailto:your.email@example.com">your.email@example.com</a></li>
                                <li><span class="kv-key">github</span><a class="kv-val" href="https://github.com/yourusername" target="_blank" rel="noopener">github.com/yourusername</a></li>
                                <li><span class="kv-key">linkedin</span><a class="kv-val" href="https://www.linkedin.com/in/yourusername" target="_blank" rel="noopener">in/yourusername</a></li>
                                <li><span class="kv-key">location</span><span class="kv-val">Your City</span></li>
                                <li><span class="kv-key">status</span><span class="kv-val accent-green">● available</span></li>
                            </ul>

                        </aside>

                        <form id="contactForm" class="contact-form" novalidate>
                            <p class="form-comment">/* Send me a message — I'll get back within 24h */</p>

                            <div class="form-group">
                                <label for="name">name <span class="text-mono small-text">: string</span></label>
                                <input type="text" id="name" name="name" required minlength="2" maxlength="100">
                                <span class="error-msg" data-for="name"></span>
                            </div>
                            <div class="form-group">
                                <label for="email">email <span class="text-mono small-text">: string</span></label>
                                <input type="email" id="email" name="email" required>
                                <span class="error-msg" data-for="email"></span>
                            </div>
                            <div class="form-group">
                                <label for="subject">subject <span class="text-mono small-text">: string?</span></label>
                                <input type="text" id="subject" name="subject" maxlength="200">
                                <span class="error-msg" data-for="subject"></span>
                            </div>
                            <div class="form-group">
                                <label for="message">message <span class="text-mono small-text">: string</span></label>
                                <textarea id="message" name="message" rows="6" required minlength="10"></textarea>
                                <span class="error-msg" data-for="message"></span>
                            </div>
                            <button type="submit" class="btn btn-primary btn-full">
                                <span class="btn-text">▶ Send Message</span>
                                <span class="btn-loader" hidden>… sending</span>
                            </button>
                            <div id="formResponse" class="form-response"></div>
                        </form>
                    </div>
                </section>

                <!-- ============== FOOTER ============== -->
                <footer class="page-footer">
                    <p>// © <?= date('Y') ?> Your Name · Built with PHP, MySQL &amp; vanilla JavaScript.</p>
                    <p class="text-mono small-text">EOF · <a href="admin/login.php">admin →</a></p>
                </footer>

            </div><!-- /.editor-body -->
        </main>
    </div><!-- /.workbench -->

    <!-- STATUS BAR -->
    <footer class="status-bar">
        <div class="status-left">
            <span class="status-item">
                <span class="status-icon">⎇</span>main
            </span>
            <span class="status-item">
                <span class="status-icon accent-green">●</span>0 errors
            </span>
            <span class="status-item">
                <span class="status-icon accent-yellow">⚠</span>0 warnings
            </span>
        </div>
        <div class="status-right">
            <span class="status-item" id="statusFile">index.tsx</span>
            <span class="status-item">UTF-8</span>
            <span class="status-item">LF</span>
            <span class="status-item">TypeScript</span>
        </div>
    </footer>

</div><!-- /.ide -->

<a href="#home" class="back-to-top" id="backToTop" aria-label="Back to top">↑</a>

<script src="assets/js/main.js"></script>
</body>
</html>
