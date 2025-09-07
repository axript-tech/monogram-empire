<?php
// Note: auth_check.php is now included on each page before this header
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monogram Empire - Admin</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Quicksand', sans-serif; }
        .bg-brand-dark { background-color: #1a1a1a; }
        .bg-brand-gray { background-color: #2c2c2c; }
        .text-brand-gold { color: #FFD700; }
        .border-brand-gold { border-color: #FFD700; }
        /* Custom scrollbar for sidebar */
        .custom-scrollbar::-webkit-scrollbar { width: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #2c2c2c; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #4a4a4a; border-radius: 4px; }

        /* Custom styles for styled file inputs */
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        .file-input-button {
            border: 1px solid #d1d5db;
            background-color: #f9fafb;
            color: #374151;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .file-input-button:hover {
            background-color: #f3f4f6;
        }
        .file-input-wrapper input[type=file] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
        }
        .file-input-filename {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: #6b7280;
            font-style: italic;
            display: block;
            text-align: center;
        }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Toast Notification -->
    <div id="toast-notification" class="fixed top-5 right-5 z-50 bg-green-500 text-white p-4 rounded-lg shadow-lg flex items-center hidden">
        <i id="toast-icon" class="fas fa-check-circle mr-3"></i>
        <span id="toast-message">Operation successful!</span>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmation-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm">
            <h3 class="text-lg font-bold text-brand-dark">Confirm Action</h3>
            <p class="text-gray-600 my-4">Are you sure?</p>
            <div class="flex justify-end space-x-4">
                <button id="cancel-action-btn" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Cancel</button>
                <button id="confirm-action-btn" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Confirm</button>
            </div>
        </div>
    </div>


    <div class="flex h-screen bg-gray-200">
        <?php include 'sidebar.php'; ?>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-md p-4 flex justify-between items-center">
                <h1 class="text-xl font-bold text-brand-dark">Welcome, <?php echo htmlspecialchars($_SESSION['user_first_name']); ?>!</h1>
                <a href="auth/logout.php" class="text-sm text-gray-600 hover:text-brand-dark"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
            </header>
            
            <main id="admin-main-content" class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">

