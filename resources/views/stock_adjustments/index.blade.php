@extends('layouts.header')

@section('content')
    <div class=" mt-4 card-hover-effect">
        <h2 class="mb-4">Stock Adjustment Logs</h2>

        <form method="GET" action="{{ route('stock_adjustments.index') }}" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="text" name="product_name" class="form-control" placeholder="Search by Product" value="{{ request('product_name') }}">
            </div>
            <div class="col-md-3">
                <select name="action" class="form-select">
                    <option value="">All Actions</option>
                    <option value="add" {{ request('action') == 'add' ? 'selected' : '' }}>Add</option>
                    <option value="remove" {{ request('action') == 'remove' ? 'selected' : '' }}>Remove</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('stock_adjustments.export', request()->query()) }}" class="btn btn-success">Export Excel</a>
                <a href="{{ route('stock_adjustments.export_pdf', request()->query()) }}" class="btn btn-danger">Export PDF</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Action</th>
                    <th>Quantity</th>
                    <th>Reason</th>
                    <th>Adjusted By</th>
                </tr>
                </thead>
                <tbody>
                @forelse($adjustments as $adjustment)
                    <tr>
                        <td>{{ $adjustment->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $adjustment->product->name }}</td>
                        <td>
                                <span class="badge bg-{{ $adjustment->action == 'add' ? 'success' : 'danger' }}">
                                    {{ ucfirst($adjustment->action) }}
                                </span>
                        </td>
                        <td>{{ number_format($adjustment->quantity, 2) }}</td>
                        <td>{{ $adjustment->reason ?? '-' }}</td>
                        <td>{{ $adjustment->user->name ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No adjustment records found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $adjustments->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
