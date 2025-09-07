<?php
include_once '../../includes/db_connect.php';
include_once '../../includes/functions.php';

$data = json_decode(file_get_contents('php://input'), true);

// The token is a secure hash and should not be sanitized with htmlspecialchars.
$token = $data['token'] ?? ''; 
$email = sanitize_input($data['email'] ?? '');
$new_password = $data['new_password'] ?? '';

if (empty($token) || empty($email) || empty($new_password)) {
    send_json_response(['success' => false, 'message' => 'All fields are required.'], 400);
}

// 1. Find the user by their email address first (case-insensitive).
$stmt = $conn->prepare("SELECT id, reset_token, reset_token_expires FROM users WHERE LOWER(email) = LOWER(?)");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// 2. Now, securely compare the provided token with the one from the database.
// This is more robust and prevents timing attacks.
if (!$user || $user['reset_token'] === null || !hash_equals($user['reset_token'], $token)) {
    send_json_response(['success' => false, 'message' => 'Invalid token or email. Please request a new reset link.'], 400);
}

// 3. Check if the token has expired
$current_time = time();
if ($current_time > $user['reset_token_expires']) {
    // Invalidate the expired token for security
    $expire_stmt = $conn->prepare("UPDATE users SET reset_token = NULL, reset_token_expires = NULL WHERE id = ?");
    $expire_stmt->bind_param("i", $user['id']);
    $expire_stmt->execute();
    $expire_stmt->close();
    
    send_json_response(['success' => false, 'message' => 'This password reset token has expired. Please request a new one.'], 400);
}

// 4. Hash the new password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// 5. Update the user's password and nullify the reset token to prevent reuse
$stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?");
$stmt->bind_param("si", $hashed_password, $user['id']);

if ($stmt->execute()) {
    send_json_response(['success' => true, 'message' => 'Your password has been reset successfully! You can now log in.']);
} else {
    send_json_response(['success' => false, 'message' => 'Failed to update password. Please try again.'], 500);
}

$stmt->close();
$conn->close();

