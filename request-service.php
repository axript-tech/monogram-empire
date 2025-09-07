<?php
// This MUST be the very first line of the file to avoid "headers already sent" errors.
include_once 'includes/functions.php';
include_once 'includes/db_connect.php';

// Pre-fill user data if they are logged in.
$user_first_name = is_logged_in() && isset($_SESSION['user_first_name']) ? htmlspecialchars($_SESSION['user_first_name']) : '';
$user_last_name = is_logged_in() && isset($_SESSION['user_last_name']) ? htmlspecialchars($_SESSION['user_last_name']) : '';
$user_email = is_logged_in() && isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : '';

include 'includes/header.php';
?>

<div class="bg-gray-50">
    <!-- Page Header -->
    <div class="bg-cover bg-brand-dark text-white py-20 bg-center py-24" style="background-image: url('https://images.unsplash.com/photo-1549060279-7f1699b5918e?q=80&w=2070&auto=format&fit=crop');">
        <div class="container mx-auto text-center">
            <h1 class="text-4xl lg:text-5xl font-bold text-white tracking-tight">Bespoke Monogram Service</h1>
            <p class="mt-4 text-lg text-gray-200">Let us craft a unique design, just for you.</p>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="container mx-auto px-6 py-16">
        <!-- Main Form Container (Visible by default) -->
        <div id="request-form-container">
            <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 items-start">
                <!-- How It Works Section -->
                <div class="space-y-6">
                    <h2 class="text-3xl font-bold text-brand-dark mb-4">How It Works</h2>
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 h-12 w-12 rounded-full bg-brand-gold text-brand-dark flex items-center justify-center font-bold text-xl">1</div>
                        <div>
                            <h3 class="font-bold text-lg">Submit Your Vision</h3>
                            <p class="text-gray-600">Fill out the form with your ideas, text, and style preferences. The more detail, the better!</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 h-12 w-12 rounded-full bg-brand-gold text-brand-dark flex items-center justify-center font-bold text-xl">2</div>
                        <div>
                            <h3 class="font-bold text-lg">Receive a Quote</h3>
                            <p class="text-gray-600">Our designers will review your request and provide a no-obligation quote within 24-48 hours.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 h-12 w-12 rounded-full bg-brand-gold text-brand-dark flex items-center justify-center font-bold text-xl">3</div>
                        <div>
                            <h3 class="font-bold text-lg">Creation & Delivery</h3>
                            <p class="text-gray-600">Once approved, we'll create your bespoke monogram and deliver the digital files upon completion.</p>
                        </div>
                    </div>
                </div>

                <!-- Form Section -->
                <div class="bg-white p-8 rounded-lg shadow-lg">
                    <h2 class="text-2xl font-bold text-brand-dark mb-6">Request a Quote</h2>
                    <div id="preorder-message-container" class="mb-4" style="display: none;"></div>
                    <form id="preorder-form" enctype="multipart/form-data">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                                <input type="text" id="first_name" name="first_name" value="<?= $user_first_name ?>" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold">
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                                <input type="text" id="last_name" name="last_name" value="<?= $user_last_name ?>" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" id="email" name="email" value="<?= $user_email ?>" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold">
                        </div>
                        <div class="mt-4">
                            <label for="monogram_text" class="block text-sm font-medium text-gray-700">Text for Monogram (e.g., "J.D.", "The Smiths")</label>
                            <input type="text" id="monogram_text" name="monogram_text" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold">
                        </div>
                        <div class="mt-4">
                            <label for="style_preference" class="block text-sm font-medium text-gray-700">Style Preference</label>
                            <select id="style_preference" name="style_preference" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold">
                                <option>Classic & Elegant</option>
                                <option>Modern & Minimalist</option>
                                <option>Vintage & Ornate</option>
                                <option>Bold & Graphic</option>
                                <option>Designer's Choice</option>
                            </select>
                        </div>
                        <div class="mt-4">
                            <label for="additional_details" class="block text-sm font-medium text-gray-700">Additional Details (Colors, symbols, etc.)</label>
                            <textarea id="additional_details" name="additional_details" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold"></textarea>
                        </div>
                        <div class="mt-4">
                            <label for="inspiration_file" class="block text-sm font-medium text-gray-700">Inspiration File (Optional, 2MB Max)</label>
                            <input type="file" id="inspiration_file" name="inspiration_file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-gold file:text-brand-dark hover:file:bg-yellow-300">
                        </div>
                        <div class="mt-6">
                            <button type="submit" class="w-full bg-brand-dark text-white font-bold py-3 px-6 rounded-lg hover:bg-gray-700 transition-colors flex items-center justify-center">
                                <span class="button-text">Request a Quote</span>
                                <i class="fas fa-spinner fa-spin ml-2 button-spinner" style="display: none;"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Success Message Container (Hidden by default) -->
        <div id="success-container" style="display: none;" class="text-center max-w-2xl mx-auto bg-white p-12 rounded-lg shadow-xl">
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6">
                <i class="fas fa-check-circle text-5xl text-green-500"></i>
            </div>
            <h2 class="text-3xl font-bold text-brand-dark mb-4">Request Submitted!</h2>
            <p class="text-gray-600 mb-6">Thank you for your interest. Our design team will review your request and get back to you with a quote shortly. Your unique tracking ID is:</p>
            <div class="bg-gray-100 text-brand-dark font-mono text-2xl font-bold py-3 px-6 rounded-lg inline-block mb-8" id="success-tracking-id"></div>
            <div class="flex justify-center space-x-4">
                <a href="#" id="success-track-link" class="bg-brand-dark text-white font-bold py-3 px-8 rounded-lg hover:bg-gray-700 transition-colors">Track My Request</a>
                <button id="make-another-request-btn" class="bg-gray-200 text-brand-dark font-bold py-3 px-8 rounded-lg hover:bg-gray-300 transition-colors">Make Another Request</button>
            </div>
        </div>
    </div>
</div>


<?php include 'includes/footer.php'; ?>


<script src="assets/js/preorder.js"></script>