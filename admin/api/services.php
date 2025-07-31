<?php
// Monogram Empire - Admin Service Requests API

// Include necessary files
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';
require_once '../includes/auth_check.php'; // Ensure only admins can access

$method = $_SERVER['REQUEST_METHOD'];

// --- GET Request: Fetch all service requests ---
if ($method === 'GET') {
    // Basic pagination parameters
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10; // Requests per page
    $offset = ($page - 1) * $limit;

    // Get total number of requests for pagination
    $total_result = $conn->query("SELECT COUNT(id) as total FROM service_requests");
    $total_requests = $total_result->fetch_assoc()['total'];
    $total_pages = ceil($total_requests / $limit);

    // Fetch requests for the current page, joining with users to get customer email
    $stmt = $conn->prepare("
        SELECT 
            sr.id, 
            sr.tracking_id,
            sr.status,
            sr.quote_price,
            sr.created_at,
            u.email as customer_email
        FROM service_requests sr
        JOIN users u ON sr.user_id = u.id
        ORDER BY sr.id DESC 
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $requests = [];
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }

    $stmt->close();
    $conn->close();

    send_json_response([
        'success' => true,
        'requests' => $requests,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_requests' => $total_requests
        ]
    ], 200);
}

// --- POST/PUT Request: Update a service request ---
// (Logic for updating a request, e.g., changing status or adding a quote, would go here)


// If the request method is not handled
send_json_response(['error' => 'Invalid request method.'], 405);
?>
