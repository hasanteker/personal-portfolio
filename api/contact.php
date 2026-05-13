<?php
/**
 * AJAX endpoint: receive a contact-form submission and save to DB.
 * Performs server-side validation in addition to the client-side checks.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}

$name    = trim($_POST['name']    ?? '');
$email   = trim($_POST['email']   ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (mb_strlen($name) < 2) {
    jsonResponse(['success' => false, 'message' => 'Name is too short.'], 400);
}
if (!validateEmail($email)) {
    jsonResponse(['success' => false, 'message' => 'Invalid email address.'], 400);
}
if (mb_strlen($message) < 10) {
    jsonResponse(['success' => false, 'message' => 'Message is too short (min 10 chars).'], 400);
}
if (mb_strlen($name) > 100 || mb_strlen($email) > 150 || mb_strlen($subject) > 200) {
    jsonResponse(['success' => false, 'message' => 'One of the fields is too long.'], 400);
}

try {
    $stmt = $pdo->prepare(
        'INSERT INTO messages (name, email, subject, message, ip_address)
         VALUES (:name, :email, :subject, :message, :ip)'
    );
    $stmt->execute([
        ':name'    => $name,
        ':email'   => $email,
        ':subject' => $subject !== '' ? $subject : null,
        ':message' => $message,
        ':ip'      => clientIp(),
    ]);

    jsonResponse([
        'success' => true,
        'message' => 'Thank you! Your message has been saved.',
    ]);
} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Could not save message.'], 500);
}
