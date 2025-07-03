@extends('layouts.header')

@section('content')
    <div class=" mt-4" >
        <div class="row">
            <!-- Add New Product Section -->
            <div class="col-md-7 breadcrumb card-hover-effect">
                <h2 class="mb-4">Add New Product</h2>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your input:<br><br>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('products.store_product') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label for="barcode" class="form-label">Barcode (optional)</label>
                            <input type="text" name="barcode" class="form-control" value="{{ old('barcode') }}">
                        </div>

                        <div class="col-md-6">
                            <label for="unit_type" class="form-label">Unit Type <span class="text-danger">*</span></label>
                            <select name="unit_type" class="form-select" required>
                                <option value="">-- Select Unit Type --</option>
                                <option value="item" {{ old('unit_type') == 'item' ? 'selected' : '' }}>Item</option>
                                <option value="kg" {{ old('unit_type') == 'kg' ? 'selected' : '' }}>KG</option>
                                <option value="litre" {{ old('unit_type') == 'litre' ? 'selected' : '' }}>Litre</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="cost_price" class="form-label">Unit cost <span class="text-danger">*</span></label>
                            <input type="number" name="cost_price" class="form-control" step="0.01" value="{{ old('cost_price') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="price_per_unit" class="form-label">Sell Price per Unit <span class="text-danger">*</span></label>
                            <input type="number" name="price_per_unit" class="form-control" step="0.01" value="{{ old('price_per_unit') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label for="stock" class="form-label">Initial Stock <span class="text-danger">*</span></label>
                            <input type="number" name="stock" class="form-control" step="0.01" value="{{ old('stock') }}" required>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left-circle"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Save Product
                        </button>
                    </div>
                </form>
            </div>

            <!-- Bulk Upload Section -->
            <div class="col-md-5">
                <div class="p-4 mt-2 breadcrumb">
                    <h4 class="mb-3">Bulk Upload Products</h4>

                    <a href="{{ asset('templates/excel_product_sheet.xlsx') }}" class="btn btn-link p-0 mb-2">
                        <i class="bi bi-download"></i> Download Sample Template
                    </a>

                    <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="file" class="form-label">Upload Excel File</label>
                            <input type="file" name="file" class="form-control" required accept=".xlsx,.xls">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload"></i> Import
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @if (session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = "{{ route('products.index') }}";
                });
            });
        </script>
    @endif

@endsection
