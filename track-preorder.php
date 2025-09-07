<?php
// This MUST be the very first line of the file.
include_once 'includes/functions.php';
include_once 'includes/db_connect.php';

$request_details = null;
$error_message = '';
$tracking_id_from_url = '';

if (isset($_GET['tracking_id'])) {
    $tracking_id_from_url = sanitize_input($_GET['tracking_id']);
    if (!is_logged_in()) {
        redirect('login.php?redirect=track-preorder.php&tracking_id=' . urlencode($tracking_id_from_url));
    }

    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT * FROM service_requests WHERE tracking_id = ? AND user_id = ?");
    $stmt->bind_param("si", $tracking_id_from_url, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $request_details = $result->fetch_assoc();
    } else {
        $error_message = "No custom request found with that tracking ID for your account.";
    }
    $stmt->close();
}

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="bg-gray-100 py-16">
    <div class="container mx-auto px-6 text-center">
        <h1 class="text-4xl font-bold text-brand-dark">Track Your Custom Order</h1>
        <p class="text-gray-600 mt-2">Enter your tracking ID to see the status of your bespoke design.</p>
    </div>
</div>

<!-- Main Content -->
<div class="container mx-auto px-10 py-16">
    <div class="max-w-2xl mx-auto bg-white p-16 rounded-lg shadow-lg">
        <form method="GET" action="track-preorder.php" class="mb-8">
            <label for="tracking_id" class="block text-sm font-medium text-gray-700 mb-2">Tracking ID</label>
            <div class="flex">
                <input type="text" id="tracking_id" name="tracking_id" value="<?= htmlspecialchars($tracking_id_from_url) ?>" placeholder="e.g., ME-ABC123XYZ" required class="flex-grow px-4 py-3 bg-white border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                <button type="submit" class="bg-brand-dark text-white font-bold px-6 py-3 rounded-r-md hover:bg-gray-700 transition-colors">
                    <i class="fas fa-search"></i> Track
                </button>
            </div>
        </form>

        <?php if ($request_details) : ?>
            <?php
            $status = $request_details['status']; // e.g., pending, in_progress, completed
           // $quote = $request_details['quote_amount'];
            ?>
            <div>
                <h2 class="text-2xl font-bold text-brand-dark mb-6">Request Status: <span class="capitalize text-brand-gold"><?= str_replace('_', ' ', htmlspecialchars($status)) ?></span></h2>

                <!-- Status Tracker -->
                <div class="mb-12">
                    <div class="flex items-center">
                        <!-- Step 1: Request Received -->
                        <div class="flex items-center text-brand-gold relative">
                            <div class="rounded-full transition duration-500 ease-in-out h-12 w-12 border-2 border-brand-gold bg-brand-gold flex items-center justify-center">
                                <i class="fas fa-file-alt text-xl text-white"></i>
                            </div>
                            <div class="absolute top-0 -ml-10 text-center mt-16 w-32 text-xs font-medium uppercase text-brand-gold">Request Received</div>
                        </div>
                        <div class="flex-auto border-t-2 transition duration-500 ease-in-out <?= in_array($status, ['in_progress', 'completed']) ? 'border-brand-gold' : 'border-gray-300' ?>"></div>

                        <!-- Step 2: In Progress -->
                        <div class="flex items-center <?= in_array($status, ['in_progress', 'completed']) ? 'text-brand-gold' : 'text-gray-500' ?> relative">
                            <div class="rounded-full transition duration-500 ease-in-out h-12 w-12 border-2 <?= in_array($status, ['in_progress', 'completed']) ? 'border-brand-gold bg-brand-gold' : 'border-gray-300' ?> flex items-center justify-center">
                                <i class="fas fa-pencil-ruler text-xl <?= in_array($status, ['in_progress', 'completed']) ? 'text-white' : '' ?>"></i>
                            </div>
                            <div class="absolute top-0 -ml-10 text-center mt-16 w-32 text-xs font-medium uppercase <?= in_array($status, ['in_progress', 'completed']) ? 'text-brand-gold' : 'text-gray-500' ?>">In Progress</div>
                        </div>
                        <div class="flex-auto border-t-2 transition duration-500 ease-in-out <?= $status === 'completed' ? 'border-brand-gold' : 'border-gray-300' ?>"></div>
                        
                        <!-- Step 3: Completed -->
                        <div class="flex items-center <?= $status === 'completed' ? 'text-brand-gold' : 'text-gray-500' ?> relative">
                            <div class="rounded-full transition duration-500 ease-in-out h-12 w-12 border-2 <?= $status === 'completed' ? 'border-brand-gold bg-brand-gold' : 'border-gray-300' ?> flex items-center justify-center">
                                <i class="fas fa-check-circle text-xl <?= $status === 'completed' ? 'text-white' : '' ?>"></i>
                            </div>
                            <div class="absolute top-0 -ml-10 text-center mt-16 w-32 text-xs font-medium uppercase <?= $status === 'completed' ? 'text-brand-gold' : 'text-gray-500' ?>">Completed</div>
                        </div>
                    </div>
                </div>


                <!-- Request Details -->
                <div class="border-t pt-6">
                    <h3 class="font-bold text-lg mb-4">Request Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Tracking ID</p>
                            <p class="font-semibold text-brand-dark"><?= htmlspecialchars($request_details['tracking_id']) ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500">Date Submitted</p>
                            <p class="font-semibold text-brand-dark"><?= date('F j, Y', strtotime($request_details['created_at'])) ?></p>
                        </div>
                        <div class="col-span-full">
                            <p class="text-gray-500">Requested Monogram</p>
                            <p class="font-semibold text-brand-dark"><?= htmlspecialchars($request_details['design_name']) ?></p>
                        </div>
                         <div class="col-span-full">
                            <p class="text-gray-500">Quoted Amount</p>
                            <p class="font-semibold text-brand-dark text-lg">
                                <?//= $quote ? format_currency($quote) : 'Awaiting Quote' ?>
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        <?php elseif ($error_message) : ?>
            <div class="text-center text-red-600 bg-red-100 border border-red-400 p-4 rounded-lg">
                <p><?= htmlspecialchars($error_message) ?></p>
            </div>
        <?php else : ?>
            <div class="text-center text-gray-500">
                <p>Please enter your tracking ID above to see your request status.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

