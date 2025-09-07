<?php
include_once '../../includes/db_connect.php';
include_once '../../includes/functions.php';
include_once '../../includes/send_email.php';

if (!is_logged_in()) {
    send_json_response(['success' => false, 'message' => 'You must be logged in.'], 401);
}

$data = json_decode(file_get_contents('php://input'), true);
$reference = sanitize_input($data['reference'] ?? '');

if (empty($reference)) {
    send_json_response(['success' => false, 'message' => 'Payment reference is missing.'], 400);
}

// Fetch Paystack secret key from settings
$settings_result = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'paystack_secret_key'");
$paystack_secret_key = $settings_result ? $settings_result->fetch_assoc()['setting_value'] : '';

if (empty($paystack_secret_key)) {
    send_json_response(['success' => false, 'message' => 'Payment gateway is not configured.'], 500);
}

// --- Verify Transaction with Paystack ---
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ["authorization: Bearer " . $paystack_secret_key, "cache-control: no-cache"],
    
    // --- SSL Certificate Fix for Localhost ---
    // IMPORTANT: This line disables SSL certificate verification.
    // This is often necessary for localhost environments (like WAMP/XAMPP) that
    // don't have up-to-date certificate bundles.
    // !!! THIS IS INSECURE AND MUST BE REMOVED IN A LIVE PRODUCTION ENVIRONMENT !!!
    CURLOPT_SSL_VERIFYPEER => false,
));
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    send_json_response(['success' => false, 'message' => 'Could not connect to payment gateway.'], 500);
}

$result = json_decode($response);
// send_json_response($response);
if ($result->status && $result->data->status === 'success') {
    // --- Payment is successful, now verify the details ---
    $order_id = (int)$result->data->metadata->order_id;
    $user_id = (int)$result->data->metadata->user_id;
    $amount_paid_kobo = $result->data->amount;

    // CRITICAL: Verify amount paid matches the order total in our database
    $order_total_stmt = $conn->prepare("SELECT SUM(price) AS total FROM order_items WHERE order_id = ?");
    $order_total_stmt->bind_param("i", $order_id);
    $order_total_stmt->execute();
    $order_total = (float)$order_total_stmt->get_result()->fetch_assoc()['total'];
    $order_total_stmt->close();

    if (round($order_total * 100) != $amount_paid_kobo) {
        // Amount mismatch - flag as a failed transaction for security.
        $conn->query("UPDATE orders SET status = 'failed' WHERE id = $order_id");
        log_activity($conn, 'PAYMENT_MISMATCH', "Order ID: $order_id. Expected " . round($order_total * 100) . " kobo, got $amount_paid_kobo.");
        send_json_response(['success' => false, 'message' => 'Payment amount mismatch. Please contact support.'], 400);
    }
    
    // --- All checks passed, finalize the order ---
    $conn->query("UPDATE orders SET status = 'completed' WHERE id = $order_id AND user_id = $user_id");
    $conn->query("INSERT INTO payments (user_id, order_id, reference, amount, status) VALUES ($user_id, $order_id, '$reference', ($amount_paid_kobo / 100), 'successful')");
    
    // Send notification emails (Full logic from previous step)
    
    send_json_response(['success' => true, 'message' => 'Payment successful and order completed!']);
} else {
    send_json_response(['success' => false, 'message' => 'Payment was not successful.'], 400);
}

$conn->close();

