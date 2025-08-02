<?php
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monogram Empire</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Quicksand -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- SwiperJS CSS -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Quicksand', 'sans-serif'],
                    },
                    colors: {
                        'brand-dark': '#1a1a1a',
                        'brand-gray': '#333333',
                        'brand-light-gray': '#f2f2f2',
                        'brand-gold': '#FFD700',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        body { animation: fadeIn 0.8s ease-out; font-family: 'Quicksand', sans-serif; }
        .nav-link { position: relative; transition: color 0.3s ease; }
        .nav-link::after { content: ''; position: absolute; width: 0; height: 2px; bottom: -5px; left: 50%; transform: translateX(-50%); background-color: #FFD700; transition: width 0.3s ease; }
        .nav-link:hover::after, .nav-link.active::after { width: 100%; }
        .swiper-button-next, .swiper-button-prev { color: #FFD700; }
        .swiper-pagination-bullet-active { background: #FFD700; }
    </style>
</head>
<body class="bg-brand-light-gray text-brand-dark">
    <header class="bg-brand-dark shadow-lg sticky top-0 z-50">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold text-white">
                <span class="text-brand-gold">Monogram</span>Empire
            </a>
            <div class="hidden md:flex items-center space-x-6">
                <a href="index.php" class="nav-link text-gray-300 hover:text-brand-gold active">Home</a>
                <a href="shop.php" class="nav-link text-gray-300 hover:text-brand-gold">Shop</a>
                <a href="request-service.php" class="nav-link text-gray-300 hover:text-brand-gold">Custom Orders</a>
                <a href="about.php" class="nav-link text-gray-300 hover:text-brand-gold">About</a>
                <a href="contact.php" class="nav-link text-gray-300 hover:text-brand-gold">Contact</a>
            </div>
            <div class="flex items-center space-x-4">
                <a href="cart.php" class="text-gray-300 hover:text-brand-gold relative">
                    <i class="fas fa-shopping-cart text-xl"></i>
                    <span id="cart-item-count" class="absolute -top-2 -right-2 bg-brand-gold text-brand-dark text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                </a>
                
                <?php if (is_logged_in()): ?>
                    <div class="relative">
                        <button id="profile-button" class="text-gray-300 hover:text-brand-gold" title="My Account">
                            <i class="fas fa-user-circle text-xl"></i>
                        </button>
                        <div id="profile-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-xl z-20">
                            <a href="order-history.php" class="block px-4 py-2 text-sm text-brand-dark hover:bg-brand-light-gray">My History</a>
                            <a href="logout.php" class="block px-4 py-2 text-sm text-brand-dark hover:bg-brand-light-gray">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="hidden md:flex items-center space-x-2">
                        <a href="login.php" class="text-gray-300 hover:text-brand-gold px-3 py-1">Log In</a>
                        <a href="register.php" class="bg-brand-gold text-brand-dark font-semibold px-4 py-2 rounded-full text-sm hover:bg-yellow-300 transition-colors">Sign Up</a>
                    </div>
                <?php endif; ?>

                <button id="mobile-menu-button" class="md:hidden text-gray-300 hover:text-brand-gold">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </nav>
        <div id="mobile-menu" class="hidden md:hidden bg-brand-gray">
            <a href="index.php" class="block py-2 px-4 text-sm text-white hover:bg-brand-gold hover:text-brand-dark">Home</a>
            <a href="shop.php" class="block py-2 px-4 text-sm text-white hover:bg-brand-gold hover:text-brand-dark">Shop</a>
            <a href="request-service.php" class="block py-2 px-4 text-sm text-white hover:bg-brand-gold hover:text-brand-dark">Custom Orders</a>
            <a href="about.php" class="block py-2 px-4 text-sm text-white hover:bg-brand-gold hover:text-brand-dark">About</a>
            <a href="contact.php" class="block py-2 px-4 text-sm text-white hover:bg-brand-gold hover:text-brand-dark">Contact</a>
             <?php if (is_logged_in()): ?>
                <a href="order-history.php" class="block py-2 px-4 text-sm text-white hover:bg-brand-gold hover:text-brand-dark">My History</a>
                <a href="logout.php" class="block py-2 px-4 text-sm text-white hover:bg-brand-gold hover:text-brand-dark">Logout</a>
             <?php else: ?>
                <a href="login.php" class="block py-2 px-4 text-sm text-white hover:bg-brand-gold hover:text-brand-dark">Log In</a>
                <a href="register.php" class="block py-2 px-4 text-sm text-white hover:bg-brand-gold hover:text-brand-dark">Sign Up</a>
             <?php endif; ?>
        </div>
    </header>
    <main>
