<?php
// Monogram Empire - Admin Products API

// Include necessary files
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';
require_once '../includes/auth_check.php'; // Ensure only admins can access

$method = $_SERVER['REQUEST_METHOD'];

// --- GET Request: Fetch all products ---
if ($method === 'GET') {
    // Basic pagination parameters
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10; // Products per page
    $offset = ($page - 1) * $limit;

    // Get total number of products for pagination
    $total_result = $conn->query("SELECT COUNT(id) as total FROM products");
    $total_products = $total_result->fetch_assoc()['total'];
    $total_pages = ceil($total_products / $limit);

    // Fetch products for the current page, joining with categories to get the category name
    $stmt = $conn->prepare("
        SELECT 
            p.id, 
            p.name, 
            p.price, 
            p.image_url, 
            c.name as category_name 
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        ORDER BY p.id DESC 
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    $stmt->close();
    $conn->close();

    send_json_response([
        'success' => true,
        'products' => $products,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_products' => $total_products
        ]
    ], 200);
}

// --- POST Request: Create a new product ---
// (Logic for creating a product, including file upload for the image, would go here)

// --- PUT Request: Update a product ---
// (Logic for updating a product would go here)

// --- DELETE Request: Delete a product ---
// (Logic for deleting a product would go here)


// If the request method is not handled
send_json_response(['error' => 'Invalid request method.'], 405);
?>
