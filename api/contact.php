<?php
include_once '../includes/db_connect.php';
include_once '../includes/functions.php';
include_once '../includes/send_email.php'; // Include our reusable email function

// Get form data
$data = json_decode(file_get_contents('php://input'), true);
$name = sanitize_input($data['name'] ?? '');
$email = sanitize_input($data['email'] ?? '');
$subject = sanitize_input($data['subject'] ?? '');
$message = sanitize_input($data['message'] ?? '');

// Validation
if (empty($name) || empty($email) || empty($subject) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    send_json_response(['success' => false, 'message' => 'Please fill in all fields with valid information.'], 400);
}

// Fetch site settings
$settings = [];
$result = $conn->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('site_email', 'site_name')");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}
$site_name = $settings['site_name'] ?? 'Monogram Empire';
$to_email = $settings['site_email'] ?? 'your-default-email@example.com';

// Prepare just the content for the email, not the full template
$email_title = "New Contact Form Message";
$email_content = "
    <p>You have received a new message through your website's contact form.</p>
    <table style='width: 100%; border-collapse: collapse; margin-top: 20px;'>
        <tr style='border-bottom: 1px solid #eee;'><td style='padding: 10px; font-weight: bold; color: #555;'>Name:</td><td style='padding: 10px;'>{$name}</td></tr>
        <tr style='border-bottom: 1px solid #eee;'><td style='padding: 10px; font-weight: bold; color: #555;'>Email:</td><td style='padding: 10px;'>{$email}</td></tr>
        <tr style='border-bottom: 1px solid #eee;'><td style='padding: 10px; font-weight: bold; color: #555;'>Subject:</td><td style='padding: 10px;'>{$subject}</td></tr>
        <tr><td style='padding: 10px; font-weight: bold; color: #555; vertical-align: top;'>Message:</td><td style='padding: 10px;'><p style='margin:0;'>" . nl2br($message) . "</p></td></tr>
    </table>
";

// Use the reusable function to send the email
if (send_email($conn, $to_email, $site_name, $subject, $email_title, $email_content, $email, $name)) {
    send_json_response(['success' => true, 'message' => 'Thank you! Your message has been sent.']);
} else {
    send_json_response(['success' => false, 'message' => 'Sorry, there was an error sending your message. Please try again later.'], 500);
}

$conn->close();

