<?php
// Monogram Empire - Contact Form API

// Include necessary files
require_once '../includes/functions.php';

// This API endpoint should only accept POST requests.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(['error' => 'Invalid request method.'], 405);
}

// Get the raw POST data
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// --- Validation ---
$errors = [];
$required_fields = ['name', 'email', 'subject', 'message'];

foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        $errors[] = ucfirst($field) . ' is required.';
    }
}

if (!empty($errors)) {
    send_json_response(['success' => false, 'message' => 'Missing required fields.', 'errors' => $errors], 400);
}

// Sanitize inputs
$name = sanitize_input($data['name']);
$email = sanitize_input($data['email']);
$subject = sanitize_input($data['subject']);
$message = nl2br(sanitize_input($data['message'])); // Use nl2br to preserve line breaks in HTML

// More specific validation for email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    send_json_response(['success' => false, 'message' => 'Invalid email format.'], 400);
}

// --- Send Stylish HTML Email ---
$admin_email = "admin@monogramempire.com"; // The email address that receives the contact form submissions
$email_subject = "New Contact Form Submission: " . $subject;

// Set headers for HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: Monogram Empire <no-reply@monogramempire.com>\r\n";
$headers .= "Reply-To: " . $name . " <" . $email . ">\r\n";

// HTML Email Body
$email_body = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Form Submission</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header { background-color: #1a1a1a; padding: 20px; text-align: center; }
        .header h1 { color: #FFD700; margin: 0; font-size: 24px; }
        .content { padding: 30px; color: #333333; line-height: 1.6; }
        .content h2 { color: #1a1a1a; font-size: 20px; }
        .message-box { background-color: #f9f9f9; border-left: 4px solid #FFD700; padding: 15px; margin-top: 20px; }
        .footer { background-color: #333333; color: #aaaaaa; text-align: center; padding: 15px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><span style="color: #FFD700;">Monogram</span>Empire</h1>
        </div>
        <div class="content">
            <h2>New Contact Form Submission</h2>
            <p>You have received a new message from your website contact form.</p>
            <hr style="border: none; border-top: 1px solid #eeeeee; margin: 20px 0;">
            <p><strong>From:</strong> ' . $name . '</p>
            <p><strong>Email:</strong> <a href="mailto:' . $email . '">' . $email . '</a></p>
            <p><strong>Subject:</strong> ' . $subject . '</p>
            <div class="message-box">
                <p><strong>Message:</strong></p>
                <p>' . $message . '</p>
            </div>
        </div>
        <div class="footer">
            <p>&copy; ' . date("Y") . ' Monogram Empire. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
';

// Use the mail() function to send the email.
// Note: This requires the server to be configured to send mail (e.g., via php.ini).
if (mail($admin_email, $email_subject, $email_body, $headers)) {
    send_json_response(['success' => true, 'message' => 'Thank you for your message! We will get back to you shortly.'], 200);
} else {
    send_json_response(['success' => false, 'message' => 'Sorry, there was an error sending your message. Please try again later.'], 500);
}

?>
