<?php
// Monogram Empire - Admin Login

// Include necessary files. Note the path change.
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';

$error_message = '';

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic validation
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $error_message = 'Both email and password are required.';
    } else {
        $email = sanitize_input($_POST['email']);
        $password = $_POST['password'];

        // Fetch admin user from the database
        $stmt = $conn->prepare("SELECT id, first_name, password FROM users WHERE email = ? AND role = 'admin'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            // Verify password
            if (password_verify($password, $admin['password'])) {
                // Password is correct, start admin session
                session_regenerate_id(true);
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['first_name'];
                
                // Redirect to the dashboard
                header("Location: ../dashboard.php");
                exit();
            } else {
                $error_message = 'Invalid email or password.';
            }
        } else {
            $error_message = 'Invalid email or password.';
        }
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Monogram Empire</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
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
<body class="bg-brand-light-gray flex items-center justify-center h-screen">
    <div class="w-full max-w-md">
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-brand-dark">
                    <span class="text-brand-gold">Monogram</span>Empire
                </h1>
                <p class="text-gray-600 mt-1">Admin Panel Login</p>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $error_message; ?></span>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-bold mb-2">Email Address</label>
                    <input type="email" id="email" name="email" required class="w-full px-4 py-3 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 font-bold mb-2">Password</label>
                    <input type="password" id="password" name="password" required class="w-full px-4 py-3 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                </div>
                <div>
                    <button type="submit" class="w-full bg-brand-dark text-white font-bold py-3 px-8 rounded-full hover:bg-brand-gray transition-colors">
                        Log In
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
