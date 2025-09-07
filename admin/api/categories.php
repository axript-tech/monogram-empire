<?php
include_once '../../includes/db_connect.php';
include_once '../../includes/functions.php';

if (!is_admin()) {
    //send_json_response(['success' => false, 'message' => 'Unauthorized'], 403);
}

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

// --- Handle GET requests ---
if ($method === 'GET') {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        // Fetch a single category
        $id = (int)$_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $category = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($category) {
            send_json_response(['success' => true, 'category' => $category]);
        } else {
            send_json_response(['success' => false, 'message' => 'Category not found.'], 404);
        }
    } else {
        // Fetch all categories with pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $total_result = $conn->query("SELECT COUNT(*) as total FROM categories");
        $total = $total_result->fetch_assoc()['total'];
        $total_pages = ceil($total / $limit);

        $stmt = $conn->prepare("SELECT * FROM categories ORDER BY name ASC LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $categories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        send_json_response([
            'success' => true,
            'categories' => $categories,
            'pagination' => ['current_page' => $page, 'total_pages' => $total_pages]
        ]);
    }
}

// --- Handle POST requests (Create) ---
if ($method === 'POST') {
    $name = sanitize_input($data['name'] ?? '');
    if (empty($name)) {
        send_json_response(['success' => false, 'message' => 'Category name is required.'], 400);
    }
    
    $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->bind_param("s", $name);
    if ($stmt->execute()) {
        log_activity($conn, 'CREATE_CATEGORY', "Created new category: $name");
        send_json_response(['success' => true, 'message' => 'Category created successfully.'], 201);
    } else {
        send_json_response(['success' => false, 'message' => 'Failed to create category.'], 500);
    }
    $stmt->close();
}

// --- Handle PUT requests (Update) ---
if ($method === 'PUT') {
    $id = (int)($data['id'] ?? 0);
    $name = sanitize_input($data['name'] ?? '');

    if ($id === 0 || empty($name)) {
        send_json_response(['success' => false, 'message' => 'Invalid data provided.'], 400);
    }

    $stmt = $conn->prepare("UPDATE categories SET name=? WHERE id=?");
    $stmt->bind_param("si", $name, $id);
    if ($stmt->execute()) {
        log_activity($conn, 'UPDATE_CATEGORY', "Updated category ID: $id to '$name'");
        send_json_response(['success' => true, 'message' => 'Category updated successfully.']);
    } else {
        send_json_response(['success' => false, 'message' => 'Failed to update category.'], 500);
    }
    $stmt->close();
}

// --- Handle DELETE requests ---
if ($method === 'DELETE') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id === 0) {
        send_json_response(['success' => false, 'message' => 'Invalid category ID.'], 400);
    }

    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        log_activity($conn, 'DELETE_CATEGORY', "Deleted category ID: $id");
        send_json_response(['success' => true, 'message' => 'Category deleted successfully.']);
    } else {
        send_json_response(['success' => false, 'message' => 'Failed to delete category. It may be in use by products.'], 500);
    }
    $stmt->close();
}

$conn->close();
