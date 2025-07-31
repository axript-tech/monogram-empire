<?php include 'includes/header.php'; 
// Placeholder for fetching order details based on ID from URL
$order_id = htmlspecialchars($_GET['id'] ?? '10521');
?>

<!-- Page Header -->
<div class="relative bg-brand-dark text-white py-20">
    <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('assets/images/hero-bg.jpg');"></div>
    <div class="relative container mx-auto px-6 text-center">
        <h1 class="text-4xl md:text-5xl font-bold" style="font-family: 'Playfair Display', serif;">Order Details</h1>
        <p class="text-lg text-gray-300 mt-2">Order #ME-<?php echo $order_id; ?></p>
    </div>
</div>

<!-- Order Details Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <div class="bg-brand-light-gray p-8 rounded-lg shadow-lg">
            <div class="flex flex-col md:flex-row justify-between md:items-center mb-6 border-b pb-6">
                <div>
                    <h2 class="text-2xl font-bold text-brand-dark">Order #ME-<?php echo $order_id; ?></h2>
                    <p class="text-gray-500">Placed on July 22, 2025</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="#" class="bg-brand-dark text-white font-bold py-2 px-6 rounded-full hover:bg-brand-gray transition-colors">
                        <i class="fas fa-download mr-2"></i>Download Invoice
                    </a>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-12">
                <!-- Order Items -->
                <div class="lg:w-2/3">
                    <h3 class="text-xl font-bold text-brand-dark mb-4">Items in this Order</h3>
                    <div class="space-y-4">
                        <!-- Item 1 -->
                        <div class="flex justify-between items-center bg-white p-4 rounded-md shadow-sm">
                            <div class="flex items-center">
                                <img src="https://placehold.co/80x80/f2f2f2/1a1a1a?text=D1" class="w-16 h-16 rounded-md mr-4">
                                <div>
                                    <p class="font-semibold text-brand-dark">Victorian Crest</p>
                                    <p class="font-semibold text-brand-gray">&#8358;15,000</p>
                                </div>
                            </div>
                            <a href="#" class="bg-brand-gold text-brand-dark font-bold py-2 px-4 rounded-full hover:bg-yellow-300 transition-colors text-sm">
                                <i class="fas fa-download mr-2"></i>Download
                            </a>
                        </div>
                        <!-- Item 2 -->
                        <div class="flex justify-between items-center bg-white p-4 rounded-md shadow-sm">
                            <div class="flex items-center">
                                <img src="https://placehold.co/80x80/e0e0e0/1a1a1a?text=D2" class="w-16 h-16 rounded-md mr-4">
                                <div>
                                    <p class="font-semibold text-brand-dark">Art Deco Initial</p>
                                    <p class="font-semibold text-brand-gray">&#8358;12,500</p>
                                </div>
                            </div>
                             <a href="#" class="bg-brand-gold text-brand-dark font-bold py-2 px-4 rounded-full hover:bg-yellow-300 transition-colors text-sm">
                                <i class="fas fa-download mr-2"></i>Download
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Billing & Summary -->
                <div class="lg:w-1/3">
                    <!-- Billing Address -->
                    <div class="mb-8">
                        <h3 class="text-xl font-bold text-brand-dark mb-4">Billing Address</h3>
                        <div class="text-gray-600 space-y-1">
                            <p>John Doe</p>
                            <p>123 Fashion Avenue</p>
                            <p>Victoria Island, Lagos</p>
                            <p>Nigeria</p>
                            <p>contact@monogramempire.com</p>
                            <p>+234 801 234 5678</p>
                        </div>
                    </div>
                    <!-- Order Summary -->
                    <div>
                        <h3 class="text-xl font-bold text-brand-dark mb-4">Order Summary</h3>
                        <div class="space-y-2 bg-white p-4 rounded-md shadow-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span>&#8358;27,500</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Payment Method</span>
                                <span>Paystack</span>
                            </div>
                            <div class="flex justify-between font-bold text-brand-dark text-lg border-t pt-3 mt-3">
                                <span>Total</span>
                                <span>&#8358;27,500</span>
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
