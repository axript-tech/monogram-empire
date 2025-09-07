<?php
// This MUST be the very first line of the file.
include_once 'includes/functions.php';
include_once 'includes/db_connect.php';

// --- Page Logic ---
// 1. Redirect if user is not logged in
if (!is_logged_in()) {
    redirect('login.php?redirect=checkout.php');
}

// 2. Check if cart is empty, if so, redirect to shop
$user_id = $_SESSION['user_id'];
$cart_check_stmt = $conn->prepare("SELECT COUNT(*) as item_count FROM cart WHERE user_id = ?");
$cart_check_stmt->bind_param("i", $user_id);
$cart_check_stmt->execute();
$cart_count = $cart_check_stmt->get_result()->fetch_assoc()['item_count'];
if ($cart_count == 0) {
    redirect('shop.php');
}
$cart_check_stmt->close();

// 3. Fetch cart items for the summary
$cart_items = [];
$total_amount = 0;
$items_stmt = $conn->prepare("SELECT p.name, p.price, p.image_url FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$items_stmt->bind_param("i", $user_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();
if ($items_result) {
    $cart_items = $items_result->fetch_all(MYSQLI_ASSOC);
    foreach ($cart_items as $item) {
        $total_amount += $item['price'];
    }
}
$items_stmt->close();

// 4. Pre-fill user data
$user_first_name = $_SESSION['user_first_name'] ?? '';
$user_last_name = $_SESSION['user_last_name'] ?? '';
$user_email = $_SESSION['user_email'] ?? '';


include 'includes/header.php';
?>

<!-- Main Checkout Content -->
<div class="bg-gray-50">
    <div class="container mx-auto px-6 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

            <!-- Left Column: Form & Steps -->
            <div>
                <!-- Step Indicator -->
                <div class="mb-8">
                     <div class="flex items-center">
                        <div class="flex items-center text-brand-gold relative">
                            <div class="rounded-full h-10 w-10 border-2 border-brand-gold bg-brand-gold flex items-center justify-center"><i class="fas fa-check text-white"></i></div>
                            <p class="ml-2 font-semibold">Contact</p>
                        </div>
                        <div class="flex-auto border-t-2 border-brand-gold mx-4"></div>
                        <div class="flex items-center text-brand-dark relative">
                            <div class="rounded-full h-10 w-10 border-2 border-brand-dark flex items-center justify-center">2</div>
                             <p class="ml-2 font-semibold">Payment</p>
                        </div>
                         <div class="flex-auto border-t-2 border-gray-300 mx-4"></div>
                        <div class="flex items-center text-gray-400 relative">
                            <div class="rounded-full h-10 w-10 border-2 border-gray-300 flex items-center justify-center">3</div>
                            <p class="ml-2">Complete</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-lg shadow-md">
                    <h2 class="text-2xl font-bold text-brand-dark mb-6">Contact Information</h2>
                    <div id="checkout-message-container" class="mb-4"></div>
                    <form id="checkout-form">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                                <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($user_first_name) ?>" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold">
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                                <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($user_last_name) ?>" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user_email) ?>" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold">
                        </div>
                         <div class="mt-8">
                            <button type="submit" class="w-full bg-brand-dark text-white font-bold py-4 px-8 rounded-lg hover:bg-gray-700 transition-colors flex items-center justify-center text-lg">
                                Place Order & Pay
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="bg-white p-8 rounded-lg shadow-md h-fit sticky top-24">
                <h2 class="text-2xl font-bold text-brand-dark mb-6 border-b pb-4">Order Summary</h2>
                <div class="space-y-4 max-h-64 overflow-y-auto pr-2">
                    <?php foreach ($cart_items as $item): ?>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-16 h-16 object-cover rounded-md mr-4">
                            <div>
                                <p class="font-semibold text-brand-dark"><?= htmlspecialchars($item['name']) ?></p>
                                <p class="text-sm text-gray-500">Digital Product</p>
                            </div>
                        </div>
                        <p class="font-semibold text-gray-700">&#8358;<?= number_format($item['price'], 2) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="border-t pt-4 mt-4 space-y-2">
                    <div class="flex justify-between text-lg">
                        <p class="text-gray-600">Subtotal</p>
                        <p class="font-semibold text-gray-800">&#8358;<?= number_format($total_amount, 2) ?></p>
                    </div>
                     <div class="flex justify-between text-lg font-bold">
                        <p class="text-brand-dark">Total</p>
                        <p class="text-brand-dark">&#8358;<?= number_format($total_amount, 2) ?></p>
                    </div>
                </div>
                
                <div class="mt-6 pt-6 border-t text-center text-gray-500 text-sm">
                    <p class="flex items-center justify-center">
                        <i class="fas fa-lock mr-2 text-green-500"></i> Secure Payment via Paystack
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

<?php 
$conn->close();
include 'includes/footer.php'; 
?>
<!-- Include Paystack and page-specific JS -->
<script src="https://js.paystack.co/v1/inline.js"></script>
<script src="assets/js/checkout.js"></script>

