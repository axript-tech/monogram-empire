<?php
include_once '../../includes/db_connect.php';
include_once '../../includes/functions.php';

if (!is_admin()) {
    //send_json_response(['success' => false, 'message' => 'Unauthorized'], 403);
}

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

// --- Handle GET requests (Fetch Users) ---
if ($method === 'GET') {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        // Fetch a single user
        $user_id = (int)$_GET['id'];
        $stmt = $conn->prepare("SELECT id, first_name, last_name, email, role FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        if ($user) {
            send_json_response(['success' => true, 'user' => $user]);
        } else {
            send_json_response(['success' => false, 'message' => 'User not found.'], 404);
        }
    } else {
        // Fetch all users with pagination
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $total_result = $conn->query("SELECT COUNT(*) as total FROM users");
        $total_users = $total_result->fetch_assoc()['total'];
        $total_pages = ceil($total_users / $limit);

        $stmt = $conn->prepare("SELECT id, first_name, last_name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $users = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        send_json_response([
            'success' => true,
            'users' => $users,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $total_pages,
                'total_users' => $total_users
            ]
        ]);
    }
}

// --- Handle POST requests (Create User) ---
if ($method === 'POST') {
    // These keys are consistent with the user-form in manage_users.php and admin.js
    $first_name = sanitize_input($data['first_name'] ?? '');
    $last_name = sanitize_input($data['last_name'] ?? '');
    $email = sanitize_input($data['email'] ?? '');
    $role = sanitize_input($data['role'] ?? 'customer');
    $password = $data['password'] ?? '';

    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        send_json_response(['success' => false, 'message' => 'All fields are required.'], 400);
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        send_json_response(['success' => false, 'message' => 'Invalid email format.'], 400);
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $hashed_password, $role);
    
    if ($stmt->execute()) {
        log_activity($conn, 'CREATE_USER', "Created new user: $email");
        send_json_response(['success' => true, 'message' => 'User created successfully.']);
    } else {
        send_json_response(['success' => false, 'message' => 'Failed to create user. Email may already exist.'], 500);
    }
    $stmt->close();
}

// --- Handle PUT requests (Update User) ---
if ($method === 'PUT') {
    $user_id = (int)($data['id'] ?? 0);
    $first_name = sanitize_input($data['first_name'] ?? '');
    $last_name = sanitize_input($data['last_name'] ?? '');
    $email = sanitize_input($data['email'] ?? '');
    $role = sanitize_input($data['role'] ?? '');
    $password = $data['password'] ?? '';

    if ($user_id === 0 || empty($first_name) || empty($last_name) || empty($email) || empty($role)) {
        send_json_response(['success' => false, 'message' => 'Incomplete user data provided.'], 400);
    }

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, email=?, role=?, password=? WHERE id=?");
        $stmt->bind_param("sssssi", $first_name, $last_name, $email, $role, $hashed_password, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, email=?, role=? WHERE id=?");
        $stmt->bind_param("ssssi", $first_name, $last_name, $email, $role, $user_id);
    }

    if ($stmt->execute()) {
        log_activity($conn, 'UPDATE_USER', "Updated user ID: $user_id");
        send_json_response(['success' => true, 'message' => 'User updated successfully.']);
    } else {
        send_json_response(['success' => false, 'message' => 'Failed to update user.'], 500);
    }
    $stmt->close();
}

// --- Handle DELETE requests ---
if ($method === 'DELETE') {
    $user_id = (int)($_GET['id'] ?? 0);
    if ($user_id === 0) {
        send_json_response(['success' => false, 'message' => 'Invalid user ID.'], 400);
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        log_activity($conn, 'DELETE_USER', "Deleted user ID: $user_id");
        send_json_response(['success' => true, 'message' => 'User deleted successfully.']);
    } else {
        send_json_response(['success' => false, 'message' => 'Failed to delete user.'], 500);
    }
    $stmt->close();
}

$conn->close();

