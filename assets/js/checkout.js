$(document).ready(function() {

    function showCheckoutMessage(message, isSuccess) {
        const messageContainer = $('#checkout-message-container');
        const successClasses = 'bg-green-100 border-green-400 text-green-700';
        const errorClasses = 'bg-red-100 border-red-400 text-red-700';
        const messageHtml = `<div class="border px-4 py-3 rounded relative ${isSuccess ? successClasses : errorClasses}" role="alert">${message}</div>`;
        
        messageContainer.html(messageHtml).slideDown();
    }

    $('#checkout-form').on('submit', function(e) {
        e.preventDefault();
        $('#checkout-message-container').slideUp().empty();
        const submitButton = $(this).find('button[type="submit"]');
        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Processing...');

        const formData = {
            first_name: $('#first_name').val(),
            last_name: $('#last_name').val(),
            email: $('#email').val(),
            order_notes: $('#order_notes').val()
        };

        $.ajax({
            url: 'api/checkout/process.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // --- Paystack Integration ---
                    let handler = PaystackPop.setup({
                        key: response.paystack_public_key,
                        email: response.email,
                        amount: response.total,
                        ref: response.reference,
                        onClose: function(){
                            // User closed the popup
                            showCheckoutMessage('Transaction was not completed.', false);
                            submitButton.prop('disabled', false).text('Place Order & Pay');
                        },
                        callback: function(transaction){
                            // Payment was successful, now verify on the backend
                            verifyPayment(transaction.reference, response.order_id);
                        }
                    });
                    handler.openIframe();
                } else {
                    showCheckoutMessage(response.message, false);
                    submitButton.prop('disabled', false).text('Place Order & Pay');
                }
            },
            error: function() {
                showCheckoutMessage('An unexpected error occurred. Please try again.', false);
                submitButton.prop('disabled', false).text('Place Order & Pay');
            }
        });
    });

    function verifyPayment(reference, orderId) {
        $.ajax({
            url: 'api/checkout/verify_payment.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ reference: reference, order_id: orderId }),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showCheckoutMessage(response.message, true);
                    // Redirect to the order details page on success
                    setTimeout(() => {
                        window.location.href = `order-details.php?id=${orderId}`;
                    }, 2000);
                } else {
                    showCheckoutMessage(response.message, false);
                }
            },
            error: function() {
                showCheckoutMessage('An error occurred while verifying your payment.', false);
            }
        });
    }
});
