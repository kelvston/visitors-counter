@extends('layouts.header')

@section('content')
    <div class="container py-4 card-hover-effect">
        <h2 class="mb-4 text-xl font-bold">📊 Expenses Report</h2>

        <form method="GET" action="#" class="mb-6 bg-white p-4 rounded shadow">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ $start }}" class="form-control" />
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ $end }}" class="form-control" />
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>

        <div class="card shadow p-4">
            <h4>Summary</h4>
            <p>Total Expenses: <strong>{{ number_format($totalExpenses, 2) }} TZS</strong></p>

            <h5>Expenses by Category</h5>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Category</th>
                    <th>Total Amount (TZS)</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($expensesByCategory as $expense)
                    <tr>
                        <td>{{ ucfirst($expense->category) }}</td>
                        <td>{{ number_format($expense->total_amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center">No expenses found for this period.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
