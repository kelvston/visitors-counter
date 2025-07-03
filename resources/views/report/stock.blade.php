@extends('layouts.header')

@section('content')
    <div class="py-4 ">
        <div class="d-flex justify-content-between align-items-center mb-3 breadcrumb card-hover-effect">
            <h2 class="text-xl font-bold">📦 Stock Report</h2>
            <div>
                <a href="{{ route('reports.stock.export') }}" class="btn btn-sm btn-success">⬇ CSV</a>
                <button onclick="window.print()" class="btn btn-sm btn-secondary">🖨 Print</button>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card-hover-effect">
        <form method="GET" class="row g-3 mb-3 ">
            <div class="col-md-4">
                <input type="text" name="product_name" class="form-control" placeholder="🔍 Filter by product name" value="{{ request('product_name') }}">
            </div>
            <div class="col-md-2 form-check pt-2">
                <input class="form-check-input" type="checkbox" name="low_stock" id="low_stock" {{ request('low_stock') ? 'checked' : '' }}>
                <label class="form-check-label" for="low_stock">Show Only Low Stock</label>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('reports.stock') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>

        {{-- Stock Table --}}
        <div class="card shadow ">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Stock</th>
                            <th>Cost Price</th>
                            <th>Selling Price</th>
                            <th>Profit/Unit</th>
                            <th>Stock Value</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($stockReport as $index => $product)
                            <tr @if($product['low_stock']) class="table-danger" @endif>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $product['product_name'] }}</td>
                                <td>{{ $product['stock_quantity'] }}</td>
                                <td>{{ number_format($product['cost_price'], 2) }}</td>
                                <td>{{ number_format($product['selling_price'], 2) }}</td>
                                <td>{{ number_format($product['profit_margin'], 2) }}</td>
                                <td>{{ number_format($product['stock_value'], 2) }}</td>
                                <td>
                                    @if ($product['low_stock'])
                                        <span class="badge bg-danger">Low</span>
                                    @else
                                        <span class="badge bg-success">OK</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No products match the filter.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Total Stock Value --}}
                @php
                    $totalValue = $stockReport->sum('stock_value');
                @endphp
                <div class="mt-3 text-end">
                    <strong>Total Stock Value:</strong> {{ number_format($totalValue, 2) }} TZS
                </div>
            </div>
        </div>
        </div>
    </div>
@endsection
