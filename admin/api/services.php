<?php
include_once '../../includes/db_connect.php';
include_once '../../includes/functions.php';

if (!is_admin()) {
    //send_json_response(['success' => false, 'message' => 'Unauthorized'], 403);
}

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

// --- Handle GET requests (Fetch Service Requests) ---
if ($method === 'GET') {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        // Fetch a single service request
        $request_id = (int)$_GET['id'];
        $query = "SELECT sr.*, u.first_name, u.last_name, u.email 
                  FROM service_requests sr 
                  JOIN users u ON sr.user_id = u.id 
                  WHERE sr.id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $service = $result->fetch_assoc();
        $stmt->close();
        if ($service) {
            send_json_response(['success' => true, 'service' => $service]);
        } else {
            send_json_response(['success' => false, 'message' => 'Service request not found.'], 404);
        }
    } else {
        // Fetch all service requests with pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $total_result = $conn->query("SELECT COUNT(*) as total FROM service_requests");
        $total_requests = $total_result->fetch_assoc()['total'];
        $total_pages = ceil($total_requests / $limit);

        $query = "SELECT sr.id, sr.tracking_id, sr.status, sr.quote_amount, sr.created_at, u.email as customer_name 
                  FROM service_requests sr 
                  JOIN users u ON sr.user_id = u.id 
                  ORDER BY sr.created_at DESC LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $requests = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        send_json_response([
            'success' => true,
            'requests' => $requests,
            'pagination' => ['current_page' => $page, 'total_pages' => $total_pages]
        ]);
    }
}

// --- Handle PUT requests (Update Service Request) ---
if ($method === 'PUT') {
    $request_id = (int)($data['id'] ?? 0);
    $status = sanitize_input($data['status'] ?? '');
    $quote_amount = !empty($data['quote_amount']) ? (float)$data['quote_amount'] : null;
    $converted_product_id = !empty($data['converted_product_id']) ? (int)$data['converted_product_id'] : null;

    if ($request_id === 0) {
        send_json_response(['success' => false, 'message' => 'Invalid request ID.'], 400);
    }
    
    // Logic for linking a completed request to a product
    if ($status === 'completed' && $converted_product_id === null) {
        // send_json_response(['success' => false, 'message' => 'Please provide a Product ID when marking a request as completed.'], 400);
    }
    
    $stmt = $conn->prepare("UPDATE service_requests SET status=?, quote_amount=?, converted_product_id=? WHERE id=?");
    $stmt->bind_param("sdii", $status, $quote_amount, $converted_product_id, $request_id);

    if ($stmt->execute()) {
        log_activity($conn, 'UPDATE_SERVICE_REQUEST', "Updated status for request ID: $request_id to '$status'");
        
        // Optional: Send email notification if status is completed
        if ($status === 'completed' && $converted_product_id) {
            // Placeholder for email sending logic
        }

        send_json_response(['success' => true, 'message' => 'Service request updated successfully.']);
    } else {
        send_json_response(['success' => false, 'message' => 'Failed to update service request.'], 500);
    }
    $stmt->close();
}


// --- Handle POST for Convert to Product ---
if ($method === 'POST' && isset($_GET['action']) && $_GET['action'] === 'convert') {
    $request_id = (int)($_GET['id'] ?? 0);
    if ($request_id === 0) {
        send_json_response(['success' => false, 'message' => 'Invalid request ID.'], 400);
    }

    // Fetch service request details to create a product from it
    $stmt = $conn->prepare("SELECT * FROM service_requests WHERE id=?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $request = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$request) {
        send_json_response(['success' => false, 'message' => 'Service request not found.'], 404);
    }

    // Create a new product based on the service request
    $product_name = "Custom: " . $request['design_name'];
    $product_description = $request['description'];
    $product_price = $request['quote_amount'] ?? 0;
    
    // You might want a default category for custom products
    $default_category_id = 1;

    $insert_stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id) VALUES (?, ?, ?, ?)");
    $insert_stmt->bind_param("ssdi", $product_name, $product_description, $product_price, $default_category_id);
    
    if ($insert_stmt->execute()) {
        $new_product_id = $conn->insert_id;
        $insert_stmt->close();

        // Update the service request status to 'completed' and link the new product
        $update_stmt = $conn->prepare("UPDATE service_requests SET status='completed', converted_product_id=? WHERE id=?");
        $update_stmt->bind_param("ii", $new_product_id, $request_id);
        $update_stmt->execute();
        $update_stmt->close();

        log_activity($conn, 'CONVERT_REQUEST_TO_PRODUCT', "Converted request ID: $request_id to new product ID: $new_product_id");
        send_json_response(['success' => true, 'message' => 'Request completed and converted to product successfully.']);
    } else {
        send_json_response(['success' => false, 'message' => 'Failed to create product from request.'], 500);
    }
}


// --- Handle DELETE requests ---
if ($method === 'DELETE') {
    $request_id = (int)($_GET['id'] ?? 0);
    if ($request_id === 0) {
        send_json_response(['success' => false, 'message' => 'Invalid request ID.'], 400);
    }

    // You may want to delete the reference image as well
    $stmt = $conn->prepare("SELECT reference_image_path FROM service_requests WHERE id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $path_result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM service_requests WHERE id = ?");
    $stmt->bind_param("i", $request_id);
    if ($stmt->execute()) {
        if ($path_result && !empty($path_result['reference_image_path']) && file_exists('../../' . $path_result['reference_image_path'])) {
            unlink('../../' . $path_result['reference_image_path']);
        }
        log_activity($conn, 'DELETE_SERVICE_REQUEST', "Deleted request ID: $request_id");
        send_json_response(['success' => true, 'message' => 'Service request deleted successfully.']);
    } else {
        send_json_response(['success' => false, 'message' => 'Failed to delete service request.'], 500);
    }
    $stmt->close();
}


$conn->close();

