<?php include 'includes/functions.php'; ?>
<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<div class="relative bg-brand-dark text-white py-20">
    <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('assets/images/hero-bg.jpg');"></div>
    <div class="relative container mx-auto px-6 text-center">
        <h1 class="text-4xl md:text-5xl font-bold" style="font-family: 'Playfair Display', serif;">Shopping Cart</h1>
        <p class="text-lg text-gray-300 mt-2">Review your selections before proceeding to checkout.</p>
    </div>
</div>

<!-- Cart Section -->
<section class="py-16 bg-white">
    <div id="cart-container" class="container mx-auto px-6">
        <div class="flex flex-col lg:flex-row gap-12">

            <!-- Cart Items -->
            <div class="lg:w-2/3">
                <div class="overflow-x-auto">
                    <table id="cart-items-table" class="min-w-full bg-white">
                        <thead class="bg-brand-light-gray text-left text-brand-dark">
                            <tr>
                                <th class="py-3 px-4 font-semibold">Product</th>
                                <th class="py-3 px-4 font-semibold">Price</th>
                                <th class="py-3 px-4 font-semibold">Subtotal</th>
                                <th class="py-3 px-4 font-semibold"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Cart items will be loaded here by JavaScript -->
                            <tr>
                                <td colspan="4" class="text-center py-12">
                                    <i class="fas fa-spinner fa-spin text-4xl text-gray-300"></i>
                                    <p class="mt-2 text-gray-500">Loading your cart...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:w-1/3">
                <div id="order-summary" class="bg-brand-light-gray p-8 rounded-lg shadow-lg">
                    <h2 class="text-2xl font-bold text-brand-dark mb-6">Order Summary</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span id="summary-subtotal">&#8358;0.00</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span>&#8358;0 (Digital)</span>
                        </div>
                        <div class="flex justify-between text-gray-600 border-b pb-4">
                            <span>VAT</span>
                            <span>&#8358;0</span>
                        </div>
                        <div class="flex justify-between font-bold text-brand-dark text-xl">
                            <span>Total</span>
                            <span id="summary-total">&#8358;0.00</span>
                        </div>
                    </div>
                    <a href="checkout.php" class="block w-full mt-8 bg-brand-gold text-brand-dark font-bold text-center py-3 px-8 rounded-full text-lg hover:bg-yellow-300 transition-transform transform hover:scale-105">
                        Proceed to Checkout
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
