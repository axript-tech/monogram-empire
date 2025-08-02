<?php 
include 'includes/functions.php';
include 'includes/header.php'; 
include 'includes/db_connect.php';

// A user must be logged in to view their order history.
if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all product orders for the current user
$orders = [];
$stmt = $conn->prepare("SELECT id, created_at, order_total, status FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
$stmt->close();

// Fetch all service requests for the current user
$service_requests = [];
$stmt = $conn->prepare("SELECT id, tracking_id, created_at, status FROM service_requests WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $service_requests[] = $row;
    }
}
$stmt->close();

$conn->close();
?>

<!-- Page Header -->
<div class="relative bg-brand-dark text-white py-20">
    <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('assets/images/hero-bg.jpg');"></div>
    <div class="relative container mx-auto px-6 text-center">
        <h1 class="text-4xl md:text-5xl font-bold" style="font-family: 'Playfair Display', serif;">My History</h1>
        <p class="text-lg text-gray-300 mt-2">Review your past orders and custom requests.</p>
    </div>
</div>

<!-- History Section with Tabs -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <!-- Tab Buttons -->
        <div class="mb-8 flex justify-center border-b">
            <button class="history-tab py-3 px-6 font-bold text-lg rounded-t-lg" data-target="product-orders">
                <i class="fas fa-box-open mr-2"></i>Product Orders
            </button>
            <button class="history-tab py-3 px-6 font-bold text-lg rounded-t-lg" data-target="custom-requests">
                <i class="fas fa-drafting-compass mr-2"></i>Custom Requests
            </button>
        </div>

        <!-- Tab Content Panels -->
        <div>
            <!-- Product Orders Panel -->
            <div id="product-orders" class="tab-panel bg-brand-light-gray p-8 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold text-brand-dark mb-6">Your Product Orders</h2>
                
                <?php if (empty($orders)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-box-open text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">You haven't placed any product orders yet.</p>
                        <a href="shop.php" class="mt-4 inline-block bg-brand-gold text-brand-dark font-bold py-2 px-6 rounded-full hover:bg-yellow-300 transition-colors">Start Shopping</a>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="text-left text-brand-dark">
                                <tr class="border-b-2 border-gray-200">
                                    <th class="py-3 px-4 font-semibold">Order ID</th>
                                    <th class="py-3 px-4 font-semibold">Date</th>
                                    <th class="py-3 px-4 font-semibold">Total</th>
                                    <th class="py-3 px-4 font-semibold">Status</th>
                                    <th class="py-3 px-4 font-semibold"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="py-4 px-4 font-semibold text-brand-dark">#ME-<?php echo $order['id']; ?></td>
                                        <td class="py-4 px-4 text-gray-600"><?php echo date("F j, Y", strtotime($order['created_at'])); ?></td>
                                        <td class="py-4 px-4 text-gray-600">&#8358;<?php echo number_format($order['order_total'], 2); ?></td>
                                        <td class="py-4 px-4">
                                            <?php 
                                                $status_class = 'bg-gray-400';
                                                if ($order['status'] === 'paid' || $order['status'] === 'completed') $status_class = 'bg-green-500';
                                                elseif ($order['status'] === 'failed') $status_class = 'bg-red-500';
                                                elseif ($order['status'] === 'pending') $status_class = 'bg-yellow-500';
                                            ?>
                                            <span class="px-3 py-1 text-xs font-semibold text-white <?php echo $status_class; ?> rounded-full"><?php echo ucfirst($order['status']); ?></span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <a href="order-details.php?id=<?php echo $order['id']; ?>" class="text-brand-gold hover:underline font-semibold">View Details</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Custom Service Requests Panel -->
            <div id="custom-requests" class="tab-panel bg-brand-light-gray p-8 rounded-lg shadow-lg" style="display: none;">
                <h2 class="text-2xl font-bold text-brand-dark mb-6">Your Custom Requests</h2>
                
                <?php if (empty($service_requests)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-drafting-compass text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">You haven't made any custom design requests yet.</p>
                        <a href="request-service.php" class="mt-4 inline-block bg-brand-gold text-brand-dark font-bold py-2 px-6 rounded-full hover:bg-yellow-300 transition-colors">Request a Design</a>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="text-left text-brand-dark">
                                <tr class="border-b-2 border-gray-200">
                                    <th class="py-3 px-4 font-semibold">Tracking ID</th>
                                    <th class="py-3 px-4 font-semibold">Date</th>
                                    <th class="py-3 px-4 font-semibold">Status</th>
                                    <th class="py-3 px-4 font-semibold"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($service_requests as $request): ?>
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="py-4 px-4 font-semibold text-brand-dark"><?php echo htmlspecialchars($request['tracking_id']); ?></td>
                                        <td class="py-4 px-4 text-gray-600"><?php echo date("F j, Y", strtotime($request['created_at'])); ?></td>
                                        <td class="py-4 px-4">
                                             <?php 
                                                $status_class = 'bg-gray-400';
                                                if ($request['status'] === 'completed') $status_class = 'bg-green-500';
                                                elseif ($request['status'] === 'cancelled') $status_class = 'bg-red-500';
                                                elseif ($request['status'] === 'pending') $status_class = 'bg-purple-500';
                                                elseif ($request['status'] === 'in_progress') $status_class = 'bg-yellow-500';
                                                elseif ($request['status'] === 'awaiting_payment') $status_class = 'bg-blue-500';
                                            ?>
                                            <span class="px-3 py-1 text-xs font-semibold text-white <?php echo $status_class; ?> rounded-full"><?php echo ucfirst(str_replace('_', ' ', $request['status'])); ?></span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <a href="track-preorder.php?tracking_id=<?php echo $request['tracking_id']; ?>" class="text-brand-gold hover:underline font-semibold">Track Status</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<!-- IMPORTANT: Include the page-specific JavaScript file AFTER the footer -->
<script src="assets/js/history.js"></script>
