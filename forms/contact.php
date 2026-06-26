<?php

// var_dump($_POST); exit;

// Block direct access
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(403);
    echo "Forbidden";
    exit;
}

// Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Sanitize inputs
function clean($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

$name    = clean($_POST["name"]    ?? "");
$email   = clean($_POST["email"]   ?? "");
$message = clean($_POST["message"] ?? "");

// Validate
if (empty($name) || empty($email) || empty($message)) {
    http_response_code(400);
    echo "Please fill in all fields.";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo "Invalid email address.";
    exit;
}

// Send via Gmail SMTP
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'aphoe4x@gmail.com';     // your Gmail
    $mail->Password   = 'maoj yovq koqo naam';   // paste your App Password here
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('aphoe4x@gmail.com', 'Portfolio Contact');
    $mail->addAddress('aphoe4x@gmail.com');
    $mail->addReplyTo($email, $name);

    $mail->Subject = "New Portfolio Message from $name";
    $mail->Body    = "Name: $name\nEmail: $email\n\nMessage:\n$message";

    $mail->send();
    echo "OK";

} catch (Exception $e) {
    http_response_code(500);
    echo "Failed to send. Error: {$mail->ErrorInfo}";
}
?>