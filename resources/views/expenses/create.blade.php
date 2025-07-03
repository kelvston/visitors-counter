@extends('layouts.header')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4 text-xl font-bold">➕ Add New Expense</h2>

        <form action="{{ route('expenses.store') }}" method="POST" class="w-100" style="max-width: 600px;">
            @csrf

            <div class="mb-3">
                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                <select name="category" id="category" class="form-control @error('category') is-invalid @enderror" required>
                    <option value="">Select Category</option>
                    @foreach(\App\Models\Expense::CATEGORIES as $cat)
                        <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                    @endforeach
                </select>
                @error('category')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="amount" class="form-label">Amount (TZS) <span class="text-danger">*</span></label>
                <input type="number" name="amount" id="amount" min="0" step="0.01"
                       value="{{ old('amount') }}" class="form-control @error('amount') is-invalid @enderror" required>
                @error('amount')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description (optional)</label>
                <input type="text" name="description" id="description" value="{{ old('description') }}" class="form-control">
                @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                <input type="date" name="date" id="date" value="{{ old('date', now()->format('Y-m-d')) }}"
                       class="form-control @error('date') is-invalid @enderror" required>
                @error('date')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Expense</button>
                <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
