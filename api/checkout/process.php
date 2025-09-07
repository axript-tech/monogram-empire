<?php
include_once '../../includes/db_connect.php';
include_once '../../includes/functions.php';


if (!is_logged_in()) {
    send_json_response(['success' => false, 'message' => 'You must be logged in to check out.'], 401);
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

// 1. Calculate the total from the cart one last time on the server
$total_stmt = $conn->prepare("SELECT SUM(p.price) AS total FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$total_stmt->bind_param("i", $user_id);
$total_stmt->execute();
$total_result = $total_stmt->get_result()->fetch_assoc();
$total_amount = (float)($total_result['total'] ?? 0);

if ($total_amount <= 0) {
    send_json_response(['success' => false, 'message' => 'Your cart is empty.'], 400);
}

// 2. Create the order in our database with a 'pending' status
$stmt = $conn->prepare("INSERT INTO orders (user_id, status) VALUES (?, 'pending')");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$order_id = $conn->insert_id;
$stmt->close();

// 3. Move items from cart to order_items
$conn->query("INSERT INTO order_items (order_id, product_id, price) SELECT $order_id, c.product_id, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = $user_id");

// 4. Clear the user's cart
$conn->query("DELETE FROM cart WHERE user_id = $user_id");

// 5. Fetch Paystack Public Key and user email
$settings_result = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'paystack_public_key'");
$paystack_public_key = $settings_result ? $settings_result->fetch_assoc()['setting_value'] : '';
$user_email = $_SESSION['user_email'];

// FIX: Add validation to ensure the Paystack key exists before proceeding.
if (empty($paystack_public_key) || strpos($paystack_public_key, 'pk_') !== 0) {
    send_json_response(['success' => false, 'message' => 'Payment gateway is not configured correctly. Please contact the site administrator.'], 500);
}


// 6. Send all necessary data to the frontend to initialize Paystack
send_json_response([
    'success' => true,
    'publicKey' => $paystack_public_key,
    'email' => $user_email,
    'amount' => round($total_amount * 100), // Convert to Kobo
    'reference' => 'ME_' . $order_id . '_' . time(),
    'orderId' => $order_id,
    'userId' => $user_id // Add the user ID to the response
]);

$conn->close();

