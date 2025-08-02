<?php
// Monogram Empire - Payment Verification API

// Include necessary files
require_once '../../includes/functions.php';
require_once '../../includes/db_connect.php';

// This API endpoint should only accept POST requests.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(['error' => 'Invalid request method.'], 405);
}

// --- User Authentication Check ---
if (!is_logged_in()) {
    send_json_response(['success' => false, 'message' => 'Authentication required.'], 401);
}

$user_id = $_SESSION['user_id'];

// Get the raw POST data
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// --- Validation ---
if (empty($data['reference']) || empty($data['order_id'])) {
    send_json_response(['success' => false, 'message' => 'Payment reference and order ID are required.'], 400);
}

$payment_reference = sanitize_input($data['reference']);
$order_id = (int)$data['order_id'];

// --- Simulate Payment Gateway Verification ---
// In a real application, you would use cURL to make an API call to Paystack's verification endpoint.
// For this simulation, we'll assume the verification is successful.
$is_payment_successful = true; 

$stmt = $conn->prepare("SELECT order_total FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    send_json_response(['success' => false, 'message' => 'Order not found.'], 404);
}
$order = $result->fetch_assoc();
$amount_paid = (float)$order['order_total'];
$stmt->close();


// --- Update Database on Successful Payment ---
if ($is_payment_successful) {
    $conn->begin_transaction();
    try {
        // 1. Update the order status to 'completed'
        $update_order_stmt = $conn->prepare("UPDATE orders SET status = 'completed' WHERE id = ? AND user_id = ?");
        $update_order_stmt->bind_param("ii", $order_id, $user_id);
        $update_order_stmt->execute();
        $update_order_stmt->close();

        // 2. Create a record in the `payments` table
        $insert_payment_stmt = $conn->prepare("INSERT INTO payments (user_id, order_id, amount, reference, status) VALUES (?, ?, ?, ?, 'successful')");
        $insert_payment_stmt->bind_param("iids", $user_id, $order_id, $amount_paid, $payment_reference);
        $insert_payment_stmt->execute();
        $insert_payment_stmt->close();

        // Commit the transaction
        $conn->commit();

        send_json_response(['success' => true, 'message' => 'Payment successful and order confirmed!'], 200);

    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        send_json_response(['success' => false, 'message' => 'An error occurred while confirming your order.'], 500);
    }
} else {
    // If payment verification failed
    $stmt = $conn->prepare("UPDATE orders SET status = 'failed' WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();
    
    send_json_response(['success' => false, 'message' => 'Payment verification failed.'], 400);
}

$conn->close();
?>
