<?php
// Monogram Empire - Checkout Processing API

// Include necessary files
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';

// This API endpoint should only accept POST requests.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(['error' => 'Invalid request method.'], 405);
}

// --- User Authentication Check ---
if (!is_logged_in()) {
    send_json_response(['success' => false, 'message' => 'You must be logged in to check out.'], 401);
}

$user_id = $_SESSION['user_id'];

// Get the raw POST data
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// --- Validation ---
$required_fields = ['first_name', 'last_name', 'email'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        send_json_response(['success' => false, 'message' => 'Contact information is incomplete.'], 400);
    }
}

$email = sanitize_input($data['email']);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    send_json_response(['success' => false, 'message' => 'Invalid email format.'], 400);
}

// --- Get Cart Items and Calculate Total ---
$stmt = $conn->prepare("SELECT p.id, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    send_json_response(['success' => false, 'message' => 'Your cart is empty.'], 400);
}

$cart_items = [];
$order_total = 0.00;
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $order_total += (float)$row['price'];
}
$stmt->close();

// --- Create Order in Database ---
$conn->begin_transaction();

try {
    // Generate a unique payment reference
    $payment_reference = 'ME-' . uniqid();

    // 1. Insert into the `orders` table with the reference
    $stmt = $conn->prepare("INSERT INTO orders (user_id, order_total, status, payment_reference) VALUES (?, ?, 'pending', ?)");
    $stmt->bind_param("ids", $user_id, $order_total, $payment_reference);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // 2. Insert each cart item into the `order_items` table
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, 1, ?)");
    foreach ($cart_items as $item) {
        $stmt->bind_param("iid", $order_id, $item['id'], $item['price']);
        $stmt->execute();
    }
    $stmt->close();

    // 3. Clear the user's cart
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // If all queries were successful, commit the transaction
    $conn->commit();

    // --- Prepare Data for Paystack ---
    // IMPORTANT: In a real app, get your key from a secure config file.
   // $paystack_public_key = 'pk_test_00b7531c09eed64cf7af3e4cc42efc753f45dd6d'; // Replace with your actual Paystack Public Key Axript
    $paystack_public_key = 'pk_test_585e50464c37b50e231c88570b96604d0fff0740'; // Replace with your actual Paystack Public Key

    send_json_response([
        'success' => true,
        'message' => 'Order created successfully. Initializing payment...',
        'order_id' => $order_id,
        'paystack_public_key' => $paystack_public_key,
        'email' => $email,
        'total' => $order_total * 100, // Paystack expects amount in kobo
        'reference' => $payment_reference
    ]c);

} catch (mysqli_sql_exception $exception) {
    $conn->rollback();
    send_json_response(['success' => false, 'message' => 'Failed to create order. Please try again.'], 500);
}

$conn->close();
?>
