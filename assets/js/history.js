$(document).ready(function() {
    // Tab switching logic for the order history page
    $('.history-tab').on('click', function(e) {
        e.preventDefault();

        // Get the target tab panel ID from the data attribute
        const target = $(this).data('target');

        // Update button styles
        $('.history-tab').removeClass('bg-brand-dark text-white').addClass('bg-gray-200 text-gray-600');
        $(this).removeClass('bg-gray-200 text-gray-600').addClass('bg-brand-dark text-white');

        // Show the target panel and hide others
        $('.tab-panel').hide();
        $('#' + target).show();
    });

    // Trigger a click on the first tab to set the initial state
    $('.history-tab').first().click();
});
