<?php
// Monogram Empire - Admin Users API

// Include necessary files
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';
require_once '../includes/auth_check.php'; // Ensure only admins can access

// This API can handle multiple request methods (GET, POST, etc.)
$method = $_SERVER['REQUEST_METHOD'];

// --- GET Request: Fetch all users ---
if ($method === 'GET') {
    // Basic pagination parameters
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10; // Users per page
    $offset = ($page - 1) * $limit;

    // Get total number of users for pagination
    $total_result = $conn->query("SELECT COUNT(id) as total FROM users");
    $total_users = $total_result->fetch_assoc()['total'];
    $total_pages = ceil($total_users / $limit);

    // Fetch users for the current page
    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, role, created_at FROM users ORDER BY id DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    $stmt->close();
    $conn->close();

    send_json_response([
        'success' => true,
        'users' => $users,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_users' => $total_users
        ]
    ], 200);
}

// --- POST Request: Create a new user ---
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Validation
    $required_fields = ['first_name', 'last_name', 'email', 'password', 'role'];
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            send_json_response(['success' => false, 'message' => 'All fields are required.'], 400);
        }
    }

    $first_name = sanitize_input($data['first_name']);
    $last_name = sanitize_input($data['last_name']);
    $email = sanitize_input($data['email']);
    $password = $data['password'];
    $role = in_array($data['role'], ['admin', 'customer']) ? $data['role'] : 'customer';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        send_json_response(['success' => false, 'message' => 'Invalid email format.'], 400);
    }
    if (strlen($password) < 8) {
        send_json_response(['success' => false, 'message' => 'Password must be at least 8 characters.'], 400);
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        send_json_response(['success' => false, 'message' => 'An account with this email already exists.'], 409);
    }
    $stmt->close();

    // Hash password and insert user
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $hashed_password, $role);
    
    if ($stmt->execute()) {
        send_json_response(['success' => true, 'message' => 'User created successfully!'], 201);
    } else {
        send_json_response(['success' => false, 'message' => 'Failed to create user.'], 500);
    }
    $stmt->close();
    $conn->close();
}


// If the request method is not handled
send_json_response(['error' => 'Invalid request method.'], 405);
