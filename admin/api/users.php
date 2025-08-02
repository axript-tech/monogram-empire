<?php
// Monogram Empire - Admin Users API

// Include necessary files
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';
require_once '../includes/auth_check.php'; // Ensure only admins can access

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

// --- GET Request Logic ---
if ($method === 'GET') {
    // Check if a specific user ID is requested
    if (isset($_GET['id'])) {
        $user_id = (int)$_GET['id'];
        $stmt = $conn->prepare("SELECT id, first_name, last_name, email, role FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($user = $result->fetch_assoc()) {
            send_json_response(['success' => true, 'user' => $user], 200);
        } else {
            send_json_response(['success' => false, 'message' => 'User not found.'], 404);
        }
        $stmt->close();
    } else {
        // Fetch all users (paginated)
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $total_result = $conn->query("SELECT COUNT(id) as total FROM users");
        $total_users = $total_result->fetch_assoc()['total'];
        $total_pages = ceil($total_users / $limit);

        $stmt = $conn->prepare("SELECT id, first_name, last_name, email, role, created_at FROM users ORDER BY id DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $users = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        send_json_response([
            'success' => true,
            'users' => $users,
            'pagination' => ['current_page' => $page, 'total_pages' => $total_pages, 'total_users' => $total_users]
        ], 200);
    }
}

// --- POST Request: Create a new user ---
if ($method === 'POST') {
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

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        send_json_response(['success' => false, 'message' => 'An account with this email already exists.'], 409);
    }
    $stmt->close();

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $hashed_password, $role);
    
    if ($stmt->execute()) {
        send_json_response(['success' => true, 'message' => 'User created successfully!'], 201);
    } else {
        send_json_response(['success' => false, 'message' => 'Failed to create user.'], 500);
    }
    $stmt->close();
}

// --- PUT Request: Update an existing user ---
if ($method === 'PUT') {
    if (empty($data['user_id'])) {
        send_json_response(['success' => false, 'message' => 'User ID is required.'], 400);
    }

    $user_id = (int)$data['user_id'];
    $first_name = sanitize_input($data['first_name']);
    $last_name = sanitize_input($data['last_name']);
    $email = sanitize_input($data['email']);
    $role = in_array($data['role'], ['admin', 'customer']) ? $data['role'] : 'customer';
    $password = $data['password'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        send_json_response(['success' => false, 'message' => 'This email is already in use by another account.'], 409);
    }
    $stmt->close();

    if (!empty($password)) {
        if (strlen($password) < 8) {
            send_json_response(['success' => false, 'message' => 'Password must be at least 8 characters.'], 400);
        }
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, password = ?, role = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $first_name, $last_name, $email, $hashed_password, $role, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, role = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $first_name, $last_name, $email, $role, $user_id);
    }

    if ($stmt->execute()) {
        send_json_response(['success' => true, 'message' => 'User updated successfully!'], 200);
    } else {
        send_json_response(['success' => false, 'message' => 'Failed to update user.'], 500);
    }
    $stmt->close();
}

// --- DELETE Request: Delete a user ---
if ($method === 'DELETE') {
    if (empty($data['user_id'])) {
        send_json_response(['success' => false, 'message' => 'User ID is required.'], 400);
    }
    $user_id = (int)$data['user_id'];

    // Prevent an admin from deleting themselves
    if ($user_id === $_SESSION['admin_id']) {
        send_json_response(['success' => false, 'message' => 'You cannot delete your own account.'], 403);
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            send_json_response(['success' => true, 'message' => 'User deleted successfully.'], 200);
        } else {
            send_json_response(['success' => false, 'message' => 'User not found.'], 404);
        }
    } else {
        send_json_response(['success' => false, 'message' => 'Failed to delete user.'], 500);
    }
    $stmt->close();
}


$conn->close();
// If the request method is not handled
send_json_response(['error' => 'Invalid request method.'], 405);

