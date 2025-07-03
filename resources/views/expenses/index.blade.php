@extends('layouts.header')

@section('content')
    <div class=" py-4 card-hover-effect">
        <h2 class="mb-4 text-xl font-bold">📋 Expenses List</h2>

        {{-- Filter Form --}}
        <form method="GET" action="{{ route('expenses.index') }}" class="mb-4 row g-3 align-items-end">
            <div class="col-md-3">
                <label for="category" class="form-label">Category</label>
                <select name="category" id="category" class="form-control">
                    <option value="">All Categories</option>
                    @foreach(\App\Models\Expense::CATEGORIES as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="from_date" class="form-label">From Date</label>
                <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="to_date" class="form-label">To Date</label>
                <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}" class="form-control">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
                <a href="{{ route('expenses.index') }}" class="btn btn-secondary w-100">Clear</a>
            </div>
        </form>

        {{-- Add new expense button --}}
        <div class="mb-3">
            <a href="{{ route('expenses.create') }}" class="btn btn-success">+ Add New Expense</a>
        </div>

        {{-- Expenses Table --}}
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Amount (TZS)</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($expenses as $expense)
                    <tr>
                        <td>{{ $expenses->firstItem() + $loop->index }}</td>
                        <td>{{ ucfirst($expense->category) }}</td>
                        <td>{{ $expense->description ?? '-' }}</td>
                        <td>{{ number_format($expense->amount, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($expense->date)->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-sm btn-primary">Edit</a>
                            <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Are you sure you want to delete this expense?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No expenses found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-3">
            {{ $expenses->withQueryString()->links() }}
        </div>
    </div>
@endsection
