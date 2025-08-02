$(document).ready(function() {

    // --- UI Helpers ---
    const toastContainer = $('#toast-container');
    const confirmationModal = $('#confirmation-modal');

    function showToast(message, isSuccess = true) {
        const icon = isSuccess ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-exclamation-circle"></i>';
        const bgColor = isSuccess ? 'bg-green-500' : 'bg-red-500';
        const toast = $(`<div class="toast-in ${bgColor} text-white p-4 rounded-lg shadow-lg flex items-center mb-2"><div class="mr-3">${icon}</div><div>${message}</div></div>`);
        toastContainer.append(toast);
        setTimeout(() => {
            toast.removeClass('toast-in').addClass('toast-out');
            toast.on('animationend', () => toast.remove());
        }, 4000);
    }

    function showConfirmation({ title, message, confirmText, onConfirm }) {
        confirmationModal.find('#confirmation-title').text(title);
        confirmationModal.find('#confirmation-message').text(message);
        const confirmBtn = confirmationModal.find('#confirm-action-btn');
        confirmBtn.text(confirmText);
        confirmBtn.off('click').on('click', () => { onConfirm(); confirmationModal.addClass('hidden'); });
        confirmationModal.find('#confirm-cancel-btn').off('click').on('click', () => confirmationModal.addClass('hidden'));
        confirmationModal.removeClass('hidden');
    }

    // --- Modal Controls ---
    const userModal = $('#user-modal');
    const productModal = $('#product-modal');
    const serviceModal = $('#service-modal');
    const orderModal = $('#order-modal');

    $('#add-user-btn').on('click', function() {
        $('#modal-title').text('Add New User');
        $('#user-form')[0].reset();
        $('#user_id').val('');
        $('#password').prop('required', true);
        userModal.removeClass('hidden');
    });
    $('#cancel-btn, #user-modal').on('click', function(e) { if (e.target.id === 'user-modal' || e.target.id === 'cancel-btn') userModal.addClass('hidden'); });
    $('#user-modal > div').on('click', e => e.stopPropagation());
    
    $('#add-product-btn').on('click', function() {
        $('#product-modal-title').text('Add New Product');
        $('#product-form')[0].reset();
        $('#product_id').val('');
        $('#image_url').prop('required', true);
        $('#digital_file_url').prop('required', true);
        $('#product-image-previews').addClass('hidden'); // Hide previews for new products
        productModal.removeClass('hidden');
    });
    $('#product-cancel-btn, #product-modal').on('click', function(e) { if (e.target.id === 'product-modal' || e.target.id === 'product-cancel-btn') productModal.addClass('hidden'); });
    $('#product-modal > div').on('click', e => e.stopPropagation());

    $('#service-cancel-btn, #service-modal').on('click', function(e) { if (e.target.id === 'service-modal' || e.target.id === 'service-cancel-btn') serviceModal.addClass('hidden'); });
    $('#service-modal > div').on('click', e => e.stopPropagation());
    
    $('#order-cancel-btn, #order-modal').on('click', function(e) { if (e.target.id === 'order-modal' || e.target.id === 'order-cancel-btn') orderModal.addClass('hidden'); });
    $('#order-modal > div').on('click', e => e.stopPropagation());

    // --- Form Submissions ---
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
        const isUpdate = userData.user_id !== '';
        if (!isUpdate && !userData.password) { showToast('Password is required for new users.', false); return; }
        $.ajax({
            url: 'api/users.php',
            method: isUpdate ? 'PUT' : 'POST',
            contentType: 'application/json',
            data: JSON.stringify(userData),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showToast(response.message, true);
                    userModal.addClass('hidden');
                    loadUsers();
                } else { showToast(response.message, false); }
            },
            error: () => showToast('An unexpected error occurred.', false)
        });
    });

    $('#product-form').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        $.ajax({
            url: 'api/products.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showToast(response.message, true);
                    productModal.addClass('hidden');
                    loadProducts();
                } else {
                    showToast(response.message, false);
                }
            },
            error: () => showToast('An unexpected error occurred.', false)
        });
    });

    $('#service-form').on('submit', function(e) {
        e.preventDefault();
        const requestData = {
            request_id: $('#request_id').val(),
            status: $('#status').val(),
            quote_price: $('#quote_price').val()
        };
        $.ajax({
            url: 'api/services.php',
            method: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showToast(response.message, true);
                    serviceModal.addClass('hidden');
                    loadServices();
                } else {
                    showToast(response.message, false);
                }
            },
            error: () => showToast('An unexpected error occurred.', false)
        });
    });

    $('#order-status-form').on('submit', function(e) {
        e.preventDefault();
        const orderData = {
            order_id: $('#order_id').val(),
            status: $('#order_status').val()
        };

        $.ajax({
            url: 'api/orders.php',
            method: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify(orderData),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showToast(response.message, true);
                    orderModal.addClass('hidden');
                    loadOrders(); // Refresh the orders list
                } else {
                    showToast(response.message, false);
                }
            },
            error: () => showToast('An unexpected error occurred.', false)
        });
    });

    // --- Data Loading Functions ---
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
                    }
                }
            });
        }
    }

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
                                        <button class="edit-user-btn text-blue-500 hover:underline mr-4" data-id="${user.id}"><i class="fas fa-edit"></i> Edit</button>
                                        <button class="delete-user-btn text-red-500 hover:underline" data-id="${user.id}"><i class="fas fa-trash-alt"></i> Delete</button>
                                    </td>
                                </tr>
                            `;
                            usersTableBody.append(row);
                        });
                        renderAdminPagination(response.pagination, 'users');
                    }
                }
            });
        }
    }

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
                                    <td class="py-3 px-4"><img src="../${product.image_url}" alt="${product.name}" class="w-12 h-12 object-cover rounded-md"></td>
                                    <td class="py-3 px-4">${product.id}</td>
                                    <td class="py-3 px-4">${product.name}</td>
                                    <td class="py-3 px-4">${product.category_name}</td>
                                    <td class="py-3 px-4">&#8358;${parseFloat(product.price).toLocaleString()}</td>
                                    <td class="py-3 px-4">
                                        <button class="edit-product-btn text-blue-500 hover:underline mr-4" data-id="${product.id}"><i class="fas fa-edit"></i> Edit</button>
                                        <button class="delete-product-btn text-red-500 hover:underline" data-id="${product.id}"><i class="fas fa-trash-alt"></i> Delete</button>
                                    </td>
                                </tr>
                            `;
                            productsTableBody.append(row);
                        });
                        renderAdminPagination(response.pagination, 'products');
                    }
                }
            });
        }
    }

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
                            const statusBadge = `<span class="${statusClasses[request.status] || 'bg-gray-100 text-gray-800'} text-xs font-semibold px-2.5 py-0.5 rounded">${request.status.replace(/_/g, ' ')}</span>`;
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
                                        <button class="view-service-btn text-blue-500 hover:underline mr-4" data-id="${request.id}"><i class="fas fa-eye"></i> View/Edit</button>
                                        <button class="delete-service-btn text-red-500 hover:underline" data-id="${request.id}"><i class="fas fa-trash-alt"></i> Delete</button>
                                    </td>
                                </tr>
                            `;
                            servicesTableBody.append(row);
                        });
                        renderAdminPagination(response.pagination, 'services');
                    }
                }
            });
        }
    }

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
                                        <button class="view-order-btn text-blue-500 hover:underline" data-id="${order.id}"><i class="fas fa-eye"></i> View Details</button>
                                    </td>
                                </tr>
                            `;
                            ordersTableBody.append(row);
                        });
                        renderAdminPagination(response.pagination, 'orders');
                    }
                }
            });
        }
    }

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
                    }
                }
            });
        }
    }

    function loadActivityLog(page = 1) {
        const logTableBody = $('#activity-log-table-body');
        if (logTableBody.length) {
            $.ajax({
                url: `api/activity_log.php?page=${page}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        logTableBody.empty();
                        if(response.logs.length === 0) {
                            logTableBody.html('<tr><td colspan="6" class="text-center py-8 text-gray-500">No activity recorded yet.</td></tr>');
                            return;
                        }

                        response.logs.forEach(log => {
                            const timestamp = new Date(log.created_at).toLocaleString();
                            const row = `
                                <tr class="border-b">
                                    <td class="py-3 px-4">${log.id}</td>
                                    <td class="py-3 px-4">${log.admin_email || 'System'}</td>
                                    <td class="py-3 px-4"><span class="bg-gray-200 text-gray-800 text-xs font-semibold px-2.5 py-0.5 rounded">${log.action}</span></td>
                                    <td class="py-3 px-4">${log.details}</td>
                                    <td class="py-3 px-4">${log.ip_address}</td>
                                    <td class="py-3 px-4">${timestamp}</td>
                                </tr>
                            `;
                            logTableBody.append(row);
                        });
                        renderAdminPagination(response.pagination, 'activity_log');
                    }
                }
            });
        }
    }

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
    
    // --- Event Delegation ---
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
        } else if (type === 'activity_log') {
            loadActivityLog(page);
        }
    });

    $(document).on('click', '.edit-user-btn', function() {
        const userId = $(this).data('id');
        $.ajax({
            url: `api/users.php?id=${userId}`,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    const user = response.user;
                    $('#modal-title').text('Edit User');
                    $('#user_id').val(user.id);
                    $('#first_name').val(user.first_name);
                    $('#last_name').val(user.last_name);
                    $('#email').val(user.email);
                    $('#role').val(user.role);
                    $('#password').prop('required', false);
                    userModal.removeClass('hidden');
                } else {
                    showToast(response.message, false);
                }
            }
        });
    });

    $(document).on('click', '.delete-user-btn', function() {
        const userId = $(this).data('id');
        showConfirmation({
            title: 'Delete User',
            message: 'Are you sure you want to delete this user? This action cannot be undone.',
            confirmText: 'Delete',
            onConfirm: function() {
                $.ajax({
                    url: 'api/users.php',
                    method: 'DELETE',
                    contentType: 'application/json',
                    data: JSON.stringify({ user_id: userId }),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showToast(response.message, true);
                            loadUsers();
                        } else {
                            showToast(response.message, false);
                        }
                    },
                    error: () => showToast('An unexpected error occurred.', false)
                });
            }
        });
    });
    
    $(document).on('click', '.edit-product-btn', function() {
        const productId = $(this).data('id');
        
        // FIX: Reset modal state before making the AJAX call
        $('#product-modal-title').text('Loading Product...');
        $('#product-form')[0].reset();
        $('#product_id').val('');
        const previewsContainer = $('#preview-container');
        const imagePreviews = $('#product-image-previews');
        previewsContainer.html('<p class="text-gray-500">Loading images...</p>');
        imagePreviews.removeClass('hidden');
        productModal.removeClass('hidden');

        $.ajax({
            url: `api/products.php?id=${productId}`,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    const product = response.product;
                    $('#product-modal-title').text('Edit Product');
                    $('#product_id').val(product.id);
                    $('#product_name').val(product.name);
                    $('#category_id').val(product.category_id);
                    $('#price').val(product.price);
                    $('#description').val(product.description);
                    $('#image_url').prop('required', false);
                    $('#digital_file_url').prop('required', false);
                    
                    previewsContainer.empty();
                    const images = [product.image_url, product.image_url_2, product.image_url_3, product.image_url_4, product.image_url_5];
                    let hasImages = false;
                    images.forEach((url, index) => {
                        if (url) {
                            hasImages = true;
                            const previewHtml = `<div class="text-center"><img src="../${url}" class="w-20 h-20 object-cover rounded-md mx-auto border"><p class="text-xs text-gray-500 mt-1">Image ${index + 1}</p></div>`;
                            previewsContainer.append(previewHtml);
                        }
                    });
                    if (!hasImages) { 
                        imagePreviews.addClass('hidden'); 
                    }
                } else {
                    showToast(response.message, false);
                    productModal.addClass('hidden');
                }
            },
            error: () => {
                showToast('Failed to fetch product details.', false);
                productModal.addClass('hidden');
            }
        });
    });

    $(document).on('click', '.delete-product-btn', function() {
        const productId = $(this).data('id');
        showConfirmation({
            title: 'Delete Product',
            message: 'Are you sure you want to delete this product? All associated files will be permanently removed.',
            confirmText: 'Delete',
            onConfirm: function() {
                $.ajax({
                    url: 'api/products.php',
                    method: 'DELETE',
                    contentType: 'application/json',
                    data: JSON.stringify({ product_id: productId }),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showToast(response.message, true);
                            loadProducts();
                        } else {
                            showToast(response.message, false);
                        }
                    },
                    error: () => showToast('An unexpected error occurred.', false)
                });
            }
        });
    });

    $(document).on('click', '.view-service-btn', function() {
        const requestId = $(this).data('id');
        $.ajax({
            url: `api/services.php?id=${requestId}`,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const req = response.request;
                    $('#service-modal-title').text(`Request: ${req.tracking_id}`);
                    $('#request_id').val(req.id);
                    $('#service-details').text(req.details);
                    $('#status').val(req.status);
                    $('#quote_price').val(req.quote_price);
                    serviceModal.removeClass('hidden');
                } else {
                    showToast(response.message, false);
                }
            }
        });
    });

    $(document).on('click', '.delete-service-btn', function() {
        const requestId = $(this).data('id');
        showConfirmation({
            title: 'Delete Service Request',
            message: 'Are you sure you want to delete this request? This action is permanent.',
            confirmText: 'Delete',
            onConfirm: function() {
                $.ajax({
                    url: 'api/services.php',
                    method: 'DELETE',
                    contentType: 'application/json',
                    data: JSON.stringify({ request_id: requestId }),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showToast(response.message, true);
                            loadServices();
                        } else {
                            showToast(response.message, false);
                        }
                    },
                    error: () => showToast('An unexpected error occurred.', false)
                });
            }
        });
    });

    $(document).on('click', '.view-order-btn', function() {
        const orderId = $(this).data('id');
        $.ajax({
            url: `api/orders.php?id=${orderId}`,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const order = response.order;
                    $('#order-modal-title').text(`Order Details: #ME-${order.id}`);
                    $('#order_id').val(order.id);
                    $('#order-customer-name').text(`${order.first_name} ${order.last_name}`);
                    $('#order-customer-email').text(order.email);
                    $('#order-date').text(new Date(order.created_at).toLocaleString());
                    $('#order-total').text(`₦${parseFloat(order.order_total).toLocaleString()}`);
                    $('#order-payment-ref').text(order.payment_reference || 'N/A');
                    $('#order_status').val(order.status);

                    const itemsContainer = $('#order-items-container');
                    itemsContainer.empty();
                    order.items.forEach(item => {
                        itemsContainer.append(`<p class="text-sm text-gray-600">${item.name} - ₦${parseFloat(item.price).toLocaleString()}</p>`);
                    });
                    
                    orderModal.removeClass('hidden');
                } else {
                    showToast(response.message, false);
                }
            }
        });
    });

    // --- Initial Load ---
    loadDashboardStats();
    loadUsers();
    loadProducts();
    loadServices();
    loadOrders();
    loadPayments();
    loadActivityLog();
});
