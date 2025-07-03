@extends('layouts.header')

@section('content')
    <div class="py-4">
        <h2 class="mb-4 text-xl font-bold">🧾 Sales Report</h2>

        {{-- Filter Form --}}
        <div class="card-summary">
        <form method="GET" action="{{ route('reports.sales') }}" class="mb-4 bg-white p-4 rounded shadow-sm">
            <div class="row g-3 card-hover-effect">
                <div class="col-md-3">
                    <label for="from" class="form-label">From Date</label>
                    <input type="date" name="from" id="from" class="form-control" value="{{ request('from') }}">
                </div>
                <div class="col-md-3">
                    <label for="to" class="form-label">To Date</label>
                    <input type="date" name="to" id="to" class="form-control" value="{{ request('to') }}">
                </div>
                <div class="col-md-3">
                    <label for="user" class="form-label">User</label>
                    <select name="user" id="user" class="form-control">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="product" class="form-label">Product</label>
                    <select name="product" id="product" class="form-control">
                        <option value="">All Products</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ (string)request('product') === (string)$product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-50">Filter</button>
                    <a href="{{ route('reports.sales') }}" class="btn btn-secondary w-25">Clear</a>
                    <a href="{{ route('reports.sales.download', request()->all()) }}" class="btn btn-success w-25">⬇ CSV</a>
                </div>
            </div>
        </form>

        {{-- Summary Section --}}
        <div class="row mb-4 ">
            <div class="col-md-4 card-hover-effect">
                <div class="p-3 bg-success text-white rounded shadow-sm">
                    <h5>Total Sales Amount</h5>
                    <h3>{{ number_format($totalSalesAmount, 2) }} TZS</h3>
                </div>
            </div>
            <div class="col-md-4 card-hover-effect">
                <div class="p-3 bg-info text-white rounded shadow-sm">
                    <h5>Total Transactions</h5>
                    <h3>{{ $totalTransactions }}</h3>
                </div>
            </div>
            <div class="col-md-4 card-hover-effect">
                <div class="p-3 bg-warning text-white rounded shadow-sm">
                    <h5>Products Sold</h5>
                    @forelse ($totalItemsSoldByUnit as $unit => $qty)
                        <div>{{ $qty }} {{ $unit }}</div>
                    @empty
                        <div>No sales</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Breakdown by Product --}}
        <div class="card shadow mb-4 card-hover-effect">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center">
                <h5 class="card-title mb-3 mb-md-0">Breakdown by Product</h5>
                <a href="{{ route('reports.products.download', request()->all()) }}" class="btn btn-sm btn-outline-success">
                    ⬇ Export CSV
                </a>
            </div>
            <div class="table-responsive mt-3">
                <table class="table table-striped table-bordered">
                    <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Product Name</th>
                        <th>Quantity Sold</th>
                        <th>Unit</th>
                        <th>Total Revenue (TZS)</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($productsBreakdown as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->total_quantity }}</td>
                            <td>{{ $item->unit_type }}</td>
                            <td>{{ number_format($item->total_revenue, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No sales data available for the selected period.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>


        {{-- Sales by User --}}
        <div class="card shadow card-hover-effect">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center">
                <h5 class="card-title mb-3 mb-md-0">Sales by User</h5>
                <a href="{{ route('reports.users.download', request()->all()) }}" class="btn btn-sm btn-outline-success">
                    ⬇ Export CSV
                </a>
            </div>
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                    <tr>
                        <th>User Name</th>
                        <th>Transactions</th>
                        <th>Sold Items (Categorized)</th>
                        <th>Total Revenue (TZS)</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($salesByUserBreakdown as $user)
                        <tr>
                            <td>{{ $user['user_name'] }}</td>
                            <td>{{ $user['transactions'] }}</td>
                            <td>
                                @foreach($user['products'] as $p)
                                    <div>{{ $p['total_quantity'] }} {{ $p['unit_type'] }} - {{ $p['product_name'] }}</div>
                                @endforeach
                            </td>
                            <td>{{ number_format($user['total_revenue'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No user sales data available.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
