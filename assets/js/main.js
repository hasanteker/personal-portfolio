/**
 * Portfolio - Client-Side Logic
 * --------------------------------------------
 * Features required by the brief:
 *  - Dynamic UI: theme toggle + tab navigation (DOM manipulation on user events)
 *  - Form validation (client-side, before submission)
 *  - AJAX via Fetch API to load projects + blog and submit contact form
 */

(function () {
    'use strict';

    /* ---------- 1. THEME TOGGLE ---------- */
    const themeToggle = document.getElementById('themeToggle');
    const savedTheme = localStorage.getItem('theme') || 'dark';
    document.documentElement.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);

    themeToggle.addEventListener('click', () => {
        const current = document.documentElement.getAttribute('data-theme');
        const next = current === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', next);
        localStorage.setItem('theme', next);
        updateThemeIcon(next);
    });

    function updateThemeIcon(theme) {
        const icon = themeToggle.querySelector('.theme-icon');
        icon.textContent = theme === 'dark' ? '☾' : '☀';
    }

    /* ---------- 2. TAB / FILE NAVIGATION ---------- */
    const tabs = document.querySelectorAll('.tab[data-target]');
    const files = document.querySelectorAll('.file[data-target]');
    const editorBody = document.getElementById('editorBody');
    const titleFile = document.getElementById('titleFile');
    const statusFile = document.getElementById('statusFile');

    const fileNames = {
        home:     'index.tsx',
        about:    'about.md',
        skills:   'skills.json',
        projects: 'projects/',
        blog:     'blog/',
        contact:  'contact.php',
    };

    function activateTab(targetId) {
        tabs.forEach(t => t.classList.toggle('active', t.dataset.target === targetId));
        files.forEach(f => f.classList.toggle('active', f.dataset.target === targetId));

        const section = document.getElementById(targetId);
        if (section) {
            editorBody.scrollTo({ top: section.offsetTop - 8, behavior: 'smooth' });
        }

        if (fileNames[targetId]) {
            titleFile.textContent = fileNames[targetId];
            statusFile.textContent = fileNames[targetId];
        }
    }

    tabs.forEach(tab => tab.addEventListener('click', () => activateTab(tab.dataset.target)));
    files.forEach(file => file.addEventListener('click', () => {
        activateTab(file.dataset.target);
        if (window.innerWidth < 900) closeSidebar();
    }));

    // Outline anchor links (no smooth-scroll on the document, scroll the editor body)
    document.querySelectorAll('.outline-list a, a[href^="#"]').forEach(link => {
        const href = link.getAttribute('href') || '';
        if (!href.startsWith('#') || href === '#') return;
        link.addEventListener('click', e => {
            const id = href.slice(1);
            const target = document.getElementById(id);
            if (!target) return;
            e.preventDefault();
            activateTab(id);
        });
    });

    /* ---------- 3. SIDEBAR (mobile toggle) ---------- */
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');

    // Create backdrop element for mobile overlay
    const backdrop = document.createElement('div');
    backdrop.className = 'sidebar-backdrop';
    document.body.appendChild(backdrop);

    function openSidebar() {
        sidebar.classList.add('open');
        backdrop.classList.add('visible');
    }
    function closeSidebar() {
        sidebar.classList.remove('open');
        backdrop.classList.remove('visible');
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
        });
    }
    backdrop.addEventListener('click', closeSidebar);

    /* ---------- 4. BACK TO TOP + ACTIVE SECTION ON SCROLL ---------- */
    const backToTop = document.getElementById('backToTop');
    const sections = document.querySelectorAll('.page[id]');

    editorBody.addEventListener('scroll', () => {
        backToTop.classList.toggle('visible', editorBody.scrollTop > 300);

        // Active tab/file follows the section in view
        const offset = editorBody.scrollTop + 60;
        let current = sections[0]?.id;
        sections.forEach(sec => {
            if (sec.offsetTop <= offset) current = sec.id;
        });
        if (current && titleFile.textContent !== fileNames[current]) {
            tabs.forEach(t => t.classList.toggle('active', t.dataset.target === current));
            files.forEach(f => f.classList.toggle('active', f.dataset.target === current));
            titleFile.textContent = fileNames[current];
            statusFile.textContent = fileNames[current];
        }
    });

    backToTop.addEventListener('click', e => {
        e.preventDefault();
        activateTab('home');
    });

    /* ---------- 5. ANIMATE SKILL PROGRESS BARS ---------- */
    const bars = document.querySelectorAll('.progress-bar');
    if (bars.length) {
        const obs = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.width = entry.target.dataset.level + '%';
                    obs.unobserve(entry.target);
                }
            });
        }, { root: editorBody, threshold: 0.4 });
        bars.forEach(b => obs.observe(b));
    }

    /* ---------- 6. AJAX: LOAD PROJECTS ---------- */
    const projectsGrid = document.getElementById('projectsGrid');
    let allProjects = [];

    async function loadProjects() {
        try {
            const res = await fetch('api/projects.php');
            if (!res.ok) throw new Error('Network error');
            allProjects = await res.json();
            renderProjects(allProjects);
        } catch (err) {
            projectsGrid.innerHTML = `<p class="empty-state">// failed to load projects: ${err.message}</p>`;
        }
    }

    function renderProjects(list) {
        if (!list.length) {
            projectsGrid.innerHTML = '<p class="empty-state">// no projects to display.</p>';
            return;
        }

        projectsGrid.innerHTML = list.map((p, i) => {
            const fileName = slugify(p.title) + '.md';

            const techTags = (p.tech_stack || '')
                .split(',')
                .map(t => t.trim())
                .filter(Boolean)
                .map(t => `<span class="tech-tag">${escapeHtml(t)}</span>`)
                .join('');

            return `
                <article class="project-card" style="animation-delay:${i * 0.06}s">
                    <header class="project-head">
                        <span class="project-filename">
                            <span class="file-icon icon-md">M↓</span>${escapeHtml(fileName)}
                        </span>
                        ${p.featured == 1 ? '<span class="featured-badge">★ featured</span>' : ''}
                    </header>
                    <div class="project-body">
                        <h3 class="project-title">${escapeHtml(p.title)}</h3>
                        <p class="project-description">${escapeHtml(p.description)}</p>
                        <div class="project-tech">${techTags}</div>
                        <div class="project-links">
                            ${p.github_url ? `<a href="${escapeHtml(p.github_url)}" target="_blank" rel="noopener">⌥ GitHub</a>` : ''}
                            ${p.demo_url ? `<a href="${escapeHtml(p.demo_url)}" target="_blank" rel="noopener">↗ Live Demo</a>` : ''}
                        </div>
                    </div>
                </article>
            `;
        }).join('');
    }

    /* ---------- 7. PROJECT FILTERS ---------- */
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const filter = btn.dataset.filter;
            let filtered = [...allProjects];
            if (filter === 'featured') {
                filtered = allProjects.filter(p => p.featured == 1);
            } else if (filter === 'latest') {
                filtered = [...allProjects]
                    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
                    .slice(0, 3);
            }
            renderProjects(filtered);
        });
    });

    /* ---------- 8. AJAX: LOAD BLOG ---------- */
    const blogGrid = document.getElementById('blogGrid');

    async function loadBlog() {
        try {
            const res = await fetch('api/blog.php');
            if (!res.ok) throw new Error('Network error');
            const posts = await res.json();

            if (!posts.length) {
                blogGrid.innerHTML = '<p class="empty-state">// no posts yet.</p>';
                return;
            }

            blogGrid.innerHTML = posts.map((post, i) => `
                <article class="blog-card" style="animation-delay:${i * 0.06}s">
                    <span class="blog-category">${escapeHtml(post.category || 'General')}</span>
                    <h3 class="blog-title">${escapeHtml(post.title)}</h3>
                    <p class="blog-excerpt">${escapeHtml(post.excerpt || post.content.substring(0, 140) + '…')}</p>
                    <div class="blog-meta">
                        <span>${formatDate(post.created_at)}</span>
                        <span class="blog-read-more">read →</span>
                    </div>
                </article>
            `).join('');
        } catch (err) {
            blogGrid.innerHTML = `<p class="empty-state">// failed to load posts: ${err.message}</p>`;
        }
    }

    /* ---------- 9. CONTACT FORM ---------- */
    const form = document.getElementById('contactForm');
    const formResponse = document.getElementById('formResponse');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const name    = form.name.value.trim();
        const email   = form.email.value.trim();
        const subject = form.subject.value.trim();
        const message = form.message.value.trim();

        clearErrors();
        let valid = true;

        if (name.length < 2) {
            showError('name', 'Name must be at least 2 characters.');
            valid = false;
        }
        if (!isValidEmail(email)) {
            showError('email', 'Please enter a valid email address.');
            valid = false;
        }
        if (message.length < 10) {
            showError('message', 'Message must be at least 10 characters.');
            valid = false;
        }
        if (!valid) return;

        const btn = form.querySelector('button[type="submit"]');
        const btnText = btn.querySelector('.btn-text');
        const btnLoader = btn.querySelector('.btn-loader');
        btn.disabled = true;
        btnText.hidden = true;
        btnLoader.hidden = false;
        formResponse.className = 'form-response';
        formResponse.textContent = '';

        try {
            const fd = new FormData();
            fd.append('name', name);
            fd.append('email', email);
            fd.append('subject', subject);
            fd.append('message', message);

            const res = await fetch('api/contact.php', { method: 'POST', body: fd });
            const data = await res.json();

            if (data.success) {
                formResponse.className = 'form-response success';
                formResponse.textContent = '✓ ' + (data.message || 'Message sent successfully!');
                form.reset();
            } else {
                formResponse.className = 'form-response error';
                formResponse.textContent = '✗ ' + (data.message || 'Something went wrong.');
            }
        } catch (err) {
            formResponse.className = 'form-response error';
            formResponse.textContent = '✗ Network error. Please try again.';
        } finally {
            btn.disabled = false;
            btnText.hidden = false;
            btnLoader.hidden = true;
        }
    });

    // Real-time validation on blur
    ['name', 'email', 'message'].forEach(field => {
        const input = form[field];
        input.addEventListener('blur', () => {
            const val = input.value.trim();
            if (field === 'email' && val && !isValidEmail(val)) {
                showError('email', 'Invalid email format.');
            } else if (field === 'name' && val && val.length < 2) {
                showError('name', 'Too short.');
            } else if (field === 'message' && val && val.length < 10) {
                showError('message', 'Too short.');
            } else {
                clearError(field);
            }
        });
    });

    function showError(field, msg) {
        const group = form[field].closest('.form-group');
        group.classList.add('has-error');
        const err = group.querySelector('.error-msg');
        if (err) err.textContent = '// ' + msg;
    }

    function clearError(field) {
        const group = form[field].closest('.form-group');
        group.classList.remove('has-error');
        const err = group.querySelector('.error-msg');
        if (err) err.textContent = '';
    }

    function clearErrors() {
        form.querySelectorAll('.form-group').forEach(g => g.classList.remove('has-error'));
        form.querySelectorAll('.error-msg').forEach(s => s.textContent = '');
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    /* ---------- 10. UTILS ---------- */
    function escapeHtml(str) {
        if (str == null) return '';
        return String(str)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function formatDate(dateStr) {
        if (!dateStr) return '';
        const d = new Date(dateStr.replace(' ', 'T'));
        if (isNaN(d)) return dateStr;
        return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    function slugify(str) {
        return String(str)
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '')
            .substring(0, 30);
    }

    /* ---------- INIT ---------- */
    loadProjects();
    loadBlog();
})();
