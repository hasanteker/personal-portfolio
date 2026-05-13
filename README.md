# Full-Stack Web Portfolio

Personal portfolio website. Developed using **HTML5, CSS3, JavaScript, PHP, and MySQL**.
The UI/UX is designed with an IDE (Integrated Development Environment) concept in mind.

Live: <https://hasanteker.com>

---

## Technologies Used

| Layer            | Technology                               |
|------------------|------------------------------------------|
| HTML             | Semantic HTML5                           |
| CSS              | Flexbox + Grid, CSS Variables            |
| JavaScript       | Vanilla JS, Fetch API (AJAX)             |
| Server           | PHP 8 + PDO                              |
| Database         | MySQL                                    |
| Authentication   | Sessions + Cookies, bcrypt               |

---

## Features

**Public Site (`index.php`)**
- Dark / light theme (Persisted in localStorage)
- IDE-themed tab navigation
- Skills table — Rendered from MySQL via PHP
- Projects and blog — AJAX via `fetch()`
- Contact form — JS validation + AJAX submission
- "Last visit" cookie example
- Mobile responsive (sidebar overlay)

**Admin Panel (`admin/`)**
- Secure login using `password_hash` + `password_verify`
- Session management + "remember me" cookie
- Dashboard, projects CRUD, blog CRUD, messages

---

## Folder Structure

```text
portfolio/
├── index.php                 homepage
├── admin/                    admin panel (login + CRUD)
├── api/                      AJAX endpoints (returns JSON)
├── assets/css/               style.css, admin.css
├── assets/js/main.js         all client-side logic
├── config/database.php       PDO connection
├── includes/functions.php    helper functions
└── database/                 SQL export files
```

---

## Installation (Local — XAMPP)

1. Copy the folder into `htdocs/portfolio/`.
2. Navigate to <http://localhost/phpmyadmin> → **Import** → `database/portfolio.sql`.
3. Check DB credentials in `config/database.php` (uses default XAMPP settings).
4. Access the site at <http://localhost/portfolio/>.

---

## Security Notes

- All SQL queries use **PDO prepared statements** (SQL injection protection).
- All external inputs are escaped with **`htmlspecialchars()`** before rendering (XSS protection).
- Passwords are hashed with **bcrypt** (`password_hash`).
- **`session_regenerate_id`** is used upon login (session fixation protection).
- Contact form is validated on both client and server side.

---

*Web Development course final project — May 2026*
