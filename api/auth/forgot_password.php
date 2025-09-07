<?php
// Note: The path is corrected to go up one level from the 'auth' folder, then another from 'api'.
include_once '../../includes/db_connect.php';
include_once '../../includes/functions.php';
include_once '../../includes/send_email.php'; // Include our reusable email function

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['email'])) {
    send_json_response(['success' => false, 'message' => 'Email is required.'], 400);
}

$email = sanitize_input($data['email']);

// Check if user exists
$stmt = $conn->prepare("SELECT id, first_name FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// To prevent user enumeration, we always send a success message.
// The actual email sending only happens if the user exists.
if ($user) {
    // Generate a secure token
    $token = bin2hex(random_bytes(32));
    $expires = date("U") + 1800; // Token expires in 30 minutes

    // Store the token in the database
    $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?");
    $stmt->bind_param("sii", $token, $expires, $user['id']);
    $stmt->execute();
    $stmt->close();

    // --- Prepare and Send Email ---
    $reset_link = "http://{$_SERVER['HTTP_HOST']}/reset-password.php?token={$token}&email=" . urlencode($email);
    
    // Get site name for email template
    $site_name_result = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'site_name'");
    $site_name = $site_name_result ? $site_name_result->fetch_assoc()['setting_value'] : 'Monogram Empire';
    $name = $site_name;

    $email_title = "Password Reset Request";
    $email_content = "
        <p>Hello " . htmlspecialchars($user['first_name']) . ",</p>
        <p>We received a request to reset the password for your account. If you did not make this request, you can safely ignore this email.</p>
        <p>To reset your password, please click the button below. This link is valid for 30 minutes.</p>
        <p style='text-align: center; margin: 30px 0;'>
            <a href='{$reset_link}' style='background-color: #FFD700; color: #1a1a1a; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Reset Your Password</a>
        </p>
        <p>If you're having trouble with the button, you can copy and paste the following URL into your browser:</p>
        <p><a href='{$reset_link}' style='color: #007bff; word-break: break-all;'>{$reset_link}</a></p>
    ";

    // Use the reusable function to send the email
    // Note: We don't need to handle the return value here, as we send a generic success message regardless.
    send_email($conn, $email, $user['first_name'], $email_title, $email_content, $name);
}

send_json_response(['success' => true, 'message' => 'If an account with that email exists, a password reset link has been sent.']);

$conn->close();

