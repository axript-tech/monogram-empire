<?php
include_once '../../includes/db_connect.php';
include_once '../../includes/functions.php';

if (!is_admin()) {
   // send_json_response(['success' => false, 'message' => 'Unauthorized'], 403);
}

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

// --- Handle GET requests (Fetch Orders) ---
if ($method === 'GET') {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        // Fetch a single, detailed order
        $order_id = (int)$_GET['id'];
        
        // Fetch main order details and calculate total amount
        $order_query = "SELECT o.*, u.first_name, u.last_name, u.email, 
                               (SELECT SUM(price) FROM order_items WHERE order_id = o.id) AS total_amount
                        FROM orders o 
                        JOIN users u ON o.user_id = u.id 
                        WHERE o.id = ?";
        $stmt = $conn->prepare($order_query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $order_result = $stmt->get_result();
        $order = $order_result->fetch_assoc();
        $stmt->close();

        if ($order) {
            // Fetch associated order items
            $items_query = "SELECT oi.price, p.name 
                            FROM order_items oi 
                            JOIN products p ON oi.product_id = p.id 
                            WHERE oi.order_id = ?";
            $stmt = $conn->prepare($items_query);
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $items_result = $stmt->get_result();
            $order['items'] = $items_result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            
            send_json_response(['success' => true, 'order' => $order]);
        } else {
            send_json_response(['success' => false, 'message' => 'Order not found.'], 404);
        }

    } else {
        // Fetch all orders with pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $total_result = $conn->query("SELECT COUNT(*) as total FROM orders");
        $total_orders = $total_result->fetch_assoc()['total'];
        $total_pages = ceil($total_orders / $limit);

        // Updated query to calculate total_amount on the fly
        $query = "SELECT o.id, o.status, o.created_at, CONCAT(u.first_name, ' ', u.last_name) as customer_name,
                         (SELECT SUM(price) FROM order_items WHERE order_id = o.id) AS total_amount
                  FROM orders o 
                  JOIN users u ON o.user_id = u.id 
                  ORDER BY o.created_at DESC LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $orders = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        send_json_response([
            'success' => true,
            'orders' => $orders,
            'pagination' => ['current_page' => $page, 'total_pages' => $total_pages]
        ]);
    }
}

// --- Handle PUT requests (Update Order Status) ---
if ($method === 'PUT') {
    $order_id = (int)($data['id'] ?? 0);
    $status = sanitize_input($data['status'] ?? '');

    if ($order_id === 0 || empty($status)) {
        send_json_response(['success' => false, 'message' => 'Invalid data provided.'], 400);
    }

    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $order_id);

    if ($stmt->execute()) {
        log_activity($conn, 'UPDATE_ORDER_STATUS', "Updated status for order ID: $order_id to '$status'");
        send_json_response(['success' => true, 'message' => 'Order status updated successfully.']);
    } else {
        send_json_response(['success' => false, 'message' => 'Failed to update order status.'], 500);
    }
    $stmt->close();
}

$conn->close();

