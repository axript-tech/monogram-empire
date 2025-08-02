<?php
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';

// A user must be logged in to view an invoice.
if (!is_logged_in()) {
    die("Authentication required.");
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($order_id <= 0) {
    die("Invalid Order ID.");
}

// Fetch order, user, and item details securely
$stmt = $conn->prepare("SELECT o.*, u.first_name, u.last_name, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ? AND o.user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Order not found or you do not have permission to view this invoice.");
}
$order = $result->fetch_assoc();
$stmt->close();

$items_stmt = $conn->prepare("SELECT oi.price, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();
$order_items = $items_result->fetch_all(MYSQLI_ASSOC);
$items_stmt->close();
$conn->close();

// Determine invoice status for display
$status_text = 'Unpaid';
$status_color = 'bg-yellow-500'; // Default to unpaid
if ($order['status'] === 'paid' || $order['status'] === 'completed') {
    $status_text = 'Paid';
    $status_color = 'bg-green-500';
} elseif ($order['status'] === 'failed') {
    $status_text = 'Failed';
    $status_color = 'bg-red-500';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #ME-<?php echo htmlspecialchars($order['id']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Quicksand', sans-serif; }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white p-10 shadow-lg rounded-lg">
        <header class="flex justify-between items-start pb-6 border-b">
            <div>
                <h1 class="text-3xl font-bold text-gray-800"><span class="text-yellow-500">Monogram</span>Empire</h1>
                <p class="text-gray-500">123 Fashion Avenue, Lagos, Nigeria</p>
            </div>
            <div class="text-right">
                <h2 class="text-2xl font-bold uppercase text-gray-700">Invoice</h2>
                <p class="text-gray-500">#ME-<?php echo htmlspecialchars($order['id']); ?></p>
                <p class="text-gray-500">Date: <?php echo date("F j, Y", strtotime($order['created_at'])); ?></p>
                <div class="mt-2">
                    <span class="text-white text-sm font-bold uppercase py-1 px-3 rounded-full <?php echo $status_color; ?>">
                        <?php echo $status_text; ?>
                    </span>
                </div>
            </div>
        </header>

        <section class="mt-8 grid grid-cols-2 gap-8">
            <div>
                <h3 class="font-bold text-gray-600">Bill To:</h3>
                <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></p>
                <p class="text-gray-600"><?php echo htmlspecialchars($order['email']); ?></p>
            </div>
            <div class="text-right">
                 <h3 class="font-bold text-gray-600">Payment Method:</h3>
                 <p class="text-gray-800">Paystack</p>
                 <p class="text-gray-800">Ref: <?php echo htmlspecialchars($order['payment_reference']); ?></p>
            </div>
        </section>

        <section class="mt-8">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 text-left font-bold text-gray-600">Item</th>
                        <th class="p-3 text-right font-bold text-gray-600">Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                    <tr class="border-b">
                        <td class="p-3 text-gray-800"><?php echo htmlspecialchars($item['name']); ?></td>
                        <td class="p-3 text-right text-gray-800">&#8358;<?php echo number_format($item['price'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="mt-8 text-right">
            <div class="w-full sm:w-1/2 ml-auto">
                <div class="flex justify-between text-gray-700">
                    <span class="font-semibold">Subtotal:</span>
                    <span>&#8358;<?php echo number_format($order['order_total'], 2); ?></span>
                </div>
                <div class="flex justify-between text-gray-700 mt-2">
                    <span class="font-semibold">VAT (0%):</span>
                    <span>&#8358;0.00</span>
                </div>
                <div class="flex justify-between text-gray-800 font-bold text-xl mt-4 border-t pt-4">
                    <span>Total:</span>
                    <span>&#8358;<?php echo number_format($order['order_total'], 2); ?></span>
                </div>
            </div>
        </section>
        
        <footer class="mt-12 text-center text-gray-500 text-sm">
            <p>Thank you for your purchase!</p>
            <button onclick="window.print()" class="no-print mt-4 bg-yellow-500 text-black font-bold py-2 px-6 rounded-full hover:bg-yellow-600 transition-colors">
                <i class="fas fa-print mr-2"></i>Print Invoice
            </button>
        </footer>
    </div>
</body>
</html>
