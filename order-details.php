<?php 
include 'includes/functions.php';
include 'includes/header.php'; 
include 'includes/db_connect.php';

// A user must be logged in to view order details.
if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($order_id <= 0) {
    echo "<p class='text-center py-20'>Invalid Order ID.</p>";
    include 'includes/footer.php';
    exit();
}

// --- NEW: Handle Download Feedback ---
$notification_message = '';
$is_error = true;
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'access_denied':
            $notification_message = "Access Denied. You do not have permission to download this file.";
            break;
        case 'file_not_found':
            $notification_message = "The requested file could not be found. Please contact support.";
            break;
        default:
            $notification_message = "An unknown error occurred during download.";
            break;
    }
}

// Fetch the main order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p class='text-center py-20'>Order not found.</p>";
    include 'includes/footer.php';
    exit();
}
$order = $result->fetch_assoc();
$stmt->close();

// Fetch the items associated with this order
$items_stmt = $conn->prepare("
    SELECT oi.price, p.id as product_id, p.name, p.image_url 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();
$order_items = [];
while ($row = $items_result->fetch_assoc()) {
    $order_items[] = $row;
}
$items_stmt->close();

// Fetch user info for billing details
$user_stmt = $conn->prepare("SELECT first_name, last_name, email, phone FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_info = $user_stmt->get_result()->fetch_assoc();
$user_stmt->close();

$conn->close();
?>

<!-- Page Header -->
<div class="relative bg-brand-dark text-white py-20">
    <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('assets/images/hero-bg.jpg');"></div>
    <div class="relative container mx-auto px-6 text-center">
        <h1 class="text-4xl md:text-5xl font-bold" style="font-family: 'Playfair Display', serif;">Order Details</h1>
        <p class="text-lg text-gray-300 mt-2">Order #ME-<?php echo htmlspecialchars($order['id']); ?></p>
    </div>
</div>

<!-- Order Details Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        
        <!-- NEW: Notification Area -->
        <?php if (!empty($notification_message)): ?>
        <div class="mb-6 p-4 rounded-md <?php echo $is_error ? 'bg-red-100 border border-red-400 text-red-700' : 'bg-green-100 border border-green-400 text-green-700'; ?>">
            <?php echo $notification_message; ?>
        </div>
        <?php endif; ?>

        <div class="bg-brand-light-gray p-8 rounded-lg shadow-lg">
            <div class="flex flex-col md:flex-row justify-between md:items-center mb-6 border-b pb-6">
                <div>
                    <h2 class="text-2xl font-bold text-brand-dark">Order #ME-<?php echo htmlspecialchars($order['id']); ?></h2>
                    <p class="text-gray-500">Placed on <?php echo date("F j, Y", strtotime($order['created_at'])); ?></p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="invoice.php?order_id=<?php echo $order['id']; ?>" target="_blank" class="bg-brand-dark text-white font-bold py-2 px-6 rounded-full hover:bg-brand-gray transition-colors">
                        <i class="fas fa-download mr-2"></i>Download Invoice
                    </a>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-12">
                <!-- Order Items -->
                <div class="lg:w-2/3">
                    <h3 class="text-xl font-bold text-brand-dark mb-4">Items in this Order</h3>
                    <div class="space-y-4">
                        <?php foreach ($order_items as $item): ?>
                        <div class="flex justify-between items-center bg-white p-4 rounded-md shadow-sm">
                            <div class="flex items-center">
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" class="w-16 h-16 rounded-md mr-4">
                                <div>
                                    <p class="font-semibold text-brand-dark"><?php echo htmlspecialchars($item['name']); ?></p>
                                    <p class="font-semibold text-brand-gray">&#8358;<?php echo number_format($item['price'], 2); ?></p>
                                </div>
                            </div>
                            <a href="download.php?order_id=<?php echo $order['id']; ?>&product_id=<?php echo $item['product_id']; ?>" class="bg-brand-gold text-brand-dark font-bold py-2 px-4 rounded-full hover:bg-yellow-300 transition-colors text-sm">
                                <i class="fas fa-download mr-2"></i>Download
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Billing & Summary -->
                <div class="lg:w-1/3">
                    <!-- Billing Address -->
                    <div class="mb-8">
                        <h3 class="text-xl font-bold text-brand-dark mb-4">Billing Details</h3>
                        <div class="text-gray-600 space-y-1">
                            <p><?php echo htmlspecialchars($user_info['first_name'] . ' ' . $user_info['last_name']); ?></p>
                            <p><?php echo htmlspecialchars($user_info['email']); ?></p>
                            <?php if (!empty($user_info['phone'])): ?>
                                <p><?php echo htmlspecialchars($user_info['phone']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- Order Summary -->
                    <div>
                        <h3 class="text-xl font-bold text-brand-dark mb-4">Order Summary</h3>
                        <div class="space-y-2 bg-white p-4 rounded-md shadow-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span>&#8358;<?php echo number_format($order['order_total'], 2); ?></span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Payment Method</span>
                                <span>Paystack</span>
                            </div>
                            <div class="flex justify-between font-bold text-brand-dark text-lg border-t pt-3 mt-3">
                                <span>Total</span>
                                <span>&#8358;<?php echo number_format($order['order_total'], 2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
             <div class="mt-12 text-center">
                <a href="order-history.php" class="text-brand-gold hover:underline font-semibold"><i class="fas fa-arrow-left mr-2"></i>Back to Order History</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
