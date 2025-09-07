<?php
include_once '../../includes/db_connect.php';
include_once '../../includes/functions.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['email']) || !isset($data['password'])) {
    send_json_response([
        'success' => false, 
        'message' => 'Invalid input.'
    ], 400);
}

$email = sanitize_input($data['email']);
$password = $data['password'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    send_json_response([
        'success' => false, 
        'message' => 'Invalid email format.'
    ], 400);
}

$stmt = $conn->prepare("SELECT id, first_name, last_name, email, password, role FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($user && password_verify($password, $user['password'])) {
    // Regenerate session ID to prevent session fixation
    session_regenerate_id(true);

    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_first_name'] = $user['first_name'];
    $_SESSION['user_last_name'] = $user['last_name'];
    $_SESSION['user_email'] = $user['email'];

    send_json_response([
        'success' => true, 
        'message' => 'Login successful! Redirecting...'
    ]);
} else {
    send_json_response([
        'success' => false, 
        'message' => 'Invalid email or password.'
    ], 401);
}

$conn->close();

