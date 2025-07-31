$(document).ready(function() {

    // --- Modal Control ---
    const userModal = $('#user-modal');
    
    // Show modal
    $('#add-user-btn').on('click', function() {
        $('#modal-title').text('Add New User');
        $('#user-form')[0].reset(); // Clear form fields
        $('#user_id').val(''); // Ensure user ID is empty
        userModal.removeClass('hidden');
    });

    // Hide modal
    $('#cancel-btn, #user-modal').on('click', function(e) {
        if (e.target.id === 'user-modal' || e.target.id === 'cancel-btn') {
            userModal.addClass('hidden');
        }
    });
    // Stop propagation for clicks inside the modal content
    $('#user-modal > div').on('click', function(e) {
        e.stopPropagation();
    });


    /**
     * Handles submission of the Add/Edit User form.
     */
    $('#user-form').on('submit', function(e) {
        e.preventDefault();
        
        const userData = {
            first_name: $('#first_name').val(),
            last_name: $('#last_name').val(),
            email: $('#email').val(),
            password: $('#password').val(),
            role: $('#role').val(),
            user_id: $('#user_id').val()
        };

        // Determine if it's a create or update action
        const isUpdate = userData.user_id !== '';
        const apiUrl = isUpdate ? `api/users.php?id=${userData.user_id}` : 'api/users.php';
        const apiMethod = isUpdate ? 'PUT' : 'POST';

        // For "create", password is required. For "update", it's optional.
        if (!isUpdate && !userData.password) {
            alert('Password is required for new users.');
            return;
        }

        $.ajax({
            url: apiUrl,
            method: apiMethod,
            contentType: 'application/json',
            data: JSON.stringify(userData),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    userModal.addClass('hidden');
                    loadUsers(); // Refresh the user list
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An unexpected error occurred.');
            }
        });
    });


    /**
     * Fetches and displays stats on the dashboard page.
     */
    function loadDashboardStats() {
        if ($('#stat-total-revenue').length) {
            $.ajax({
                url: 'api/dashboard.php',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const stats = response.stats;
                        $('#stat-total-revenue').text('₦' + parseFloat(stats.total_revenue).toLocaleString());
                        $('#stat-total-orders').text(stats.total_orders);
                        $('#stat-total-users').text(stats.total_users);
                        $('#stat-pending-requests').text(stats.pending_requests);
                    } else {
                        console.error("Failed to load dashboard stats:", response.message);
                    }
                },
                error: function() {
                    console.error("AJAX error while loading dashboard stats.");
                }
            });
        }
    }

    /**
     * Fetches and displays a paginated list of users.
     */
    function loadUsers(page = 1) {
        const usersTableBody = $('#users-table-body');
        if (usersTableBody.length) {
            $.ajax({
                url: `api/users.php?page=${page}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        usersTableBody.empty();
                        if(response.users.length === 0) {
                            usersTableBody.html('<tr><td colspan="6" class="text-center py-8 text-gray-500">No users found.</td></tr>');
                            return;
                        }
                        
                        response.users.forEach(user => {
                            const roleBadge = user.role === 'admin' 
                                ? `<span class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded">Admin</span>`
                                : `<span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">Customer</span>`;
                            const joinedDate = new Date(user.created_at).toLocaleDateString('en-CA');

                            const row = `
                                <tr class="border-b">
                                    <td class="py-3 px-4">${user.id}</td>
                                    <td class="py-3 px-4">${user.first_name} ${user.last_name}</td>
                                    <td class="py-3 px-4">${user.email}</td>
                                    <td class="py-3 px-4">${roleBadge}</td>
                                    <td class="py-3 px-4">${joinedDate}</td>
                                    <td class="py-3 px-4">
                                        <a href="#" class="text-blue-500 hover:underline mr-4"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="#" class="text-red-500 hover:underline"><i class="fas fa-trash-alt"></i> Delete</a>
                                    </td>
                                </tr>
                            `;
                            usersTableBody.append(row);
                        });
                        renderAdminPagination(response.pagination, 'users');
                    } else {
                        usersTableBody.html(`<tr><td colspan="6" class="text-center py-8 text-red-500">Error: ${response.message}</td></tr>`);
                    }
                },
                error: function() {
                    usersTableBody.html('<tr><td colspan="6" class="text-center py-8 text-red-500">Failed to load users.</td></tr>');
                }
            });
        }
    }

    /**
     * Fetches and displays a paginated list of products.
     */
    function loadProducts(page = 1) {
        const productsTableBody = $('#products-table-body');
        if (productsTableBody.length) {
            $.ajax({
                url: `api/products.php?page=${page}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        productsTableBody.empty();
                        if(response.products.length === 0) {
                            productsTableBody.html('<tr><td colspan="6" class="text-center py-8 text-gray-500">No products found.</td></tr>');
                            return;
                        }

                        response.products.forEach(product => {
                            const row = `
                                <tr class="border-b">
                                    <td class="py-3 px-4"><img src="${product.image_url}" alt="${product.name}" class="w-12 h-12 object-cover rounded-md"></td>
                                    <td class="py-3 px-4">${product.id}</td>
                                    <td class="py-3 px-4">${product.name}</td>
                                    <td class="py-3 px-4">${product.category_name}</td>
                                    <td class="py-3 px-4">&#8358;${parseFloat(product.price).toLocaleString()}</td>
                                    <td class="py-3 px-4">
                                        <a href="#" class="text-blue-500 hover:underline mr-4"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="#" class="text-red-500 hover:underline"><i class="fas fa-trash-alt"></i> Delete</a>
                                    </td>
                                </tr>
                            `;
                            productsTableBody.append(row);
                        });
                        renderAdminPagination(response.pagination, 'products');
                    } else {
                        productsTableBody.html(`<tr><td colspan="6" class="text-center py-8 text-red-500">Error: ${response.message}</td></tr>`);
                    }
                },
                error: function() {
                    productsTableBody.html('<tr><td colspan="6" class="text-center py-8 text-red-500">Failed to load products.</td></tr>');
                }
            });
        }
    }

    /**
     * Fetches and displays a paginated list of service requests.
     */
    function loadServices(page = 1) {
        const servicesTableBody = $('#services-table-body');
        if (servicesTableBody.length) {
            $.ajax({
                url: `api/services.php?page=${page}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        servicesTableBody.empty();
                        if(response.requests.length === 0) {
                            servicesTableBody.html('<tr><td colspan="6" class="text-center py-8 text-gray-500">No service requests found.</td></tr>');
                            return;
                        }

                        response.requests.forEach(request => {
                            const statusClasses = {
                                pending: 'bg-purple-100 text-purple-800',
                                in_progress: 'bg-yellow-100 text-yellow-800',
                                awaiting_payment: 'bg-blue-100 text-blue-800',
                                completed: 'bg-green-100 text-green-800',
                                cancelled: 'bg-red-100 text-red-800'
                            };
                            const statusBadge = `<span class="${statusClasses[request.status] || 'bg-gray-100 text-gray-800'} text-xs font-semibold px-2.5 py-0.5 rounded">${request.status.replace('_', ' ')}</span>`;
                            const quote = request.quote_price ? `&#8358;${parseFloat(request.quote_price).toLocaleString()}` : 'N/A';
                            const requestDate = new Date(request.created_at).toLocaleDateString('en-CA');

                            const row = `
                                <tr class="border-b">
                                    <td class="py-3 px-4 font-semibold text-brand-dark">${request.tracking_id}</td>
                                    <td class="py-3 px-4">${request.customer_email}</td>
                                    <td class="py-3 px-4">${requestDate}</td>
                                    <td class="py-3 px-4">${statusBadge}</td>
                                    <td class="py-3 px-4">${quote}</td>
                                    <td class="py-3 px-4">
                                        <a href="#" class="text-blue-500 hover:underline"><i class="fas fa-eye"></i> View</a>
                                    </td>
                                </tr>
                            `;
                            servicesTableBody.append(row);
                        });
                        renderAdminPagination(response.pagination, 'services');
                    } else {
                        servicesTableBody.html(`<tr><td colspan="6" class="text-center py-8 text-red-500">Error: ${response.message}</td></tr>`);
                    }
                },
                error: function() {
                    servicesTableBody.html('<tr><td colspan="6" class="text-center py-8 text-red-500">Failed to load service requests.</td></tr>');
                }
            });
        }
    }

    /**
     * Fetches and displays a paginated list of orders.
     */
    function loadOrders(page = 1) {
        const ordersTableBody = $('#orders-table-body');
        if (ordersTableBody.length) {
            $.ajax({
                url: `api/orders.php?page=${page}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        ordersTableBody.empty();
                        if(response.orders.length === 0) {
                            ordersTableBody.html('<tr><td colspan="6" class="text-center py-8 text-gray-500">No orders found.</td></tr>');
                            return;
                        }

                        response.orders.forEach(order => {
                            const statusClasses = {
                                pending: 'bg-yellow-100 text-yellow-800',
                                paid: 'bg-green-100 text-green-800',
                                completed: 'bg-blue-100 text-blue-800',
                                failed: 'bg-red-100 text-red-800'
                            };
                            const statusBadge = `<span class="${statusClasses[order.status] || 'bg-gray-100 text-gray-800'} text-xs font-semibold px-2.5 py-0.5 rounded">${order.status}</span>`;
                            const orderDate = new Date(order.created_at).toLocaleDateString('en-CA');

                            const row = `
                                <tr class="border-b">
                                    <td class="py-3 px-4 font-semibold text-brand-dark">#ME-${order.id}</td>
                                    <td class="py-3 px-4">${order.customer_email}</td>
                                    <td class="py-3 px-4">${orderDate}</td>
                                    <td class="py-3 px-4">&#8358;${parseFloat(order.order_total).toLocaleString()}</td>
                                    <td class="py-3 px-4">${statusBadge}</td>
                                    <td class="py-3 px-4">
                                        <a href="#" class="text-blue-500 hover:underline"><i class="fas fa-eye"></i> View Details</a>
                                    </td>
                                </tr>
                            `;
                            ordersTableBody.append(row);
                        });
                        renderAdminPagination(response.pagination, 'orders');
                    } else {
                        ordersTableBody.html(`<tr><td colspan="6" class="text-center py-8 text-red-500">Error: ${response.message}</td></tr>`);
                    }
                },
                error: function() {
                    ordersTableBody.html('<tr><td colspan="6" class="text-center py-8 text-red-500">Failed to load orders.</td></tr>');
                }
            });
        }
    }

    /**
     * Fetches and displays a paginated list of payments.
     */
    function loadPayments(page = 1) {
        const paymentsTableBody = $('#payments-table-body');
        if (paymentsTableBody.length) {
            $.ajax({
                url: `api/payments.php?page=${page}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        paymentsTableBody.empty();
                        if(response.payments.length === 0) {
                            paymentsTableBody.html('<tr><td colspan="7" class="text-center py-8 text-gray-500">No payments found.</td></tr>');
                            return;
                        }

                        response.payments.forEach(payment => {
                            const statusClasses = {
                                successful: 'bg-green-100 text-green-800',
                                failed: 'bg-red-100 text-red-800',
                                pending: 'bg-yellow-100 text-yellow-800'
                            };
                            const statusBadge = `<span class="${statusClasses[payment.status] || 'bg-gray-100 text-gray-800'} text-xs font-semibold px-2.5 py-0.5 rounded">${payment.status}</span>`;
                            const paymentDate = new Date(payment.paid_at).toLocaleDateString('en-CA');

                            const row = `
                                <tr class="border-b">
                                    <td class="py-3 px-4">${payment.id}</td>
                                    <td class="py-3 px-4 font-mono text-xs">${payment.reference}</td>
                                    <td class="py-3 px-4">${payment.type} (${payment.related_id})</td>
                                    <td class="py-3 px-4">${payment.customer_email}</td>
                                    <td class="py-3 px-4">&#8358;${parseFloat(payment.amount).toLocaleString()}</td>
                                    <td class="py-3 px-4">${statusBadge}</td>
                                    <td class="py-3 px-4">${paymentDate}</td>
                                </tr>
                            `;
                            paymentsTableBody.append(row);
                        });
                        renderAdminPagination(response.pagination, 'payments');
                    } else {
                        paymentsTableBody.html(`<tr><td colspan="7" class="text-center py-8 text-red-500">Error: ${response.message}</td></tr>`);
                    }
                },
                error: function() {
                    paymentsTableBody.html('<tr><td colspan="7" class="text-center py-8 text-red-500">Failed to load payments.</td></tr>');
                }
            });
        }
    }

    /**
     * Renders pagination controls for admin pages.
     */
    function renderAdminPagination(pagination, type) {
        const container = $('#pagination-container');
        container.empty();
        if (pagination.total_pages <= 1) return;

        let paginationHtml = '<nav class="flex items-center space-x-2">';
        if (pagination.current_page > 1) {
            paginationHtml += `<a href="#" class="page-link px-4 py-2 text-gray-500 hover:text-brand-dark" data-page="${pagination.current_page - 1}" data-type="${type}">&laquo;</a>`;
        }

        for (let i = 1; i <= pagination.total_pages; i++) {
            const activeClass = i === pagination.current_page ? 'bg-brand-dark text-white' : 'bg-white text-gray-700 hover:bg-gray-200';
            paginationHtml += `<a href="#" class="page-link px-4 py-2 rounded-md ${activeClass}" data-page="${i}" data-type="${type}">${i}</a>`;
        }

        if (pagination.current_page < pagination.total_pages) {
            paginationHtml += `<a href="#" class="page-link px-4 py-2 text-gray-500 hover:text-brand-dark" data-page="${pagination.current_page + 1}" data-type="${type}">&raquo;</a>`;
        }
        paginationHtml += '</nav>';
        container.html(paginationHtml);
    }
    
    // --- Event Delegation for Pagination ---
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        const type = $(this).data('type');

        if (type === 'users') {
            loadUsers(page);
        } else if (type === 'products') {
            loadProducts(page);
        } else if (type === 'services') {
            loadServices(page);
        } else if (type === 'orders') {
            loadOrders(page);
        } else if (type === 'payments') {
            loadPayments(page);
        }
    });


    // --- Initial Load ---
    loadDashboardStats();
    loadUsers();
    loadProducts();
    loadServices();
    loadOrders();
    loadPayments();

});
