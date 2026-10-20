<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// Block direct access
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(403);
    echo json_encode(['ok' => false, 'message' => 'Forbidden']);
    exit;
}

// Load PHPMailer
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function respond($code, $ok, $message) {
    http_response_code($code);
    echo json_encode(['ok' => $ok, 'message' => $message]);
    exit;
}

// Sanitize inputs
function clean($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

$name    = clean($_POST["name"]    ?? "");
$email   = clean($_POST["email"]   ?? "");
$message = clean($_POST["message"] ?? "");

// Validate
if (empty($name) || empty($email) || empty($message)) {
    respond(400, false, 'Please fill in all fields.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(400, false, 'Invalid email address.');
}

// Credentials come from the environment. Never hardcode them here: on static
// hosts (GitHub Pages included) this file is served as plain text.
$host     = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
$username = getenv('SMTP_USER');
$password = getenv('SMTP_PASS');
$port     = (int) (getenv('SMTP_PORT') ?: 587);
$secure   = getenv('SMTP_SECURE') ?: 'tls';
$to       = getenv('CONTACT_TO') ?: $username;

if (!$username || !$password || !$to) {
    respond(500, false, 'Mail is not configured on this server.');
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = $host;
    $mail->SMTPAuth   = true;
    $mail->Username   = $username;
    $mail->Password   = $password;
    $mail->SMTPSecure = $secure;
    $mail->Port       = $port;

    $mail->setFrom($username, 'Portfolio Contact');
    $mail->addAddress($to);
    $mail->addReplyTo($email, $name);

    $mail->Subject = "New Portfolio Message from $name";
    $mail->Body    = "Name: $name\nEmail: $email\n\nMessage:\n$message";

    $mail->send();
    echo json_encode(['ok' => true, 'message' => 'Your message has been sent. Thank you!']);

} catch (Exception $e) {
    respond(500, false, 'Failed to send. Error: ' . $mail->ErrorInfo);
}
