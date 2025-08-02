<?php
// Monogram Empire - Admin Service Requests API

require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';
require_once '../includes/auth_check.php';

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

// --- GET Request Logic ---
if ($method === 'GET') {
    if (isset($_GET['id'])) {
        // Fetch a single service request
        $request_id = (int)$_GET['id'];
        $stmt = $conn->prepare("SELECT sr.id, sr.tracking_id, sr.status, sr.quote_price, sr.details, sr.created_at, u.email as customer_email FROM service_requests sr JOIN users u ON sr.user_id = u.id WHERE sr.id = ?");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($request = $result->fetch_assoc()) {
            send_json_response(['success' => true, 'request' => $request], 200);
        } else {
            send_json_response(['success' => false, 'message' => 'Service request not found.'], 404);
        }
    } else {
        // Fetch all service requests (paginated)
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $total_result = $conn->query("SELECT COUNT(id) as total FROM service_requests");
        $total_requests = $total_result->fetch_assoc()['total'];
        $total_pages = ceil($total_requests / $limit);
        $stmt = $conn->prepare("SELECT sr.id, sr.tracking_id, sr.status, sr.quote_price, sr.created_at, u.email as customer_email FROM service_requests sr JOIN users u ON sr.user_id = u.id ORDER BY sr.id DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $requests = $result->fetch_all(MYSQLI_ASSOC);
        send_json_response(['success' => true, 'requests' => $requests, 'pagination' => ['current_page' => $page, 'total_pages' => $total_pages, 'total_requests' => $total_requests]], 200);
    }
}

// --- PUT Request: Update a service request ---
if ($method === 'PUT') {
    if (empty($data['request_id'])) {
        send_json_response(['success' => false, 'message' => 'Request ID is required.'], 400);
    }
    $request_id = (int)$data['request_id'];
    $status = $data['status'];
    $quote_price = !empty($data['quote_price']) ? (float)$data['quote_price'] : null;

    $allowed_statuses = ['pending', 'in_progress', 'awaiting_payment', 'completed', 'cancelled'];
    if (!in_array($status, $allowed_statuses)) {
        send_json_response(['success' => false, 'message' => 'Invalid status value.'], 400);
    }

    $stmt = $conn->prepare("UPDATE service_requests SET status = ?, quote_price = ? WHERE id = ?");
    $stmt->bind_param("sdi", $status, $quote_price, $request_id);

    if ($stmt->execute()) {
        send_json_response(['success' => true, 'message' => 'Service request updated successfully!'], 200);
    } else {
        send_json_response(['success' => false, 'message' => 'Failed to update service request.'], 500);
    }
}

// --- DELETE Request: Delete a service request ---
if ($method === 'DELETE') {
    if (empty($data['request_id'])) {
        send_json_response(['success' => false, 'message' => 'Request ID is required.'], 400);
    }
    $request_id = (int)$data['request_id'];

    // Note: You might want to add logic to delete associated uploaded files from the server here.

    $stmt = $conn->prepare("DELETE FROM service_requests WHERE id = ?");
    $stmt->bind_param("i", $request_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            send_json_response(['success' => true, 'message' => 'Service request deleted successfully.'], 200);
        } else {
            send_json_response(['success' => false, 'message' => 'Service request not found.'], 404);
        }
    } else {
        send_json_response(['success' => false, 'message' => 'Failed to delete service request.'], 500);
    }
}

$conn->close();
send_json_response(['error' => 'Invalid request method.'], 405);
