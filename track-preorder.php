<?php 
include 'includes/functions.php';
include 'includes/header.php'; 
include 'includes/db_connect.php';

// A user must be logged in to track their order.
if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$tracking_id_from_url = isset($_GET['tracking_id']) ? sanitize_input($_GET['tracking_id']) : '';
$service_request = null;

// If a tracking ID is present in the URL, fetch its details
if (!empty($tracking_id_from_url)) {
    $stmt = $conn->prepare("SELECT * FROM service_requests WHERE tracking_id = ? AND user_id = ?");
    $stmt->bind_param("si", $tracking_id_from_url, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $service_request = $result->fetch_assoc();
    }
    $stmt->close();
}
$conn->close();
?>

<!-- Page Header -->
<div class="relative bg-brand-dark text-white py-20">
    <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('assets/images/hero-bg.jpg');"></div>
    <div class="relative container mx-auto px-6 text-center">
        <h1 class="text-4xl md:text-5xl font-bold" style="font-family: 'Playfair Display', serif;">Track Your Custom Order</h1>
        <p class="text-lg text-gray-300 mt-2">Enter your tracking ID to see the status of your bespoke design.</p>
    </div>
</div>

<!-- Tracking Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6 max-w-2xl">
        <div class="bg-brand-light-gray p-8 rounded-lg shadow-lg">
            <h2 class="text-3xl font-bold text-brand-dark mb-6 text-center">Check Order Status</h2>
            <form id="tracking-form" method="GET" action="track-preorder.php" class="mb-8">
                <div class="flex">
                    <input type="text" id="tracking_id" name="tracking_id" value="<?php echo htmlspecialchars($tracking_id_from_url); ?>" placeholder="Enter your tracking ID (e.g., ME-CUSTOM-XXXX)" required class="w-full px-4 py-3 bg-white border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                    <button type="submit" class="bg-brand-gold text-brand-dark font-bold px-6 py-3 rounded-r-md hover:bg-yellow-300 transition-colors">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <?php if ($service_request): ?>
            <!-- Status Display -->
            <div id="status-display" class="mt-12">
                <h3 class="text-2xl font-bold text-brand-dark mb-6">Status for #<?php echo htmlspecialchars($service_request['tracking_id']); ?></h3>
                <?php
                    $statuses = ['pending', 'in_progress', 'awaiting_payment', 'completed'];
                    $current_status_index = array_search($service_request['status'], $statuses);
                ?>
                <!-- Progress Tracker -->
                <div class="relative">
                    <div class="absolute left-0 top-3.5 w-full h-1 bg-gray-300"></div>
                    <div class="absolute left-0 top-3.5 h-1 bg-brand-gold" style="width: <?php echo ($current_status_index / (count($statuses) - 1)) * 100; ?>%;"></div>
                    <div class="flex justify-between items-center">
                        <?php foreach ($statuses as $index => $status): ?>
                        <div class="text-center z-10">
                            <div class="w-8 h-8 <?php echo ($index <= $current_status_index) ? 'bg-brand-gold' : 'bg-gray-300'; ?> rounded-full flex items-center justify-center mx-auto">
                                <i class="fas fa-check text-white"></i>
                            </div>
                            <p class="text-sm mt-2 font-semibold <?php echo ($index <= $current_status_index) ? 'text-brand-dark' : 'text-gray-500'; ?>"><?php echo ucfirst(str_replace('_', ' ', $status)); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="mt-8 bg-white p-4 rounded-md text-center text-gray-600">
                    <p><strong>Latest Update:</strong> Your request is currently "<?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $service_request['status']))); ?>".</p>
                </div>
            </div>
            <?php elseif (!empty($tracking_id_from_url)): ?>
            <!-- Not Found Message -->
            <div class="mt-12 text-center">
                <i class="fas fa-exclamation-circle text-4xl text-red-400 mb-4"></i>
                <p class="text-gray-600">No custom order found with that tracking ID for your account.</p>
            </div>
            <?php endif; ?>

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
