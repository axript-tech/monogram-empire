<?php
// Monogram Empire - Admin Payments API

// Include necessary files
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';
require_once '../includes/auth_check.php'; // Ensure only admins can access

$method = $_SERVER['REQUEST_METHOD'];

// --- GET Request: Fetch all payments ---
if ($method === 'GET') {
    // Basic pagination parameters
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10; // Payments per page
    $offset = ($page - 1) * $limit;

    // Get total number of payments for pagination
    $total_result = $conn->query("SELECT COUNT(id) as total FROM payments");
    $total_payments = $total_result->fetch_assoc()['total'];
    $total_pages = ceil($total_payments / $limit);

    // Fetch payments for the current page, joining with users to get customer email
    $stmt = $conn->prepare("
        SELECT 
            p.id, 
            p.reference,
            p.amount,
            p.status,
            p.paid_at,
            p.order_id,
            p.service_request_id,
            u.email as customer_email
        FROM payments p
        JOIN users u ON p.user_id = u.id
        ORDER BY p.id DESC 
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $payments = [];
    while ($row = $result->fetch_assoc()) {
        // Determine the type of transaction for easier display
        if ($row['order_id']) {
            $row['type'] = 'Product Order';
            $row['related_id'] = '#ME-' . $row['order_id'];
        } elseif ($row['service_request_id']) {
            $row['type'] = 'Service Request';
            // You might need another query to get the tracking_id from the service_request_id
            $row['related_id'] = '#SR-' . $row['service_request_id'];
        } else {
            $row['type'] = 'Unknown';
            $row['related_id'] = 'N/A';
        }
        $payments[] = $row;
    }

    $stmt->close();
    $conn->close();

    send_json_response([
        'success' => true,
        'payments' => $payments,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_payments' => $total_payments
        ]
    ], 200);
}

// If the request method is not handled
send_json_response(['error' => 'Invalid request method.'], 405);
?>
