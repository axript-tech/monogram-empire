$(document).ready(function() {
    $('#checkout-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitButton = form.find('button[type="submit"]');
        const messageContainer = $('#checkout-message-container');
        const originalButtonText = submitButton.html();

        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Processing...');
        messageContainer.slideUp().empty();

        const formData = {
            first_name: $('#first_name').val(),
            last_name: $('#last_name').val(),
            email: $('#email').val()
        };

        // 1. Call our process API to create the order and get payment details
        $.ajax({
            url: 'api/checkout/process.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                console.log(response)
                if (response.success) {
                    // 2. Use the response to launch Paystack
                    const handler = PaystackPop.setup({
                        key: response.publicKey,
                        email: response.email,
                        amount: response.amount, // Amount is already in Kobo from API
                        ref: response.reference,
                        metadata: {
                            order_id: response.orderId,
                            user_id: response.userId
                        },
                        callback: function(paystackResponse) {
                            // 3. On successful payment, call our verification API
                            verifyPayment(paystackResponse.reference);
                        },
                        onClose: function() {
                            messageContainer.html('<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded" role="alert">Payment window closed. Your order is pending.</div>').slideDown();
                            submitButton.prop('disabled', false).html(originalButtonText);
                        }
                    });
                    handler.openIframe();
                } else {
                    messageContainer.html(`<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">${response.message}</div>`).slideDown();
                    submitButton.prop('disabled', false).html(originalButtonText);
                }
            },
            error: function() {
                messageContainer.html('<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">An error occurred while preparing your order.</div>').slideDown();
                submitButton.prop('disabled', false).html(originalButtonText);
            }
        });
    });

    /**
     * Sends the payment reference to our backend for secure verification.
     * @param {string} reference - The transaction reference from Paystack.
     */
    function verifyPayment(reference) {
        const messageContainer = $('#checkout-message-container');
        messageContainer.html('<div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded" role="alert">Verifying payment, please wait...</div>').slideDown();

        $.ajax({
            url: 'api/checkout/verify_payment.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ reference: reference }),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    messageContainer.html(`<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">${response.message}</div>`).slideDown();
                    // Redirect to order history after a short delay
                    setTimeout(() => {
                        window.location.href = 'order-history.php';
                    }, 3000);
                } else {
                    messageContainer.html(`<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">${response.message}</div>`).slideDown();
                }
            },
            error: function() {
                 messageContainer.html('<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">A critical error occurred while verifying your payment. Please contact support.</div>').slideDown();
            }
        });
    }
});

