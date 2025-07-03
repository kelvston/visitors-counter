@extends('layouts.header')

@section('content')
    <div class="py-4">
        <h2 class="mb-4 text-xl font-bold">💰 Profit & Loss Report</h2>

        {{-- Filter Form --}}
        <form method="GET" action="{{ route('reports.profitLoss') }}" class="mb-4 bg-white p-4 rounded shadow-sm">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control"
                           value="{{ \Carbon\Carbon::parse($start)->format('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control"
                           value="{{ \Carbon\Carbon::parse($end)->format('Y-m-d') }}">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-50">Filter</button>
                    <a href="{{ route('reports.profitLoss') }}" class="btn btn-secondary w-50">Clear</a>
                </div>
            </div>
        </form>

        {{-- Summary Cards --}}
        <div class="row text-white mb-4">
            <div class="col-md-3">
                <div class="p-4 bg-success rounded shadow-sm">
                    <h5>Revenue</h5>
                    <h3>{{ number_format($revenue, 2) }} TZS</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4 bg-danger rounded shadow-sm">
                    <h5>Cost of Goods Sold</h5>
                    <h3>{{ number_format($cost, 2) }} TZS</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4 bg-warning rounded shadow-sm">
                    <h5>Expenses</h5>
                    <h3>{{ number_format($expenses, 2) }} TZS</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4 bg-info rounded shadow-sm">
                    <h5>Profit</h5>
                    <h3>{{ number_format($profit, 2) }} TZS</h3>
                </div>
            </div>
        </div>
        {{-- Breakdown by Product and Day --}}

        <div class="card mt-4">

            <div class="card-body">
                <h5 class="card-title mb-3">📆 Daily Profit Breakdown by Product</h5>
                <a href="{{ route('reports.profitLoss.download', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                   class="btn btn-outline-success btn-sm float-end">
                    ⬇ Export CSV
                </a>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Revenue (TZS)</th>
                            <th>Cost (TZS)</th>
                            <th>Profit (TZS)</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($salesBreakdown as $row)
                            <tr>
                                <td>{{ $row->sale_date }}</td>
                                <td>{{ $row->product_name }}</td>
                                <td>{{ number_format($row->revenue, 2) }}</td>
                                <td>{{ number_format($row->cost, 2) }}</td>
                                <td>{{ number_format($row->revenue - $row->cost, 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <canvas id="profitChart" height="100"></canvas>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('profitChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($salesBreakdown->pluck('sale_date')->unique()),
                datasets: [{
                    label: 'Profit (TZS)',
                    data: @json(
                    $salesBreakdown
                        ->groupBy('sale_date')
                        ->map(fn($group) => $group->sum(fn($row) => $row->revenue - $row->cost))
                        ->values()
                ),
                    backgroundColor: 'rgba(54, 162, 235, 0.3)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    fill: true,
                    tension: 0.2,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    y: { beginAtZero: true },
                    x: { title: { display: true, text: 'Date' } }
                }
            }
        });
    </script>

@endsection
