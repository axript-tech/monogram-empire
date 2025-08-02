$(document).ready(function() {

    function showPreorderMessage(message, isSuccess) {
        const messageContainer = $('#preorder-message-container');
        const successClasses = 'bg-green-100 border-green-400 text-green-700';
        const errorClasses = 'bg-red-100 border-red-400 text-red-700';
        const messageHtml = `<div class="border px-4 py-3 rounded relative ${isSuccess ? successClasses : errorClasses}" role="alert">${message}</div>`;
        
        messageContainer.html(messageHtml).slideDown();
    }

    $('#service-request-form').on('submit', function(e) {
        e.preventDefault();
        $('#preorder-message-container').slideUp().empty();
        const submitButton = $(this).find('button[type="submit"]');
        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Submitting...');

        // Use FormData to handle file uploads
        const formData = new FormData(this);

        $.ajax({
            url: 'api/preorder/submit.php',
            method: 'POST',
            data: formData,
            processData: false, // Important for FormData
            contentType: false, // Important for FormData
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showPreorderMessage(response.message, true);
                    $('#service-request-form')[0].reset();
                    // Optionally, redirect to the tracking page
                    setTimeout(() => {
                        window.location.href = `track-preorder.php?tracking_id=${response.tracking_id}`;
                    }, 3000);
                } else {
                    showPreorderMessage(response.message, false);
                    submitButton.prop('disabled', false).text('Request a Quote');
                }
            },
            error: function(jqXHR) {
                if (jqXHR.status === 401) {
                    showPreorderMessage('You must be logged in to submit a request.', false);
                     setTimeout(() => window.location.href = 'login.php', 2000);
                } else {
                    showPreorderMessage('An unexpected error occurred. Please try again.', false);
                }
                submitButton.prop('disabled', false).text('Request a Quote');
            }
        });
    });

});
