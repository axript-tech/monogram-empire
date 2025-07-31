<?php
// Monogram Empire - Admin Orders API

// Include necessary files
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';
require_once '../includes/auth_check.php'; // Ensure only admins can access

$method = $_SERVER['REQUEST_METHOD'];

// --- GET Request: Fetch all orders ---
if ($method === 'GET') {
    // Basic pagination parameters
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10; // Orders per page
    $offset = ($page - 1) * $limit;

    // Get total number of orders for pagination
    $total_result = $conn->query("SELECT COUNT(id) as total FROM orders");
    $total_orders = $total_result->fetch_assoc()['total'];
    $total_pages = ceil($total_orders / $limit);

    // Fetch orders for the current page, joining with users to get customer email
    $stmt = $conn->prepare("
        SELECT 
            o.id, 
            o.order_total,
            o.status,
            o.created_at,
            u.email as customer_email
        FROM orders o
        JOIN users u ON o.user_id = u.id
        ORDER BY o.id DESC 
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }

    $stmt->close();
    $conn->close();

    send_json_response([
        'success' => true,
        'orders' => $orders,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_orders' => $total_orders
        ]
    ], 200);
}

// --- POST/PUT Request: Update an order ---
// (Logic for updating an order's status would go here)


// If the request method is not handled
send_json_response(['error' => 'Invalid request method.'], 405);
?>
