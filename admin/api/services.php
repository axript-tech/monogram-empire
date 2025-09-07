<?php
include_once '../../includes/db_connect.php';
include_once '../../includes/functions.php';
include_once '../../includes/send_email.php'; // For sending notifications

if (!is_admin()) {
//    send_json_response(['success' => false, 'message' => 'Unauthorized'], 403);
}

$name= 'Monogram Empire';
$email= 'info@monogramempire.com';
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
        $service = $stmt->get_result()->fetch_assoc();
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
        $total = $conn->query("SELECT COUNT(*) as total FROM service_requests")->fetch_assoc()['total'];
        $total_pages = ceil($total / $limit);

        $query = "SELECT sr.id, sr.tracking_id, sr.status, sr.quote_amount, sr.created_at, CONCAT(u.first_name, ' ', u.last_name) as customer_name
                  FROM service_requests sr JOIN users u ON sr.user_id = u.id 
                  ORDER BY sr.created_at DESC LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $requests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        send_json_response(['success' => true, 'requests' => $requests, 'pagination' => ['current_page' => $page, 'total_pages' => $total_pages]]);
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
    
    // --- New Workflow Logic ---
    if ($status === 'completed') {
        if ($converted_product_id === null) {
            send_json_response(['success' => false, 'message' => 'A Product ID is required when marking a request as completed.'], 400);
        }
        // Verify the product ID exists
        $product_stmt = $conn->prepare("SELECT name FROM products WHERE id = ?");
        $product_stmt->bind_param("i", $converted_product_id);
        $product_stmt->execute();
        $product_result = $product_stmt->get_result();
        if ($product_result->num_rows === 0) {
            send_json_response(['success' => false, 'message' => "Product with ID {$converted_product_id} does not exist."], 404);
        }
        $product = $product_result->fetch_assoc();
        $product_stmt->close();
    }
    
    $stmt = $conn->prepare("UPDATE service_requests SET status=?, quote_amount=?, converted_product_id=? WHERE id=?");
    $stmt->bind_param("sdii", $status, $quote_amount, $converted_product_id, $request_id);

    if ($stmt->execute()) {
        log_activity($conn, 'UPDATE_SERVICE_REQUEST', "Updated status for request ID: $request_id to '$status'");
        
        // --- Send Customer Notification on Completion ---
        if ($status === 'completed' && $converted_product_id) {
            // Fetch customer and site details for the email
            $customer_stmt = $conn->prepare("SELECT u.first_name, u.email FROM users u JOIN service_requests sr ON u.id = sr.user_id WHERE sr.id = ?");
            $customer_stmt->bind_param("i", $request_id);
            $customer_stmt->execute();
            $customer = $customer_stmt->get_result()->fetch_assoc();
            $customer_stmt->close();

            $site_name_result = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'site_name'");
            $site_name = $site_name_result ? $site_name_result->fetch_assoc()['setting_value'] : 'Monogram Empire';

            $product_link = "https://{$_SERVER['HTTP_HOST']}/product-details.php?id={$converted_product_id}";
            $email_subject = "Your Custom Monogram is Ready! - {$site_name}";
            $email_title = "Your Bespoke Design is Complete!";
            $email_content = "
                <p>Hello " . htmlspecialchars($customer['first_name']) . ",</p>
                <p>We're excited to let you know that your custom monogram, '<strong>" . htmlspecialchars($product['name']) . "</strong>', is now complete and has been converted into a product on our website.</p>
                <p>You can view and purchase your exclusive design by clicking the button below.</p>
                <p style='text-align: center; margin: 30px 0;'>
                    <a href='{$product_link}' style='background-color: #1a1a1a; color: #ffffff; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>View Your Custom Product</a>
                </p>
                <p>Thank you for choosing {$site_name} for your bespoke design needs!</p>
            ";

            send_email($conn, $customer['email'], $customer['first_name'], $email_subject, $email_title, $email_content,$email, $name);
        }

        send_json_response(['success' => true, 'message' => 'Service request updated successfully.']);
    } else {
        send_json_response(['success' => false, 'message' => 'Failed to update service request.'], 500);
    }
    $stmt->close();
}

// --- Handle DELETE requests ---
if ($method === 'DELETE') {
    $request_id = (int)($_GET['id'] ?? 0);
    if ($request_id === 0) {
        send_json_response(['success' => false, 'message' => 'Invalid request ID.'], 400);
    }
    $stmt = $conn->prepare("DELETE FROM service_requests WHERE id = ?");
    $stmt->bind_param("i", $request_id);
    if ($stmt->execute()) {
        log_activity($conn, 'DELETE_SERVICE_REQUEST', "Deleted request ID: $request_id");
        send_json_response(['success' => true, 'message' => 'Service request deleted successfully.']);
    } else {
        send_json_response(['success' => false, 'message' => 'Failed to delete service request.'], 500);
    }
    $stmt->close();
}

$conn->close();

