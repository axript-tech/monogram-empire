$(document).ready(function() {

    // =================================================================
    // 1. UI HELPERS (TOAST NOTIFICATIONS & CONFIRMATION MODAL)
    // =================================================================

    const toast = $('#toast-notification');
    const toastIcon = $('#toast-icon');
    const toastMessage = $('#toast-message');
    const confirmationModal = $('#confirmation-modal');
    const confirmBtn = $('#confirm-action-btn');
    const cancelBtn = $('#cancel-action-btn');
    let confirmCallback = null;

    function showToast(message, isSuccess = true) {
        toastMessage.text(message);
        if (isSuccess) {
            toast.removeClass('bg-red-500').addClass('bg-green-500');
            toastIcon.removeClass('fa-times-circle').addClass('fa-check-circle');
        } else {
            toast.removeClass('bg-green-500').addClass('bg-red-500');
            toastIcon.removeClass('fa-check-circle').addClass('fa-times-circle');
        }
        toast.fadeIn();
        setTimeout(() => toast.fadeOut(), 4000);
    }

    function showConfirmation(message, callback) {
        confirmationModal.find('p').text(message);
        confirmCallback = callback;
        confirmationModal.removeClass('hidden');
    }

    confirmBtn.on('click', function() {
        if (typeof confirmCallback === 'function') {
            confirmCallback();
        }
        confirmationModal.addClass('hidden');
    });

    cancelBtn.on('click', function() {
        confirmationModal.addClass('hidden');
    });

    // =================================================================
    // 2. GENERIC MODAL CONTROLS
    // =================================================================

    function showModal(modal) {
        modal.removeClass('hidden');
    }

    function hideModal(modal) {
        modal.addClass('hidden');
        const form = modal.find('form');
        if (form.length) {
            form[0].reset();
            // Reset file input text
            $('.file-input-filename').text('No file chosen');
            // Hide all previews
            $('img[id^="preview-"]').addClass('hidden').attr('src', '');
        }
        modal.find('input[name="id"]').val('');
        $('#image-previews').empty().addClass('hidden');
        resetProductModalSteps();
    }

    $('.close-modal-btn').on('click', function() {
        hideModal($(this).closest('.fixed.inset-0'));
    });
    
    // Updated to handle live image previews
    $(document).on('change', 'input[type="file"]', function() {
        const targetSelector = $(this).data('filename-target');
        const previewSelector = $(this).data('preview-target');
        const file = this.files[0];

        if (targetSelector) {
            $(targetSelector).text(file ? file.name : 'No file chosen');
        }

        if (previewSelector && file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $(previewSelector).attr('src', e.target.result).removeClass('hidden');
            };
            reader.readAsDataURL(file);
        } else if(previewSelector) {
            $(previewSelector).addClass('hidden').attr('src', '');
        }
    });

    // =================================================================
    // 3. PRODUCT MODAL STEPPER LOGIC
    // =================================================================
    let currentStep = 1;
    const totalSteps = 3;
    const nextBtn = $('#next-step-btn');
    const prevBtn = $('#prev-step-btn');
    const submitBtn = $('#submit-product-btn');

    function updateStepIndicator() {
        $('.step-indicator').each(function() {
            const step = $(this).data('step');
            const circle = $(this).find('.step-circle');
            const text = $(this).find('.step-text');
            
            circle.text(step);
            if(circle.find('i').length) circle.find('i').remove();

            if (step < currentStep) {
                circle.addClass('completed').removeClass('active').html('<i class="fas fa-check"></i>');
                text.removeClass('active');
            } else if (step == currentStep) {
                circle.addClass('active').removeClass('completed').text(step);
                text.addClass('active');
            } else {
                circle.removeClass('active completed').text(step);
                text.removeClass('active');
            }
        });
    }
    
    function resetProductModalSteps() {
        currentStep = 1;
        $('.form-step').addClass('hidden');
        $(`.form-step[data-step="1"]`).removeClass('hidden');
        prevBtn.addClass('hidden');
        nextBtn.removeClass('hidden');
        submitBtn.addClass('hidden');
        updateStepIndicator();
    }
    
    function validateStep(step) {
        let isValid = true;
        const isEditing = $('#product_id').val() !== '';
        
        $(`.form-step[data-step="${step}"] [required]`).each(function() {
            const input = $(this);
            let hasValue = true;

            if (input.is('[type=file]')) {
                if (!isEditing && input[0].files.length === 0) {
                    hasValue = false;
                }
            } else {
                if (!input.val()) {
                    hasValue = false;
                }
            }

            if (!hasValue) {
                isValid = false;
                if (input.is('[type=file]')) {
                    input.closest('.file-input-wrapper').find('.file-input-button').addClass('border-red-500');
                } else {
                    input.addClass('border-red-500');
                }
            } else {
                if (input.is('[type=file]')) {
                    input.closest('.file-input-wrapper').find('.file-input-button').removeClass('border-red-500');
                } else {
                    input.removeClass('border-red-500');
                }
            }
        });
        if (!isValid) {
            showToast('Please fill in all required fields.', false);
        }
        return isValid;
    }


    nextBtn.on('click', function() {
        if (!validateStep(currentStep)) return;

        if (currentStep < totalSteps) {
            currentStep++;
            $('.form-step').addClass('hidden');
            $(`.form-step[data-step="${currentStep}"]`).removeClass('hidden');
            prevBtn.removeClass('hidden');
            if (currentStep === totalSteps) {
                nextBtn.addClass('hidden');
                submitBtn.removeClass('hidden');
            }
            updateStepIndicator();
        }
    });

    prevBtn.on('click', function() {
        if (currentStep > 1) {
            currentStep--;
            $('.form-step').addClass('hidden');
            $(`.form-step[data-step="${currentStep}"]`).removeClass('hidden');
            submitBtn.addClass('hidden');
            nextBtn.removeClass('hidden');
            if (currentStep === 1) {
                prevBtn.addClass('hidden');
            }
            updateStepIndicator();
        }
    });


    // =================================================================
    // 4. DATA LOADING FUNCTIONS
    // =================================================================
    
    function renderAdminPagination(pagination, type) {
        const container = $('#pagination-container');
        container.empty();
        if (!pagination || pagination.total_pages <= 1) return;

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

    function loadDashboardStats() {
        if ($('#stat-total-revenue').length) {
            $.getJSON('api/dashboard.php', function(response) {
                if (response.success && response.stats) {
                    const stats = response.stats;
                    $('#stat-total-revenue').text('₦' + parseFloat(stats.total_revenue || 0).toLocaleString());
                    $('#stat-total-orders').text(stats.total_orders || 0);
                    $('#stat-total-users').text(stats.total_users || 0);
                    $('#stat-pending-requests').text(stats.pending_requests || 0);
                } else {
                    $('#stat-total-revenue, #stat-total-orders, #stat-total-users, #stat-pending-requests').text('--');
                }
            }).fail(function() {
                $('#stat-total-revenue, #stat-total-orders, #stat-total-users, #stat-pending-requests').text('Error');
            });
        }
    }
    
    function loadRecentActivity() {
        const tableBody = $('#recent-activity-body');
        if (!tableBody.length) return;
        
        $.getJSON('api/dashboard_activity.php', function(response) {
            tableBody.empty();
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

    function loadUsers(page = 1) {
        const tableBody = $('#users-table-body');
        if (!tableBody.length) return;
        tableBody.html('<tr><td colspan="6" class="text-center py-8">Loading...</td></tr>');
        $.getJSON(`api/users.php?page=${page}`, function(response) {
            tableBody.empty();
            if (response.success && response.users.length > 0) {
                response.users.forEach(user => {
                    const roleBadge = user.role === 'admin' ? `<span class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded">Admin</span>` : `<span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">Customer</span>`;
                    tableBody.append(`
                        <tr class="border-b">
                            <td class="py-3 px-4">${user.id}</td>
                            <td class="py-3 px-4">${user.first_name} ${user.last_name}</td>
                            <td class="py-3 px-4">${user.email}</td>
                            <td class="py-3 px-4">${roleBadge}</td>
                            <td class="py-3 px-4">${new Date(user.created_at).toLocaleDateString()}</td>
                            <td class="py-3 px-4"><button class="edit-btn text-blue-500" data-id="${user.id}" data-type="user">Edit</button> <button class="delete-btn text-red-500" data-id="${user.id}" data-type="user">Delete</button></td>
                        </tr>
                    `);
                });
                renderAdminPagination(response.pagination, 'users');
            } else {
                tableBody.html('<tr><td colspan="6" class="text-center py-8">No users found.</td></tr>');
            }
        }).fail(function() {
            tableBody.html('<tr><td colspan="6" class="text-center py-8 text-red-500">Failed to load users.</td></tr>');
        });
    }

    function loadProducts(page = 1) {
        const tableBody = $('#products-table-body');
        if (!tableBody.length) return;
        tableBody.html('<tr><td colspan="6" class="text-center py-8">Loading...</td></tr>');
        $.getJSON(`api/products.php?page=${page}`, function(response) {
            tableBody.empty();
            if (response.success && response.products.length > 0) {
                response.products.forEach(p => {
                    tableBody.append(`
                        <tr class="border-b">
                            <td class="py-3 px-4"><img src="../${p.image_url}" class="h-12 w-12 object-cover rounded"></td>
                            <td class="py-3 px-4">${p.name}</td>
                            <td class="py-3 px-4">${p.category_name}</td>
                            <td class="py-3 px-4">₦${parseFloat(p.price).toLocaleString()}</td>
                            <td class="py-3 px-4">${new Date(p.created_at).toLocaleDateString()}</td>
                            <td class="py-3 px-4"><button class="edit-btn text-blue-500" data-id="${p.id}" data-type="product">Edit</button> <button class="delete-btn text-red-500" data-id="${p.id}" data-type="product">Delete</button></td>
                        </tr>
                    `);
                });
                renderAdminPagination(response.pagination, 'products');
            } else {
                tableBody.html('<tr><td colspan="6" class="text-center py-8">No products found.</td></tr>');
            }
        }).fail(function() {
            tableBody.html('<tr><td colspan="6" class="text-center py-8 text-red-500">Failed to load products.</td></tr>');
        });
    }

    function loadCategories(page = 1) {
        const tableBody = $('#categories-table-body');
        if (!tableBody.length) return;
        tableBody.html('<tr><td colspan="3" class="text-center py-8">Loading...</td></tr>');
        $.getJSON(`api/categories.php?page=${page}`, function(response) {
            tableBody.empty();
            if (response.success && response.categories.length > 0) {
                response.categories.forEach(cat => {
                    tableBody.append(`
                        <tr class="border-b">
                            <td class="py-3 px-4">${cat.id}</td>
                            <td class="py-3 px-4">${cat.name}</td>
                            <td class="py-3 px-4"><button class="edit-btn text-blue-500" data-id="${cat.id}" data-type="category">Edit</button> <button class="delete-btn text-red-500" data-id="${cat.id}" data-type="category">Delete</button></td>
                        </tr>
                    `);
                });
                renderAdminPagination(response.pagination, 'categories');
            } else {
                tableBody.html('<tr><td colspan="3" class="text-center py-8">No categories found.</td></tr>');
            }
        }).fail(function() {
            tableBody.html('<tr><td colspan="3" class="text-center py-8 text-red-500">Failed to load categories.</td></tr>');
        });
    }

    function loadAllProductsForSelect() {
        const productDatalist = $('#product-datalist');
        if (!productDatalist.length) return;

        $.getJSON('api/products.php?list=all', function(response) {
            if (response.success && response.products) {
                productDatalist.empty();
                response.products.forEach(product => {
                    productDatalist.append(`<option value="${product.id}">${product.name} (SKU: ${product.sku || 'N/A'})</option>`);
                });
            }
        });
    }

    function loadServices(page = 1) {
        const tableBody = $('#services-table-body');
        if (!tableBody.length) return;
        tableBody.html('<tr><td colspan="6" class="text-center py-8">Loading...</td></tr>');
        $.getJSON(`api/services.php?page=${page}`, function(response) {
            tableBody.empty();
            if (response.success && response.requests.length > 0) {
                response.requests.forEach(r => {
                    const statusBadge = `<span class="text-xs font-semibold px-2.5 py-0.5 rounded capitalize">${r.status.replace('_', ' ')}</span>`;
                    tableBody.append(`
                        <tr class="border-b">
                            <td class="py-3 px-4">${r.tracking_id}</td>
                            <td class="py-3 px-4">${r.customer_name}</td>
                            <td class="py-3 px-4">${new Date(r.created_at).toLocaleDateString()}</td>
                            <td class="py-3 px-4">${statusBadge}</td>
                            <td class="py-3 px-4">${r.quote_amount ? '₦' + parseFloat(r.quote_amount).toLocaleString() : 'N/A'}</td>
                            <td class="py-3 px-4"><button class="view-details-btn text-blue-500" data-id="${r.id}" data-type="service">View/Edit</button> <button class="delete-btn text-red-500" data-id="${r.id}" data-type="service">Delete</button></td>
                        </tr>
                    `);
                });
                renderAdminPagination(response.pagination, 'services');
            } else {
                tableBody.html('<tr><td colspan="6" class="text-center py-8">No service requests found.</td></tr>');
            }
        }).fail(function() {
            tableBody.html('<tr><td colspan="6" class="text-center py-8 text-red-500">Failed to load service requests.</td></tr>');
        });
    }

    function loadOrders(page = 1) {
        const tableBody = $('#orders-table-body');
        if (!tableBody.length) return;
        tableBody.html('<tr><td colspan="6" class="text-center py-8">Loading...</td></tr>');
        $.getJSON(`api/orders.php?page=${page}`, function(response) {
            tableBody.empty();
            if (response.success && response.orders.length > 0) {
                response.orders.forEach(o => {
                    const statusBadge = `<span class="text-xs font-semibold px-2.5 py-0.5 rounded capitalize">${o.status}</span>`;
                    tableBody.append(`
                        <tr class="border-b">
                            <td class="py-3 px-4">#${o.id}</td>
                            <td class="py-3 px-4">${o.customer_name}</td>
                            <td class="py-3 px-4">${new Date(o.created_at).toLocaleDateString()}</td>
                            <td class="py-3 px-4">₦${parseFloat(o.total_amount).toLocaleString()}</td>
                            <td class="py-3 px-4">${statusBadge}</td>
                            <td class="py-3 px-4"><button class="view-details-btn text-blue-500" data-id="${o.id}" data-type="order">View Details</button></td>
                        </tr>
                    `);
                });
                renderAdminPagination(response.pagination, 'orders');
            } else {
                tableBody.html('<tr><td colspan="6" class="text-center py-8">No orders found.</td></tr>');
            }
        }).fail(function() {
            tableBody.html('<tr><td colspan="6" class="text-center py-8 text-red-500">Failed to load orders.</td></tr>');
        });
    }

    function loadPayments(page = 1) {
        const tableBody = $('#payments-table-body');
        if (!tableBody.length) return;
        tableBody.html('<tr><td colspan="7" class="text-center py-8">Loading...</td></tr>');
        $.getJSON(`api/payments.php?page=${page}`, function(response) {
            tableBody.empty();
            if (response.success && response.payments.length > 0) {
                response.payments.forEach(p => {
                     const statusBadge = `<span class="text-xs font-semibold px-2.5 py-0.5 rounded capitalize">${p.status}</span>`;
                    tableBody.append(`
                        <tr class="border-b">
                            <td class="py-3 px-4">${p.id}</td>
                             <td class="py-3 px-4 font-mono text-xs">${p.reference}</td>
                            <td class="py-3 px-4">${p.customer_email}</td>
                            <td class="py-3 px-4">₦${parseFloat(p.amount).toLocaleString()}</td>
                             <td class="py-3 px-4 capitalize">${p.type}</td>
                            <td class="py-3 px-4">${statusBadge}</td>
                            <td class="py-3 px-4">${new Date(p.paid_at).toLocaleString()}</td>
                        </tr>
                    `);
                });
                renderAdminPagination(response.pagination, 'payments');
            } else {
                tableBody.html('<tr><td colspan="7" class="text-center py-8">No payments found.</td></tr>');
            }
        }).fail(function() {
            tableBody.html('<tr><td colspan="7" class="text-center py-8 text-red-500">Failed to load payments.</td></tr>');
        });
    }

    function loadActivityLog(page = 1) {
        const tableBody = $('#activity-log-table-body');
        if (!tableBody.length) return;
        tableBody.html('<tr><td colspan="5" class="text-center py-8">Loading...</td></tr>');
        $.getJSON(`api/activity_log.php?page=${page}`, function(response) {
            tableBody.empty();
            if (response.success && response.logs.length > 0) {
                response.logs.forEach(log => {
                    tableBody.append(`
                        <tr class="border-b">
                            <td class="py-3 px-4">${new Date(log.created_at).toLocaleString()}</td>
                            <td class="py-3 px-4">${log.admin_email}</td>
                            <td class="py-3 px-4">${log.action}</td>
                            <td class="py-3 px-4">${log.details}</td>
                            <td class="py-3 px-4">${log.ip_address}</td>
                        </tr>
                    `);
                });
                renderAdminPagination(response.pagination, 'activity_log');
            } else {
                tableBody.html('<tr><td colspan="5" class="text-center py-8">No activity recorded.</td></tr>');
            }
        }).fail(function() {
            tableBody.html('<tr><td colspan="5" class="text-center py-8 text-red-500">Failed to load activity log.</td></tr>');
        });
    }
    
    // =================================================================
    // 5. USER MANAGEMENT (CRUD)
    // =================================================================

    const userModal = $('#user-modal');

    $('#add-user-btn').on('click', function() {
        userModal.find('h3').text('Add New User');
        userModal.find('form')[0].reset();
        userModal.find('input[name="id"]').val('');
        showModal(userModal);
    });

    $('#user-form').on('submit', function(e) {
        e.preventDefault();
        const submitBtn = $(this).find('button[type="submit"]');
        const originalBtnText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Saving...');

        const id = $('#user_id').val();
        const method = id ? 'PUT' : 'POST';
        const url = `api/users.php${id ? `?id=${id}` : ''}`;
        
        const formData = {
            id: id,
            first_name: $('#user_first_name').val(),
            last_name: $('#user_last_name').val(),
            email: $('#user_email').val(),
            role: $('#user_role').val(),
            password: $('#user_password').val()
        };

        $.ajax({
            url: url,
            method: method,
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    hideModal(userModal);
                    showToast(response.message, true);
                    loadUsers();
                } else {
                    showToast(response.message, false);
                }
            },
            error: function() {
                showToast('An unexpected error occurred.', false);
            },
            complete: function() {
                submitBtn.prop('disabled', false).text(originalBtnText);
            }
        });
    });

    // =================================================================
    // 6. PRODUCT MANAGEMENT (CRUD)
    // =================================================================

    const productModal = $('#product-modal');

    $('#add-product-btn').on('click', function() {
        $('#product-modal-title').text('Add New Product');
        productModal.find('form')[0].reset();
        productModal.find('input[name="id"]').val('');
        $('#image-previews').empty().addClass('hidden');
        resetProductModalSteps();
        showModal(productModal);
    });

    $('#product-form').on('submit', function(e) {
        e.preventDefault();
        const submitBtn = $('#submit-product-btn');
        const originalBtnText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Saving...');

        const formData = new FormData(this);
        const id = formData.get('id');

        $.ajax({
            url: `api/products.php${id ? `?id=${id}` : ''}`,
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    hideModal(productModal);
                    showToast(response.message, true);
                    loadProducts();
                } else {
                    showToast(response.message, false);
                }
            },
            error: function() {
                showToast('An unexpected error occurred.', false);
            },
            complete: function() {
                submitBtn.prop('disabled', false).text(originalBtnText);
            }
        });
    });

    // =================================================================
    // 7. CATEGORY MANAGEMENT (CRUD)
    // =================================================================
    const categoryModal = $('#category-modal');

    $('#add-category-btn').on('click', function() {
        $('#category-modal-title').text('Add New Category');
        categoryModal.find('form')[0].reset();
        categoryModal.find('input[name="id"]').val('');
        showModal(categoryModal);
    });

    $('#category-form').on('submit', function(e) {
        e.preventDefault();
        const submitBtn = $(this).find('button[type="submit"]');
        const originalBtnText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Saving...');

        const id = $('#category_id').val();
        const method = id ? 'PUT' : 'POST';
        const url = `api/categories.php${id ? `?id=${id}` : ''}`;
        
        const formData = {
            id: id,
            name: $('#category_name').val()
        };

        $.ajax({
            url: url,
            method: method,
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    hideModal(categoryModal);
                    showToast(response.message, true);
                    loadCategories();
                } else {
                    showToast(response.message, false);
                }
            },
            error: function() {
                showToast('An unexpected error occurred.', false);
            },
            complete: function() {
                submitBtn.prop('disabled', false).text(originalBtnText);
            }
        });
    });


    // =================================================================
    // 8. SERVICE REQUEST MANAGEMENT (CRUD)
    // =================================================================

    const serviceModal = $('#service-modal');

    // Show/hide the 'Converted Product ID' field based on status
    $('#service_status').on('change', function() {
        if ($(this).val() === 'completed') {
            $('#converted-product-id-wrapper').slideDown();
        } else {
            $('#converted-product-id-wrapper').slideUp();
        }
    });

    $('#service-form').on('submit', function(e) {
        e.preventDefault();
        const submitBtn = $(this).find('button[type="submit"]');
        const originalBtnText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Saving...');

        const id = $('#service_id').val();
        const formData = {
            id: id,
            status: $('#service_status').val(),
            quote_amount: $('#service_quote').val(),
            converted_product_id: $('#converted_product_id').val()
        };

        $.ajax({
            url: `api/services.php?id=${id}`,
            method: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    hideModal(serviceModal);
                    showToast(response.message, true);
                    loadServices();
                } else {
                    showToast(response.message, false);
                }
            },
            error: function(jqXHR) {
                const response = jqXHR.responseJSON;
                 if (response && response.message) {
                    showToast(response.message, false);
                 } else {
                    showToast('An error occurred while updating the request.', false);
                 }
            },
            complete: function() {
                submitBtn.prop('disabled', false).text(originalBtnText);
            }
        });
    });
    

    // =================================================================
    // 9. ORDER MANAGEMENT
    // =================================================================

    const orderModal = $('#order-modal');

    $('#order-status-form').on('submit', function(e) {
        e.preventDefault();
        const submitBtn = $(this).find('button[type="submit"]');
        const originalBtnText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Saving...');

        const id = $('#order_id').val();
        const formData = {
            id: id,
            status: $('#order_status').val()
        };

        $.ajax({
            url: `api/orders.php?id=${id}`,
            method: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    hideModal(orderModal);
                    showToast(response.message, true);
                    loadOrders();
                } else {
                    showToast(response.message, false);
                }
            },
            error: function() {
                showToast('An error occurred while updating the order.', false);
            },
            complete: function() {
                submitBtn.prop('disabled', false).text(originalBtnText);
            }
        });
    });

    // =================================================================
    // 10. SETTINGS MANAGEMENT
    // =================================================================
     $('#settings-form').on('submit', function(e) {
        e.preventDefault();
        const submitBtn = $(this).find('button[type="submit"]');
        const originalBtnText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Saving...');
        
        const formData = {
            site_name: $('#site_name').val(),
            site_email: $('#site_email').val(),
            paystack_public_key: $('#paystack_public_key').val(),
            paystack_secret_key: $('#paystack_secret_key').val(),
            smtp_host: $('#smtp_host').val(),
            smtp_username: $('#smtp_username').val(),
            smtp_password: $('#smtp_password').val(),
            smtp_port: $('#smtp_port').val(),
            smtp_secure: $('#smtp_secure').val()
        };

        $.ajax({
            url: 'api/settings.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showToast(response.message, true);
                } else {
                    showToast(response.message, false);
                }
            },
            error: function() {
                showToast('An error occurred while saving settings.', false);
            },
            complete: function() {
                submitBtn.prop('disabled', false).text(originalBtnText);
            }
        });
    });

    // =================================================================
    // 11. EVENT DELEGATION FOR DYNAMIC CONTENT
    // =================================================================
    const mainContent = $('#admin-main-content');

    mainContent.on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        const type = $(this).data('type');

        if (type === 'user') {
            $.get(`api/users.php?id=${id}`, function(response) {
                if (response.success) {
                    const user = response.user;
                    userModal.find('h3').text('Edit User');
                    $('#user_id').val(user.id);
                    $('#user_first_name').val(user.first_name);
                    $('#user_last_name').val(user.last_name);
                    $('#user_email').val(user.email);
                    $('#user_role').val(user.role);
                    $('#user_password').val('').attr('placeholder', 'Leave blank to keep current password');
                    showModal(userModal);
                }
            });
        } else if (type === 'product') {
            $('#product-modal-loader').show();
            $('#product-form-content').hide();
            showModal(productModal);
            
            $.get(`api/products.php?id=${id}`, function(response) {
                if (response.success) {
                    const product = response.product;
                    $('#product-modal-title').text('Edit Product');
                    $('#product_id').val(product.id);
                    $('#product_name').val(product.name);
                    $('#product_category').val(product.category_id);
                    $('#product_price').val(product.price);
                    $('#product_description').val(product.description);
                    $('#product_sku').val(product.sku);

                    const previewsContainer = $('#image-previews');
                    previewsContainer.empty().addClass('hidden');
                    
                    const images = [product.image_url, product.image_url_2, product.image_url_3, product.image_url_4, product.image_url_5].filter(Boolean);
                    if (images.length > 0) {
                        previewsContainer.removeClass('hidden');
                        images.forEach((img, index) => {
                            previewsContainer.append(`<div class="relative"><img src="../${img}" class="h-20 w-20 object-cover rounded"><p class="text-xs text-center mt-1">Image ${index + 1}</p></div>`);
                        });
                    }
                    
                    $('#product-modal-loader').hide();
                    $('#product-form-content').show();
                }
            });
        } else if (type === 'category') {
             $.get(`api/categories.php?id=${id}`, function(response) {
                if (response.success) {
                    const cat = response.category;
                    $('#category-modal-title').text('Edit Category');
                    $('#category_id').val(cat.id);
                    $('#category_name').val(cat.name);
                    showModal(categoryModal);
                }
            });
        }
    });

    mainContent.on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        const type = $(this).data('type');
        let url = '', callback;

        if (type === 'user') {
            url = `api/users.php?id=${id}`;
            callback = loadUsers;
        } else if (type === 'product') {
            url = `api/products.php?id=${id}`;
            callback = loadProducts;
        } else if (type === 'service') {
            url = `api/services.php?id=${id}`;
            callback = loadServices;
        } else if (type === 'category') {
            url = `api/categories.php?id=${id}`;
            callback = loadCategories;
        }

        showConfirmation('Are you sure you want to delete this item?', function() {
            $.ajax({
                url: url,
                method: 'DELETE',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, true);
                        callback();
                    } else {
                        showToast(response.message, false);
                    }
                }
            });
        });
    });

    mainContent.on('click', '.view-details-btn', function() {
        const id = $(this).data('id');
        const type = $(this).data('type');

        if (type === 'service') {
             $.get(`api/services.php?id=${id}`, function(response) {
                if (response.success) {
                    const service = response.service;
                    serviceModal.find('h3').text(`Service Request #${service.tracking_id}`);
                    $('#service_id').val(service.id);
                    $('#service_customer_name').val(`${service.first_name} ${service.last_name}`);
                    $('#service_customer_email').val(service.email);
                    $('#service_monogram_text').val(service.design_name);
                    $('#service_details').val(service.description);
                    if(service.reference_image_path) {
                        $('#service_reference_link').attr('href', `../${service.reference_image_path}`).show();
                    } else {
                        $('#service_reference_link').hide();
                    }
                    $('#service_status').val(service.status).trigger('change'); // Trigger change to show/hide product ID field
                    $('#service_quote').val(service.quote_amount);
                    $('#converted_product_id').val(service.converted_product_id);
                    showModal(serviceModal);
                }
            });
        } else if (type === 'order') {
             $.get(`api/orders.php?id=${id}`, function(response) {
                if (response.success) {
                    const order = response.order;
                    orderModal.find('h3').text(`Order Details #${order.id}`);
                    $('#order_id').val(order.id);
                    $('#order_customer_name').val(`${order.first_name} ${order.last_name}`);
                    $('#order_customer_email').val(order.email);
                    $('#order_date').val(new Date(order.created_at).toLocaleString());
                    $('#order_total').val('₦' + parseFloat(order.total_amount).toLocaleString());
                    $('#order_status').val(order.status);
                    
                    const itemsList = $('#order-items-list');
                    itemsList.empty();
                    order.items.forEach(item => {
                        itemsList.append(`<li>${item.name} (₦${parseFloat(item.price).toLocaleString()})</li>`);
                    });

                    showModal(orderModal);
                }
            });
        }
    });
    
    mainContent.on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        const type = $(this).data('type');

        if (type === 'users') loadUsers(page);
        else if (type === 'products') loadProducts(page);
        else if (type === 'services') loadServices(page);
        else if (type === 'orders') loadOrders(page);
        else if (type === 'payments') loadPayments(page);
        else if (type === 'activity_log') loadActivityLog(page);
        else if (type === 'categories') loadCategories(page);
    });

    // =================================================================
    // 12. INITIAL LOAD (Conditional)
    // =================================================================
    const currentPath = window.location.pathname;

    if (currentPath.endsWith('dashboard.php')) {
        loadDashboardStats();
        loadRecentActivity();
    } else if (currentPath.endsWith('manage_users.php')) {
        loadUsers();
    } else if (currentPath.endsWith('manage_products.php')) {
        loadProducts();
    } else if (currentPath.endsWith('manage_categories.php')) {
        loadCategories();
    } else if (currentPath.endsWith('manage_services.php')) {
        loadServices();
        loadAllProductsForSelect();
    } else if (currentPath.endsWith('manage_orders.php')) {
        loadOrders();
    } else if (currentPath.endsWith('manage_payments.php')) {
        loadPayments();
    } else if (currentPath.endsWith('activity_log.php')) {
        loadActivityLog();
    }

});

