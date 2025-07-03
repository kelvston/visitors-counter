@extends('layouts.header')

@section('content')
    <nav aria-label="breadcrumb" class=" px-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Back</a></li>
        </ol>
    </nav>
    <div class=" py-4 px-3 summary-card" >
        <div class="row g-4" >
            <div class="col-md-5">
                <div class="card shadow-sm h-100 card-hover-effect">
                    <div class="card-header bg-primary text-white py-3">
                        <h2 class="text-center mb-0"><i class="bi bi-bar-chart-fill me-2"></i> Sales Summary</h2>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm text-center align-middle">
                                <thead class="table-dark">
                                <tr>
                                    <th scope="col">Period</th>
                                    <th scope="col">Items</th>
                                    <th scope="col">Per Litre</th>
                                    <th scope="col">Per Kg</th>
                                    <th scope="col">Total Quantity</th>
                                    <th scope="col">Total Price</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><strong>Today</strong></td>
                                    <td id="todayItems">0
                                        <button class="btn btn-sm btn-outline-danger rounded-circle decrement-btn" onclick="decrementSale('item')">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                    </td>
                                    <td id="todayLitre">0
                                        <button class="btn btn-sm btn-outline-danger rounded-circle decrement-btn" onclick="decrementSale('litre')">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                    </td>
                                    <td id="todayKg">0
                                        <button class="btn btn-sm btn-outline-danger rounded-circle decrement-btn" onclick="decrementSale('kg')">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                    </td>
                                    <td id="todayTotal"><strong>0</strong></td>
                                    <td id="todayPrice"><strong>0</strong></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card ">
                    <div class="card-header bg-warning text-white">
                        <h4 class="mb-0">Add New Sale</h4>
                    </div>
                    <div class="card-body">
                        <form id="addSaleForm" method="POST" action="{{ route('products.store') }}"> {{-- Changed to sales.store --}}
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Search Item:</label>
                                <input type="text" id="productSearch" class="form-control mb-2" placeholder="Type to search..." autocomplete="off">
                                <input type="hidden" id="selectedProductId">
                                <div id="quantitySection" style="display: none;">
                                    <label class="form-label">Quantity:</label>
                                    <input type="text" value="1" id="productQuantity" class="form-control mb-2" placeholder="Qty (e.g., 1, 1/2, 0.25)">
                                    <button type="button" class="btn btn-primary w-100 mb-2" id="addItemBtn">+ Add Item</button>
                                    <small class="form-text text-muted">Click Add Item to insert</small>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="printReceipt" name="print_receipt" checked>
                                    <label class="form-check-label" for="printReceipt">
                                        Print Receipt
                                    </label>
                                </div>
                            </div>
                            <div id="selectedItemsContainer" class="mb-3"></div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-cart-plus-fill me-2"></i> Record Sale
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-3 card-hover-effect">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0">Recent Sales</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive recent-sales-card">
                            <table class="table table-sm text-center align-middle">
                                <thead class="table-light">
                                <tr>
                                    <th scope="col">Item</th>
                                    <th scope="col">Qty</th>
                                    <th scope="col">Unit</th>
                                    <th scope="col">Subtotal</th>
                                </tr>
                                </thead>
                                <tbody id="recentSalesTable">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <template id="selectedItemRowTemplate">
        <div class="selected-item-row">
            <input type="hidden" class="product_id">
            <span class="item_name">Item Name</span>
            <input type="text" class="form-control quantity mx-2" placeholder="Qty (e.g., 1, 1/2, 0.25)">
            <div class="btn-group">
                <button type="button" class="btn btn-outline-danger decrement-btn">−</button>
                <button type="button" class="btn btn-outline-success increment-btn">+</button>
            </div>
            <button type="button" class="btn btn-outline-secondary ms-3 remove-btn">
                <i class="bi bi-x-circle"></i>
            </button>
        </div>
    </template>

    {{-- NEW TEMPLATE FOR RECENT SALES ROWS --}}
    <template id="recentSaleRowTemplate">
        <tr>
            <td class="recent-item-name"></td>
            <td class="recent-quantity"></td>
            <td class="recent-unit-type"></td>
            <td class="recent-subtotal"></td>
            <td class="recent-date"></td>
        </tr>
    </template>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> {{-- Make sure Chart.js is included if you use it --}}

    <script>
        // Parse fraction or decimal string to float number
        function parseFraction(input) {
            input = input.trim();
            if (!input) return 0;
            if (input.includes('/')) {
                let parts = input.split('/');
                if (parts.length === 2) {
                    let numerator = parseFloat(parts[0]);
                    let denominator = parseFloat(parts[1]);
                    if (!isNaN(numerator) && !isNaN(denominator) && denominator !== 0) {
                        return numerator / denominator;
                    }
                }
                return 0;
            }
            let val = parseFloat(input);
            return isNaN(val) ? 0 : val;
        }

        // Format quantity decimal as string with max 2 decimals
        function formatQuantity(qty) {
            return qty % 1 === 0 ? qty.toString() : qty.toFixed(2);
        }

        $(document).ready(function() {
            let itemCount = 0;
            let selectedProduct = null;
            const selectedItems = new Set(); // To keep track of unique product_ids in the current sale

            // Autocomplete Search
            $('#productSearch').autocomplete({
                minLength: 1,
                source: function(request, response) {
                    $.ajax({
                        url: "{{ route('products.autocomplete') }}",
                        dataType: "json",
                        data: { term: request.term },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                select: function(event, ui) {
                    selectedProduct = ui.item;
                    $('#selectedProductId').val(ui.item.id);
                    $('#quantitySection').show();
                    return false;
                }
            });

            // Hide quantity section if input cleared
            $('#productSearch').on('input', function() {
                if (!$(this).val()) {
                    $('#quantitySection').hide();
                    selectedProduct = null;
                    $('#selectedProductId').val('');
                }
            });

            // Add Item Button
            $('#addItemBtn').on('click', function () {
                const productId = $('#selectedProductId').val();
                const quantityStr = $('#productQuantity').val().trim();
                const quantity = parseFraction(quantityStr);

                if (!productId || !selectedProduct) {
                    Swal.fire({
                        icon: 'error',
                        title: 'No product selected',
                        text: 'Please select a valid product from the list.'
                    });
                    return;
                }

                if (selectedItems.has(productId)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Duplicate product',
                        text: 'This product has already been added to the list. Adjust quantity or remove it first.'
                    });
                    return;
                }

                if (!quantity || quantity <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid quantity',
                        text: 'Please enter a valid positive quantity (e.g., 1, 1/2, 0.25).'
                    });
                    return;
                }
                const template = $('#selectedItemRowTemplate').html();
                const $itemRow = $(template);
                $itemRow.find('.product_id').val(productId);
                $itemRow.find('.product_id').attr('name', `items[${itemCount}][product_id]`); // Set name for form submission
                $itemRow.find('.item_name').text(selectedProduct.value);
                $itemRow.find('.quantity').val(formatQuantity(quantity));
                $itemRow.find('.quantity').attr('name', `items[${itemCount}][quantity]`); // Set name for form submission
                $itemRow.find('.increment-btn').click(() => {
                    let qty = parseFraction($itemRow.find('.quantity').val());
                    qty += 0.25; // Increment by 0.25
                    $itemRow.find('.quantity').val(formatQuantity(qty));
                });

                $itemRow.find('.decrement-btn').click(() => {
                    let qty = parseFraction($itemRow.find('.quantity').val());
                    if (qty > 0.25) { // Ensure quantity doesn't go below 0.25 for positive value
                        qty -= 0.25; // Decrement by 0.25
                        $itemRow.find('.quantity').val(formatQuantity(qty));
                    } else if (qty > 0 && qty <= 0.25) { // If it's already small, set to 0 to allow removal
                        $itemRow.find('.quantity').val(0);
                    }
                });

                $itemRow.find('.remove-btn').click(() => {
                    $itemRow.remove();
                    selectedItems.delete(productId); // Remove from tracking set
                });

                $('#selectedItemsContainer').append($itemRow);
                selectedItems.add(productId); // Add to tracking set
                itemCount++; // Increment for unique indexing

                // Clear input fields for next item
                $('#productSearch').val('');
                $('#productQuantity').val('1');
                $('#selectedProductId').val('');
                $('#quantitySection').hide();
                selectedProduct = null;
            });

            // Submit form via AJAX with SweetAlert feedback
            function b64toBlob(b64Data, contentType='', sliceSize=512) {
                const byteCharacters = atob(b64Data);
                const byteArrays = [];

                for (let offset = 0; offset < byteCharacters.length; offset += sliceSize) {
                    const slice = byteCharacters.slice(offset, offset + sliceSize);

                    const byteNumbers = new Array(slice.length);
                    for (let i = 0; i < slice.length; i++) {
                        byteNumbers[i] = slice.charCodeAt(i);
                    }

                    const byteArray = new Uint8Array(byteNumbers);

                    byteArrays.push(byteArray);
                }

                return new Blob(byteArrays, {type: contentType});
            }


            $('#addSaleForm').on('submit', function(e) {
                e.preventDefault();

                let itemsData = [];
                let isValid = true;

                // Loop through all selected items in the container
                $('#selectedItemsContainer .selected-item-row').each(function() {
                    const productId = $(this).find('.product_id').val();
                    const quantityStr = $(this).find('.quantity').val().trim();
                    const quantity = parseFraction(quantityStr);

                    if (!productId) {
                        Swal.fire('Validation Error', 'A selected item is missing a product ID. Please re-add or clear.', 'error');
                        isValid = false;
                        return false; // Break .each loop
                    }
                    if (quantity <= 0 || isNaN(quantity)) {
                        Swal.fire('Validation Error', `Quantity for '${$(this).find('.item_name').text()}' must be a positive number.`, 'error');
                        isValid = false;
                        return false; // Break .each loop
                    }
                    itemsData.push({
                        product_id: productId,
                        quantity: quantity
                    });
                });

                // If no items were added via the "Add Item" button, check the main fields
                if (itemsData.length === 0) {
                    const singleProductId = $('#selectedProductId').val();
                    const quantityStr = $('#productQuantity').val().trim();
                    const quantity = parseFraction(quantityStr);

                    if (!singleProductId) {
                        Swal.fire({
                            icon: 'error',
                            title: 'No products added',
                            text: 'Please select and add at least one product to the sale.'
                        });
                        return;
                    }

                    if (quantity <= 0 || isNaN(quantity)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid quantity',
                            text: 'Please enter a valid positive quantity for the single item.'
                        });
                        return;
                    }

                    itemsData.push({
                        product_id: singleProductId,
                        quantity: quantity
                    });
                }

                if (!isValid) {
                    return; // Stop submission if previous validation failed
                }

                const submitButton = $(this).find('button[type="submit"]');
                submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Recording...');
                const print_receipt = $('#printReceipt').is(':checked') ? 1 : 0;

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        print_receipt,
                        items: itemsData // Send as 'items' array
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false,
                                customClass: {
                                    popup: 'my-swal-popup',
                                    title: 'my-swal-title',
                                    htmlContainer: 'my-swal-text',
                                    timerProgressBar: 'my-swal-timer-progress'
                                },
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer);
                                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                                }
                            }).then(() => {

                                $('#selectedItemsContainer').empty(); // Clear all added items
                                $('#productSearch').val('');
                                $('#productQuantity').val('1');
                                $('#selectedProductId').val('');
                                $('#quantitySection').hide();
                                selectedProduct = null;
                                selectedItems.clear(); // Clear tracking set
                                itemCount = 0; // Reset item counter

                                submitButton.prop('disabled', false).html('<i class="bi bi-cart-plus-fill me-2"></i> Record Sale');
// If receipt PDF returned, open it in a new tab
                                if (response.receipt_pdf) {
                                    const pdfData = response.receipt_pdf;
                                    const blob = b64toBlob(pdfData, 'application/pdf');
                                    const blobUrl = URL.createObjectURL(blob);
                                    window.open(blobUrl, '_blank');
                                }
                                fetchSalesSummary(); // Refresh summary after recording sale
                                fetchRecentSales(); // Refresh recent sales after recording sale
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to record sale.',
                            });
                            submitButton.prop('disabled', false).html('<i class="bi bi-cart-plus-fill me-2"></i> Record Sale');
                        }
                    },
                    error: function(xhr) {
                        let errorText = 'An error occurred.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            if (xhr.responseJSON.errors) {
                                let errors = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                                errorText = `Validation Errors:<br>${errors}`;
                            } else {
                                errorText = xhr.responseJSON.message;
                            }
                        } else if (xhr.statusText) {
                            errorText = xhr.statusText;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: errorText, // Use html for multiline errors
                        });
                        submitButton.prop('disabled', false).html('<i class="bi bi-cart-plus-fill me-2"></i> Record Sale');
                    }
                });
            });

            // Function to fetch and update sales summary table and quick summary
            function fetchSalesSummary() {
                fetch("{{ route('sales.summary') }}")
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                console.error('Error loading sales summary:', errorData.message || 'Unknown error');
                                throw new Error('Network response was not ok');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        document.getElementById('todayItems').innerHTML = data.today.items + `<button class="btn btn-sm btn-outline-danger rounded-circle decrement-btn" onclick="decrementSale('item')"><i class="bi bi-dash"></i></button>`;
                        document.getElementById('todayLitre').innerHTML = data.today.litre + `<button class="btn btn-sm btn-outline-danger rounded-circle decrement-btn" onclick="decrementSale('litre')"><i class="bi bi-dash"></i></button>`;
                        document.getElementById('todayKg').innerHTML = data.today.kg + `<button class="btn btn-sm btn-outline-danger rounded-circle decrement-btn" onclick="decrementSale('kg')"><i class="bi bi-dash"></i></button>`;
                        document.getElementById('todayTotal').textContent = data.today.total_quantity;
                        document.getElementById('todayPrice').textContent = data.today.total_revenue;

                        document.getElementById('totalSalesItems').textContent = data.total_sales.items;
                        document.getElementById('totalSalesLitre').textContent = data.total_sales.litre;
                        document.getElementById('totalSalesKg').textContent = data.total_sales.kg;
                        document.getElementById('totalSalesTotal').textContent = data.total_sales.total_quantity;
                        document.getElementById('totalSalesPrice').textContent = data.total_sales.total_revenue;

                        // Update quick summary
                        $('#summaryTotalSales').text(data.total_sales.total_revenue);
                        $('#summaryTopItem').text(data.top_item?.name || 'N/A');
                        $('#summaryAvgDaily').text(data.avg_daily_sales ? data.avg_daily_sales.toFixed(2) : '0');
                    })
                    .catch(error => {
                        console.error('Error fetching sales summary:', error);
                    });
            }

            // Function to fetch and update recent sales table
            function fetchRecentSales() {
                fetch("{{ route('sales.recent') }}")
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                console.error('Error loading recent sales:', errorData.message || 'Unknown error');
                                throw new Error('Network response was not ok');
                            });
                        }
                        return response.json();
                    })
                    .then(response => {
                        if (response.success) {
                            const $tbody = $('#recentSalesTable');
                            $tbody.empty(); // Clear existing rows
                            if (response.data.length === 0) {
                                $tbody.append('<tr><td colspan="5">No recent sales</td></tr>');
                                return;
                            }
                            response.data.forEach(item => {
                                const $row = $($('#recentSaleRowTemplate').html()); // Use the new template
                                $row.find('.recent-item-name').text(item.name);
                                $row.find('.recent-quantity').text(item.quantity);
                                $row.find('.recent-unit-type').text(item.unit_type);
                                $row.find('.recent-subtotal').text(item.subtotal);
                                $tbody.append($row);
                            });
                        } else {
                            console.error('Failed to load recent sales:', response.message);
                            $('#recentSalesTable').html('<tr><td colspan="5">Error loading recent sales</td></tr>');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching recent sales:', error);
                        $('#recentSalesTable').html('<tr><td colspan="5">Error loading recent sales</td></tr>');
                    });
            }

            // Initial fetches on page load
            fetchSalesSummary();
            fetchRecentSales(); // Call this to populate recent sales on load

            // The decrementSale function (assuming it's in SalesController)
            function decrementSale(type) {
                $.ajax({
                    url: "{{ route('sales.decrement') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        type: type
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Decremented!',
                                text: response.message || 'Sale decremented successfully.',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                fetchSalesSummary(); // Refresh summary after decrement
                                fetchRecentSales(); // Refresh recent sales after decrement
                            });
                        } else {
                            Swal.fire('Error', response.message || 'Failed to decrement sale.', 'error');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred during decrement.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', errorMessage, 'error');
                    }
                });
            }
        });
    </script>
@endpush

