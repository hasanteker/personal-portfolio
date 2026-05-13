-- ============================================
-- Portfolio Database - SQL Export
-- ============================================
-- Description: MySQL schema for the Full-Stack Portfolio project.
-- Usage: Import via phpMyAdmin or `mysql -u root -p portfolio < portfolio.sql`
-- ============================================

CREATE DATABASE IF NOT EXISTS `portfolio`
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

USE `portfolio`;

-- --------------------------------------------
-- Table: users (admin accounts)
-- --------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin: username = admin, password = admin123
-- The hash below is a bcrypt hash for 'admin123' (compatible with PHP password_verify).
INSERT INTO `users` (`username`, `password`, `email`) VALUES
('admin', '$2y$10$8HGq8ZFNU3jdbSQ5spIUM.U3wPMAZVKykEAwe.Ahi/jl.m6khk.Ea', 'admin@portfolio.local');

-- --------------------------------------------
-- Table: projects
-- --------------------------------------------
DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(150) NOT NULL,
    `description` TEXT NOT NULL,
    `image` VARCHAR(255) DEFAULT 'assets/img/project-default.svg',
    `tech_stack` VARCHAR(255) DEFAULT NULL,
    `github_url` VARCHAR(255) DEFAULT NULL,
    `demo_url` VARCHAR(255) DEFAULT NULL,
    `featured` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `projects` (`title`, `description`, `tech_stack`, `github_url`, `demo_url`, `featured`) VALUES
('Password Strength Analyzer', 'A real-time password strength checker that evaluates entropy, detects common patterns (dictionary words, keyboard walks, repeated characters), and gives suggestions to harden the password. Useful for understanding what makes a password actually secure.', 'JavaScript, HTML5, CSS3', 'https://github.com/yourusername', '#', 1),
('SecureNotes — Encrypted Notes App', 'A web-based notes application where each note is encrypted with AES-256 before it ever leaves the browser. The server only ever sees ciphertext. Built to learn how end-to-end encryption is implemented in practice.', 'PHP, MySQL, JavaScript, Web Crypto API', 'https://github.com/yourusername', '#', 1),
('SQL Injection Demo Lab', 'An intentionally vulnerable login form built side-by-side with a secured version. Visitors can try classic injection payloads on the unsafe form and see exactly what changes when prepared statements are used.', 'PHP, MySQL, HTML5', 'https://github.com/yourusername', '#', 1),
('PortPeek — TCP Port Scanner', 'A lightweight TCP port scanner written in Python. Supports custom port ranges, banner grabbing, and parallel scanning with threads. Built as a learning exercise in network programming.', 'Python, Sockets, Threading', 'https://github.com/yourusername', '#', 0),
('XSS Playground', 'A safe sandbox for experimenting with cross-site scripting payloads. Includes a vulnerable demo page and a secure version using proper output encoding so you can compare the two.', 'JavaScript, PHP', 'https://github.com/yourusername', '#', 0),
('CTF Writeups Archive', 'My personal log of solved Capture-The-Flag challenges — web exploitation, crypto, forensics. Organized by category and difficulty.', 'Markdown, Jekyll', 'https://github.com/yourusername', '#', 0);

-- --------------------------------------------
-- Table: blog_posts
-- --------------------------------------------
DROP TABLE IF EXISTS `blog_posts`;
CREATE TABLE `blog_posts` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(200) NOT NULL,
    `content` TEXT NOT NULL,
    `excerpt` VARCHAR(300) DEFAULT NULL,
    `image` VARCHAR(255) DEFAULT 'assets/img/blog-default.svg',
    `category` VARCHAR(50) DEFAULT 'General',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `blog_posts` (`title`, `excerpt`, `content`, `category`) VALUES
('Why MD5 Should Never Touch Your Passwords', 'Hashing is not encryption — and using fast hashes like MD5 for passwords is a recipe for disaster.', 'When I started learning about authentication, I thought hashing a password with MD5 was "enough". It turns out, MD5 was never designed for passwords. It is blazing fast, which is exactly what an attacker needs. With a modern GPU, billions of MD5 hashes can be computed every second, making brute force trivial. In this post I walk through what I learned about password hashing — why bcrypt, scrypt and Argon2 exist, what salting actually does, and how PHP password_hash() works under the hood.', 'Web Security'),
('SQL Injection Explained by Building One', 'The fastest way to understand SQL injection is to write a vulnerable login form and attack it yourself.', 'In my web security class we covered SQL injection in theory, but it did not really click until I built a vulnerable version myself. In this writeup I share the experiment: a simple login page using string concatenation, the payload that bypasses authentication, and the one-line fix using prepared statements. There is no substitute for hands-on practice.', 'Web Security'),
('My First CTF: Lessons From Picking Locks', 'I solved my first beginner CTF and learned more in a weekend than I did in a semester.', 'A friend dragged me into picoCTF and I was completely lost for the first hour. Then something clicked. CTF (Capture The Flag) challenges teach you to think like an attacker — to look at a system not as "how does this work?" but "how could this break?" In this post I cover the three challenges that finally made it click, and why I think every developer should try one.', 'CTF'),
('Understanding XSS by Reading Bug Reports', 'You learn cross-site scripting faster by reading real bug reports than by reading textbooks.', 'HackerOne and Bugcrowd are full of public XSS reports. Reading them taught me more than any chapter on input validation ever did. In this post I break down three reports — a reflected XSS via search params, a stored XSS in a comment field, and a DOM-based XSS via innerHTML — and explain what went wrong and how each was fixed.', 'Web Security'),
('Setting Up a Kali Linux VM for Learning', 'A short guide to building a safe lab environment for security experiments.', 'If you want to play with security tools without breaking your main machine, a Kali Linux VM is the standard answer. In this post I share my setup — VirtualBox configuration, networking modes, snapshots, and the first ten tools I always install. Aimed at total beginners.', 'Linux');

-- --------------------------------------------
-- Table: messages (contact form submissions)
-- --------------------------------------------
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `subject` VARCHAR(200) DEFAULT NULL,
    `message` TEXT NOT NULL,
    `is_read` TINYINT(1) DEFAULT 0,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------
-- Table: skills
-- --------------------------------------------
DROP TABLE IF EXISTS `skills`;
CREATE TABLE `skills` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    `level` INT(3) NOT NULL DEFAULT 50,
    `category` VARCHAR(50) DEFAULT 'General',
    `icon` VARCHAR(10) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `skills` (`name`, `level`, `category`, `icon`) VALUES
('HTML5',       85, 'Frontend',   'H5'),
('CSS3',        80, 'Frontend',   'C3'),
('JavaScript',  80, 'Frontend',   'JS'),
('PHP',         75, 'Backend',    'PHP'),
('MySQL',       75, 'Database',   'SQL'),
('Python',      70, 'Scripting',  'PY'),
('Linux',       75, 'OS',         'LNX'),
('Networking',  65, 'Security',   'NET'),
('Web Security',70, 'Security',   'SEC'),
('Git',         70, 'Tools',      'GIT');
