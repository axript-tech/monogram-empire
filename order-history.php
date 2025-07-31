<?php 
include 'includes/header.php'; 
include 'includes/db_connect.php';

// A user must be logged in to view their order history.
if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all orders for the current user
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
$conn->close();
?>

<!-- Page Header -->
<div class="relative bg-brand-dark text-white py-20">
    <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('assets/images/hero-bg.jpg');"></div>
    <div class="relative container mx-auto px-6 text-center">
        <h1 class="text-4xl md:text-5xl font-bold" style="font-family: 'Playfair Display', serif;">Order History</h1>
        <p class="text-lg text-gray-300 mt-2">Review your past orders and track their status.</p>
    </div>
</div>

<!-- Order History Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <div class="bg-brand-light-gray p-8 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold text-brand-dark mb-6">Your Orders</h2>
            
            <?php if (empty($orders)): ?>
                <!-- Message for no orders -->
                <div class="text-center py-12">
                    <i class="fas fa-box-open text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">You haven't placed any orders yet.</p>
                    <a href="shop.php" class="mt-4 inline-block bg-brand-gold text-brand-dark font-bold py-2 px-6 rounded-full hover:bg-yellow-300 transition-colors">Start Shopping</a>
                </div>
            <?php else: ?>
                <!-- Orders Table -->
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
                                            $status_class = 'bg-gray-400'; // Default
                                            if ($order['status'] === 'paid' || $order['status'] === 'completed') {
                                                $status_class = 'bg-green-500';
                                            } elseif ($order['status'] === 'failed') {
                                                $status_class = 'bg-red-500';
                                            } elseif ($order['status'] === 'pending') {
                                                $status_class = 'bg-yellow-500';
                                            }
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
    </div>
</section>

<?php include 'includes/footer.php'; ?>
