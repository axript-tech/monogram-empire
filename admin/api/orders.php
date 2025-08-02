<?php
// Monogram Empire - Admin Orders API

require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';
require_once '../includes/auth_check.php';

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

// --- GET Request Logic ---
if ($method === 'GET') {
    if (isset($_GET['id'])) {
        // Fetch a single order with all details
        $order_id = (int)$_GET['id'];
        
        // Main order info
        $stmt = $conn->prepare("SELECT o.*, u.first_name, u.last_name, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();
        $stmt->close();

        if (!$order) {
            send_json_response(['success' => false, 'message' => 'Order not found.'], 404);
        }

        // Order items
        $stmt = $conn->prepare("SELECT oi.price, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $order['items'] = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        send_json_response(['success' => true, 'order' => $order], 200);

    } else {
        // Fetch all orders (paginated)
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $total_result = $conn->query("SELECT COUNT(id) as total FROM orders");
        $total_orders = $total_result->fetch_assoc()['total'];
        $total_pages = ceil($total_orders / $limit);
        $stmt = $conn->prepare("SELECT o.id, o.order_total, o.status, o.created_at, u.email as customer_email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.id DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $orders = $result->fetch_all(MYSQLI_ASSOC);
        send_json_response(['success' => true, 'orders' => $orders, 'pagination' => ['current_page' => $page, 'total_pages' => $total_pages, 'total_orders' => $total_orders]], 200);
    }
}

// --- PUT Request: Update an order's status ---
if ($method === 'PUT') {
    if (empty($data['order_id']) || empty($data['status'])) {
        send_json_response(['success' => false, 'message' => 'Order ID and status are required.'], 400);
    }
    $order_id = (int)$data['order_id'];
    $status = $data['status'];
    $allowed_statuses = ['pending', 'paid', 'completed', 'failed'];

    if (!in_array($status, $allowed_statuses)) {
        send_json_response(['success' => false, 'message' => 'Invalid status value.'], 400);
    }

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);

    if ($stmt->execute()) {
        send_json_response(['success' => true, 'message' => 'Order status updated successfully!'], 200);
    } else {
        send_json_response(['success' => false, 'message' => 'Failed to update order status.'], 500);
    }
}

$conn->close();
send_json_response(['error' => 'Invalid request method.'], 405);
