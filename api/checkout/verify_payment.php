<?php
include_once '../../includes/db_connect.php';
include_once '../../includes/functions.php';
include_once '../../includes/send_email.php';

if (!is_logged_in()) {
    send_json_response(['success' => false, 'message' => 'You must be logged in.'], 401);
}

$name = 'Monogram Empire';
$email = 'info@monogramempire.com';

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
    CURLOPT_SSL_VERIFYPEER => false,
)); 
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    send_json_response(['success' => false, 'message' => 'Could not connect to payment gateway.'], 500);
}

$result = json_decode($response);

if ($result->status && $result->data->status === 'success') {
    $order_id = (int)$result->data->metadata->order_id;
    $user_id = (int)$result->data->metadata->user_id;
    $amount_paid_kobo = $result->data->amount;

    $order_total_stmt = $conn->prepare("SELECT SUM(price) AS total FROM order_items WHERE order_id = ?");
    $order_total_stmt->bind_param("i", $order_id);
    $order_total_stmt->execute();
    $order_total = (float)$order_total_stmt->get_result()->fetch_assoc()['total'];
    $order_total_stmt->close();

    if (round($order_total * 100) != $amount_paid_kobo) {
        $conn->query("UPDATE orders SET status = 'failed' WHERE id = $order_id");
        log_activity($conn, 'PAYMENT_MISMATCH', "Order ID: $order_id. Expected " . round($order_total * 100) . " kobo, got $amount_paid_kobo.");
        send_json_response(['success' => false, 'message' => 'Payment amount mismatch. Please contact support.'], 400);
    }
    
    $conn->query("UPDATE orders SET status = 'completed' WHERE id = $order_id AND user_id = $user_id");
    $conn->query("INSERT INTO payments (user_id, order_id, reference, amount, status) VALUES ($user_id, $order_id, '$reference', ($amount_paid_kobo / 100), 'successful')");
    
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
    $items_result = $items_stmt->get_result();
    $items = $items_result->fetch_all(MYSQLI_ASSOC);
    $items_stmt->close();
    
    $settings_stmt = $conn->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('site_name', 'site_email')");
    $settings_data = [];
    while($row = $settings_stmt->fetch_assoc()) {
        $settings_data[$row['setting_key']] = $row['setting_value'];
    }
    $site_name = $settings_data['site_name'] ?? 'Monogram Empire';
    $admin_email = $settings_data['site_email'] ?? 'admin@example.com';

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
                <td style='padding: 10px; border-top: 2px solid #ddd; text-align: right;'>&#8358;" . number_format($amount_paid_kobo / 100, 2) . "</td>
            </tr>
        </table>
        <p style='text-align: center; margin: 30px 0;'>
            <a href='http://{$_SERVER['HTTP_HOST']}/order-details.php?id={$order_id}' style='background-color: #1a1a1a; color: #ffffff; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>View Your Order & Download</a>
        </p>
    ";
    send_email($conn, $customer['email'], $customer['first_name'], $customer_subject, $customer_title, $customer_content, $email, $name);

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
                <td style='padding: 10px; border-top: 2px solid #ddd; text-align: right;'>&#8358;" . number_format($amount_paid_kobo / 100, 2) . "</td>
            </tr>
        </table>
         <p style='text-align: center; margin: 30px 0;'>
            <a href='http://{$_SERVER['HTTP_HOST']}/admin/manage_orders.php' style='background-color: #1a1a1a; color: #ffffff; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>View in Admin Panel</a>
        </p>
    ";
    send_email($conn, $admin_email, 'Admin', $admin_subject, $admin_title, $admin_content, $email, $name);
    
    send_json_response(['success' => true, 'message' => 'Payment successful and order completed!']);
} else {
    send_json_response(['success' => false, 'message' => 'Payment was not successful.'], 400);
}

$conn->close();

