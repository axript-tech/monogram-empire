<?php
// This file should be included at the top of all secure admin pages.
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Monogram Empire</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Quicksand', 'sans-serif'] },
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
        /* Custom Scrollbar Styling for Webkit Browsers (Chrome, Safari) */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #1a1a1a; /* bg-brand-dark */
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #333333; /* bg-brand-gray */
            border-radius: 10px;
            border: 2px solid #1a1a1a; /* bg-brand-dark */
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: #FFD700; /* bg-brand-gold */
        }
        /* Toast Notification Animation */
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
        .toast-in { animation: slideInRight 0.5s forwards; }
        .toast-out { animation: slideOutRight 0.5s forwards; }
    </style>
</head>
<body class="bg-brand-light-gray">
    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed top-5 right-5 z-[100]"></div>

    <!-- Confirmation Modal -->
    <div id="confirmation-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[99] flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-sm text-center">
            <h3 id="confirmation-title" class="text-lg font-bold text-brand-dark mb-4">Are you sure?</h3>
            <p id="confirmation-message" class="text-gray-600 mb-6">This action cannot be undone.</p>
            <div class="flex justify-center space-x-4">
                <button id="confirm-cancel-btn" class="bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-full hover:bg-gray-400 transition-colors">Cancel</button>
                <button id="confirm-action-btn" class="bg-red-500 text-white font-bold py-2 px-6 rounded-full hover:bg-red-600 transition-colors">Delete</button>
            </div>
        </div>
    </div>

    <div class="flex h-screen bg-gray-200">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="flex justify-between items-center p-4 bg-white border-b-2 border-gray-200">
                <div>
                    <h1 class="text-xl font-bold text-brand-dark">Dashboard</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                    <a href="auth/logout.php" class="text-sm text-red-500 hover:underline">
                        <i class="fas fa-sign-out-alt mr-1"></i>Logout
                    </a>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-brand-light-gray p-6">
                <div class="container mx-auto">
