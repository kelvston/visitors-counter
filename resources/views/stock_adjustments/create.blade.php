@extends('layouts.header')

@section('content')
    <div class="mt-4 card-hover-effect">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow rounded-3">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Adjust Product Stock</h4>
                        <a href="{{ route('inventory.audit-log') }}" class="btn btn-light btn-sm">
                            <i class="bi bi-journal-text"></i> Audit Log
                        </a>
                    </div>

                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Manual Adjustment Form --}}
                        <form action="{{ route('stock.adjust') }}" method="POST" class="mb-4">
                            @csrf
                            <div class="mb-3">
                                <label for="product_id" class="form-label">Product</label>
                                <select name="product_id" class="form-select" required>
                                    <option value="">-- Select Product --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }} (Stock: {{ $product->stock }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="type" class="form-label">Adjustment Type</label>
                                <select name="type" class="form-select" required>
                                    <option value="add">Add Stock</option>
                                    <option value="remove">Remove Stock</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" step="0.01" name="quantity" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="reason" class="form-label">Reason (optional)</label>
                                <input type="text" name="reason" class="form-control">
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('products.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left-circle"></i> Back
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Submit Adjustment
                                </button>
                            </div>
                        </form>

                        <hr>

                        {{-- Excel Upload for Bulk Adjustment --}}
                        <h5 class="mb-3">Bulk Stock Adjustment via Excel</h5>

                        <a href="{{ route('inventory.export-template') }}" class="btn btn-link p-0 mb-2">
                            <i class="bi bi-download"></i> Download Excel Template
                        </a>

                        <form action="{{ route('stock.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="file" class="form-label">Upload Excel File</label>
                                <input type="file" name="file" class="form-control" required accept=".xlsx,.xls">
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload"></i> Import Adjustments
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
