<?php 
// --- PHP LOGIC FIRST ---
include 'includes/functions.php';
include 'includes/db_connect.php';

// A user must be logged in to checkout.
if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items and user info for pre-filling the form
$cart_items = [];
$subtotal = 0.00;
$user_info = null;

// Get cart items
$stmt = $conn->prepare("SELECT p.id, p.name, p.price, p.image_url FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // If cart is empty, redirect to shop before any HTML is sent
    header("Location: shop.php");
    exit();
}

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $subtotal += (float)$row['price'];
}
$stmt->close();

// Get user info to pre-fill the form
$stmt = $conn->prepare("SELECT first_name, last_name, email, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user_info = $user_result->fetch_assoc();
$stmt->close();

$conn->close();

// --- NOW INCLUDE THE HEADER AND START HTML ---
include 'includes/header.php'; 
?>

<!-- Page Header -->
<div class="relative bg-brand-dark text-white py-20">
    <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('assets/images/hero-bg.jpg');"></div>
    <div class="relative container mx-auto px-6 text-center">
        <h1 class="text-4xl md:text-5xl font-bold" style="font-family: 'Playfair Display', serif;">Checkout</h1>
        <p class="text-lg text-gray-300 mt-2">Almost there. Please confirm your details to finalize the order.</p>
    </div>
</div>

<!-- Checkout Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <div class="flex flex-col lg:flex-row gap-12">

            <!-- Billing Details -->
            <div class="lg:w-2/3">
                <div class="bg-brand-light-gray p-8 rounded-lg shadow-lg">
                    <h2 class="text-2xl font-bold text-brand-dark mb-6">Contact Information</h2>
                    
                    <!-- Container for AJAX messages -->
                    <div id="checkout-message-container" class="mb-4" style="display: none;"></div>

                    <form id="checkout-form" method="POST">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="first_name" class="block text-gray-700 font-bold mb-2">First Name</label>
                                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user_info['first_name'] ?? ''); ?>" required class="w-full px-4 py-3 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                            </div>
                            <div>
                                <label for="last_name" class="block text-gray-700 font-bold mb-2">Last Name</label>
                                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user_info['last_name'] ?? ''); ?>" required class="w-full px-4 py-3 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="email" class="block text-gray-700 font-bold mb-2">Email for Delivery</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_info['email'] ?? ''); ?>" required class="w-full px-4 py-3 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                        </div>

                        <div class="mb-4">
                            <h3 class="text-xl font-bold text-brand-dark mb-4">Order Notes (Optional)</h3>
                            <textarea id="order_notes" name="order_notes" rows="4" placeholder="Notes about your order..." class="w-full px-4 py-3 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold"></textarea>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Order Summary & Payment -->
            <div class="lg:w-1/3">
                <div class="bg-brand-light-gray p-8 rounded-lg shadow-lg sticky top-24">
                    <h2 class="text-2xl font-bold text-brand-dark mb-6">Your Order</h2>
                    
                    <!-- Order Items -->
                    <div class="space-y-4 border-b pb-4">
                        <?php foreach ($cart_items as $item): ?>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" class="w-12 h-12 rounded-md mr-3">
                                <div>
                                    <p class="font-semibold text-brand-dark"><?php echo htmlspecialchars($item['name']); ?></p>
                                </div>
                            </div>
                            <p class="font-semibold text-brand-gray">&#8358;<?php echo number_format($item['price'], 2); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Totals -->
                    <div class="space-y-2 py-4">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span>&#8358;<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div class="flex justify-between font-bold text-brand-dark text-xl border-t pt-4 mt-2">
                            <span>Total</span>
                            <span>&#8358;<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <p class="text-sm text-gray-600 mb-4">
                            By placing your order, you agree to our <a href="terms.php" class="text-brand-gold hover:underline">Terms & Conditions</a> and <a href="terms.php#privacy" class="text-brand-gold hover:underline">Privacy Policy</a>.
                        </p>
                        <!-- This button now submits the form via JavaScript -->
                        <button type="submit" form="checkout-form" class="w-full bg-brand-gold text-brand-dark font-bold py-3 px-8 rounded-full text-lg hover:bg-yellow-300 transition-transform transform hover:scale-105">
                            Place Order & Pay
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<!-- Paystack Inline JS -->
<script src="https://js.paystack.co/v1/inline.js"></script>
<!-- IMPORTANT: Include the page-specific JavaScript file AFTER the footer -->
<script src="assets/js/checkout.js"></script>
