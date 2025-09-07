$(document).ready(function() {
    /**
     * Fetches and displays stats on the dashboard page.
     */
    function loadDashboardStats() {
        $.getJSON('api/dashboard.php', function(response) {
            if (response.success.success && response.success.stats) {
                const stats = response.success.stats;
                console.log(stats)
                $('#stat-total-revenue').text('₦' + parseFloat(stats.total_revenue || 0).toLocaleString());
                $('#stat-total-orders').text(stats.total_orders || 0);
                $('#stat-total-users').text(stats.total_users || 0);
                $('#stat-pending-requests').text(stats.pending_requests || 0);
            } else {
                console.error("Dashboard stats could not be loaded from the API.");
                $('#stat-total-revenue, #stat-total-orders, #stat-total-users, #stat-pending-requests').text('--');
            }
        }).fail(function() {
            console.error("Failed to fetch dashboard stats. The API endpoint may be down or returning an error.");
            $('#stat-total-revenue, #stat-total-orders, #stat-total-users, #stat-pending-requests').text('Error');
        });
    }

    /**
     * Fetches and displays the most recent activity on the dashboard.
     */
    function loadRecentActivity() {
        const tableBody = $('#recent-activity-body');
        if (!tableBody.length) return;
        
        $.getJSON('api/dashboard_activity.php', function(response) {
            tableBody.empty(); // Clear the loading message
            if (response.success && response.activities.length > 0) {
                response.activities.forEach(activity => {
                    const activityDate = new Date(activity.created_at).toLocaleString();
                    const row = `
                        <tr class="border-b">
                            <td class="py-3 px-4">${activity.admin_email || 'System'}</td>
                            <td class="py-3 px-4"><span class="bg-gray-200 text-gray-800 text-xs font-semibold px-2.5 py-0.5 rounded">${activity.action}</span></td>
                            <td class="py-3 px-4">${activity.details}</td>
                            <td class="py-3 px-4">${activityDate}</td>
                        </tr>
                    `;
                    tableBody.append(row);
                });
            } else {
                tableBody.html('<tr><td colspan="4" class="text-center py-8 text-gray-500">No recent activity found.</td></tr>');
            }
        }).fail(function() {
            tableBody.html('<tr><td colspan="4" class="text-center py-8 text-red-500">Failed to load recent activity.</td></tr>');
        });
    }

    // --- Initial Load ---
    loadDashboardStats();
    loadRecentActivity();
});
