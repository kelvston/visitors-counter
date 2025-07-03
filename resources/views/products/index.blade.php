@extends('layouts.header')

@section('content')
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .filters {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .sort-icon {
            cursor: pointer;
            margin-left: 5px;
        }

        .sort-icon.active {
            color: #0d6efd;
        }

        @media (max-width: 768px) {
            .filters .row > div {
                margin-bottom: 10px;
            }
        }
    </style>


        <div class="header breadcrumb card-hover-effect">
            <h1>Products</h1>
            <div>
                <a href="{{ route('products.export') }}" class="btn btn-success me-2" aria-label="Export products to CSV">
                    <i class="bi bi-download"></i> Export CSV
                </a>
                <a href="{{ route('products.create') }}" class="btn btn-primary" aria-label="Add new product">
                    <i class="bi bi-plus-circle"></i> Add Product
                </a>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="filters breadcrumb card-hover-effect">
            <form id="filterForm" method="GET" aria-label="Product filters">
                <div class="row">
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search"
                                   placeholder="Search products..."
                                   value="{{ request('search') }}"
                                   aria-label="Search products by name or barcode">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="unit_type" aria-label="Filter by unit type">
                            <option value="">All Unit Types</option>
                            <option value="item" {{ request('unit_type') == 'item' ? 'selected' : '' }}>Item</option>
                            <option value="kg" {{ request('unit_type') == 'kg' ? 'selected' : '' }}>KG</option>
                            <option value="litre" {{ request('unit_type') == 'litre' ? 'selected' : '' }}>Litre</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="stock_status" aria-label="Filter by stock status">
                            <option value="">All Stock Status</option>
                            <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                            <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                            <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="number" class="form-control" name="price_min"
                                   placeholder="Min Price" value="{{ request('price_min') }}"
                                   aria-label="Minimum price filter">
                            <input type="number" class="form-control" name="price_max"
                                   placeholder="Max Price" value="{{ request('price_max') }}"
                                   aria-label="Maximum price filter">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100" aria-label="Apply filters">
                            <i class="bi bi-filter"></i> Apply Filters
                        </button>

                        <a href="{{ route('products.index') }}" class="btn btn-secondary w-100 mt-2" aria-label="Clear all filters">
                            <i class="bi bi-x-circle"></i> Clear Filters
                            </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-responsive breadcrumb card-hover-effect">
            <table class="table table-striped" aria-label="Products table">
                <thead>
                <tr>
                    @php
                        $sortableColumns = ['id', 'name', 'unit_type', 'price_per_unit','cost_price', 'stock'];
                    @endphp
                    @foreach(['id' => 'ID', 'name' => 'Name', 'unit_type' => 'Unit Type', 'price_per_unit' => 'Price per Unit','cost_price'=>'Cost Price', 'stock' => 'Stock'] as $column => $label)
                        <th>
                            {{ $label }}
                            @if(in_array($column, $sortableColumns))
                                <span class="sort-icon {{ request('sort') == $column ? 'active' : '' }}"
                                      onclick="sortTable('{{ $column }}')"
                                      role="button"
                                      aria-label="Sort by {{ $label }} {{ request('sort') == $column && request('order') == 'asc' ? 'descending' : 'ascending' }}">
                                <i class="bi bi-arrow-down-up"></i>
                            </span>
                            @endif
                        </th>
                    @endforeach
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->unit_type }}</td>
                        <td>{{ number_format($product->price_per_unit, 2) }}</td>
                        <td>{{ number_format($product->cost_price, 2) }}</td>
                        <td>
                        <span class="badge {{ $product->stock == 0 ? 'bg-danger' : ($product->stock < 10 ? 'bg-warning' : 'bg-success') }}"
                              aria-label="Stock status: {{ $product->stock }} {{ $product->unit_type }}">
                            {{ $product->stock }} {{ $product->unit_type }}
                        </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info me-2" onclick="openEditModal({{ $product->id }})"
                                    aria-label="Edit product {{ $product->name }}">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                            <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this product?')"
                                        aria-label="Delete product {{ $product->name }}">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    Showing {{ $products->firstItem() }} to {{ $products->lastItem() }}
                    of {{ $products->total() }} products
                </div>
                {{ $products->links() }}
            </div>
        </div>

    <script>
        const sortableColumns = ['id', 'name', 'unit_type', 'price_per_unit','cost_price', 'stock'];
        console.log('Sortable Columns:', sortableColumns); // Debugging output

        function sortTable(column) {
            if (!sortableColumns.includes(column)) return; // Prevent invalid columns
            const currentUrl = new URL(window.location.href);
            const currentSort = currentUrl.searchParams.get('sort');
            const currentOrder = currentUrl.searchParams.get('order');

            let newOrder = 'asc';
            if (currentSort === column && currentOrder === 'asc') {
                newOrder = 'desc';
            }

            currentUrl.searchParams.set('sort', column);
            currentUrl.searchParams.set('order', newOrder);
            window.location.href = currentUrl.toString();
        }

        // Auto-submit form on select change
        document.querySelectorAll('.filters select').forEach(select => {
            select.addEventListener('change', () => {
                document.getElementById('filterForm').submit();
            });
        });

        // Debounce function
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Search input handler with debounce
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.addEventListener('input', debounce(() => {
                document.getElementById('filterForm').submit();
            }, 500));
        }

        // Placeholder for edit modal
        function openEditModal(id) {
            // Implement modal logic here, e.g., fetch product data via AJAX and open Bootstrap modal
            console.log(`Edit product with ID: ${id}`);
        }
    </script>

@endsection
