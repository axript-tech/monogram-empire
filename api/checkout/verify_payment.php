<?php
include_once '../../includes/db_connect.php';
include_once '../../includes/functions.php';
include_once '../../includes/send_email.php'; // Include our reusable email function

if (!is_logged_in()) {
    send_json_response(['success' => false, 'message' => 'You must be logged in to verify a payment.'], 401);
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
    CURLOPT_HTTPHEADER => [
        "authorization: Bearer " . $paystack_secret_key,
        "cache-control: no-cache"
    ],
));
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    send_json_response(['success' => false, 'message' => 'Payment verification failed.'], 500);
}

$result = json_decode($response);
$name = "Monogram Empire";

if ($result->status && $result->data->status === 'success') {
    // --- Payment is successful, update our database ---
    $order_id = (int)$result->data->metadata->order_id;
    $user_id = (int)$result->data->metadata->user_id;
    $amount_paid = ($result->data->amount / 100); // Paystack returns amount in kobo

    // Update order status to 'completed'
    $stmt = $conn->prepare("UPDATE orders SET status = 'completed' WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $stmt->close();
    
    // Create a record in the payments table
    $stmt = $conn->prepare("INSERT INTO payments (user_id, order_id, reference, amount, type, status) VALUES (?, ?, ?, ?, 'order', 'successful')");
    $stmt->bind_param("iisd", $user_id, $order_id, $reference, $amount_paid);
    $stmt->execute();
    $stmt->close();

    // --- Send Notification Emails ---
    // Fetch data needed for emails
    $user_stmt = $conn->prepare("SELECT first_name, email FROM users WHERE id = ?");
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $customer = $user_stmt->get_result()->fetch_assoc();
    $user_stmt->close();

    $items_stmt = $conn->prepare("SELECT p.name, oi.price FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
    $items_stmt->bind_param("i", $order_id);
    $items_stmt->execute();
    $items = $items_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $items_stmt->close();
    
    $settings_stmt = $conn->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('site_name', 'site_email')");
    $settings = $settings_stmt->fetch_all(MYSQLI_ASSOC);
    $site_name = $settings[0]['setting_value'];
    $admin_email = $settings[1]['setting_value'];

    // 1. Send Order Receipt to Customer
    $customer_subject = "Your Order Confirmation from {$site_name} (#{$order_id})";
    $customer_title = "Thank You for Your Order!";
    $items_html = "";
    foreach ($items as $item) {
        $items_html .= "<tr><td style='padding: 10px; border-bottom: 1px solid #eee;'>{$item['name']}</td><td style='padding: 10px; border-bottom: 1px solid #eee; text-align: right;'>&#8358;" . number_format($item['price'], 2) . "</td></tr>";
    }
    $customer_content = "
        <p>Hello " . htmlspecialchars($customer['first_name']) . ",</p>
        <p>Your payment was successful and your order is complete. You can now download your purchased designs from your order history.</p>
        <h3 style='border-top: 1px solid #eee; padding-top: 20px; margin-top: 20px;'>Order Summary (#{$order_id})</h3>
        <table style='width: 100%;'>
            {$items_html}
            <tr style='font-weight: bold;'>
                <td style='padding: 10px; border-top: 2px solid #ddd;'>Total</td>
                <td style='padding: 10px; border-top: 2px solid #ddd; text-align: right;'>&#8358;" . number_format($amount_paid, 2) . "</td>
            </tr>
        </table>
        <p style='text-align: center; margin: 30px 0;'>
            <a href='http://{$_SERVER['HTTP_HOST']}/order-details.php?id={$order_id}' style='background-color: #1a1a1a; color: #ffffff; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>View Your Order & Download</a>
        </p>
    ";
    send_email($conn, $customer['email'], $customer['first_name'], $customer_subject, $customer_title, $customer_content, $name);

    // 2. Send New Order Notification to Admin
    $admin_subject = "New Order Notification (#{$order_id})";
    $admin_title = "You Have a New Order!";
    $admin_content = "
        <p>A new order has been placed and paid for on your website.</p>
        <h3>Order Details (#{$order_id})</h3>
        <p><strong>Customer:</strong> " . htmlspecialchars($customer['first_name']) . " (" . htmlspecialchars($customer['email']) . ")</p>
        <table style='width: 100%;'>
            {$items_html}
            <tr style='font-weight: bold;'>
                <td style='padding: 10px; border-top: 2px solid #ddd;'>Total Paid</td>
                <td style='padding: 10px; border-top: 2px solid #ddd; text-align: right;'>&#8358;" . number_format($amount_paid, 2) . "</td>
            </tr>
        </table>
         <p style='text-align: center; margin: 30px 0;'>
            <a href='http://{$_SERVER['HTTP_HOST']}/admin/manage_orders.php' style='background-color: #1a1a1a; color: #ffffff; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>View in Admin Panel</a>
        </p>
    ";
    send_email($conn, $admin_email, 'Admin', $admin_subject, $admin_title, $admin_content, $name);

    send_json_response(['success' => true, 'message' => 'Payment successful and order completed!']);
} else {
    send_json_response(['success' => false, 'message' => 'Payment verification failed.'], 400);
}

$conn->close();

