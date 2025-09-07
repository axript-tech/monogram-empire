<?php
// This MUST be the very first line of the file.
include_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monogram Empire</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- SwiperJS for Slider -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    
    <style>
        body { font-family: 'Quicksand', sans-serif; }
        .bg-brand-dark { background-color: #1a1a1a; }
        .bg-brand-gray { background-color: #2c2c2c; }
        .text-brand-gold { color: #FFD700; }
        .border-brand-gold { border-color: #FFD700; }
        .bg-brand-gold { background-color: #FFD700; }
        .text-brand-dark { color: #1a1a1a; }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed top-5 right-5 z-50"></div>

    <!-- Confirmation Modal -->
    <div id="confirmation-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm">
            <h3 id="confirmation-title" class="text-lg font-bold text-brand-dark">Confirm Action</h3>
            <p id="confirmation-message" class="text-gray-600 my-4">Are you sure?</p>
            <div class="flex justify-end space-x-4">
                <button id="confirm-cancel-btn" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Cancel</button>
                <button id="confirm-action-btn" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Confirm</button>
            </div>
        </div>
    </div>


    <header class="bg-brand-dark text-white shadow-md sticky top-0 z-40">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold"><span class="text-brand-gold">Monogram</span>Empire</a>
            
            <nav class="hidden md:flex items-center space-x-6">
                <a href="index.php" class="hover:text-brand-gold">Home</a>
                <a href="shop.php" class="hover:text-brand-gold">Shop</a>
                <a href="request-service.php" class="hover:text-brand-gold">Custom Orders</a>
                <a href="about.php" class="hover:text-brand-gold">About</a>
                <a href="contact.php" class="hover:text-brand-gold">Contact</a>
            </nav>

            <div class="flex items-center space-x-4">
                <a href="cart.php" class="relative hover:text-brand-gold">
                    <i class="fas fa-shopping-cart fa-lg"></i>
                    <span id="cart-item-count" class="absolute -top-2 -right-2 bg-brand-gold text-brand-dark text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">0</span>
                </a>

                <?php if (is_logged_in()): ?>
                    <div class="relative">
                        <button id="profile-button" class="hover:text-brand-gold">
                            <i class="fas fa-user-circle fa-lg"></i>
                        </button>
                        <div id="profile-dropdown" class="absolute right-0 mt-2 w-48 bg-white text-brand-dark rounded-md shadow-lg py-1 z-50 hidden">
                            <a href="order-history.php" class="block px-4 py-2 text-sm hover:bg-gray-100">My History</a>
                            <a href="logout.php" class="block px-4 py-2 text-sm hover:bg-gray-100">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                     <div class="hidden md:flex items-center space-x-2">
                        <a href="login.php" class="px-4 py-2 text-sm rounded-md hover:bg-brand-gray">Log In</a>
                        <a href="register.php" class="bg-brand-gold text-brand-dark px-4 py-2 text-sm font-bold rounded-md hover:bg-yellow-300">Sign Up</a>
                    </div>
                <?php endif; ?>

                <button id="mobile-menu-button" class="md:hidden">
                    <i class="fas fa-bars fa-lg"></i>
                </button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden px-6 pt-2 pb-4 space-y-2">
            <a href="index.php" class="block hover:text-brand-gold">Home</a>
            <a href="shop.php" class="block hover:text-brand-gold">Shop</a>
            <a href="request-service.php" class="block hover:text-brand-gold">Custom Orders</a>
            <a href="about.php" class="block hover:text-brand-gold">About</a>
            <a href="contact.php" class="block hover:text-brand-gold">Contact</a>
            <?php if (!is_logged_in()): ?>
            <div class="border-t border-gray-700 pt-4 mt-4 space-y-2">
                 <a href="login.php" class="block bg-brand-gray text-center px-4 py-2 rounded-md">Log In</a>
                 <a href="register.php" class="block bg-brand-gold text-brand-dark text-center px-4 py-2 font-bold rounded-md">Sign Up</a>
            </div>
            <?php endif; ?>
        </div>
    </header>
    <main>

