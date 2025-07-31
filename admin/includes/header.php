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
</head>
<body class="bg-brand-light-gray">
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
