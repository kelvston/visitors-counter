@extends('layouts.header')

@section('content')
    <div class="py-4">
        <h2 class="mb-4 text-xl font-bold">📦 Monthly Stock Movement (by Unit Type)</h2>

        {{-- Filters --}}
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <label>From</label>
                <input type="month" name="start" class="form-control" value="{{ request('start', now()->subMonths(5)->format('Y-m')) }}">
            </div>
            <div class="col-md-3">
                <label>To</label>
                <input type="month" name="end" class="form-control" value="{{ request('end', now()->format('Y-m')) }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-primary me-2">Filter</button>
                <a href="{{ route('reports.stockMovement') }}" class="btn btn-secondary">Clear</a>
            </div>
        </form>

        <div class="card shadow">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                    <tr>
                        <th>Month</th>
                        <th>Unit Type</th>
                        <th>Incoming</th>
                        <th>Outgoing</th>
                        <th>Net Movement</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($movement as $month)
                        @php
                            $rowSpan = $month['units']->count();
                        @endphp
                        @foreach ($month['units'] as $i => $unit)
                            <tr>
                                @if ($i === 0)
                                    <td rowspan="{{ $rowSpan }}">{{ $month['month'] }}</td>
                                @endif
                                <td>{{ $unit['unit_type'] }}</td>
                                <td>{{ number_format($unit['incoming'], 2) }}</td>
                                <td>{{ number_format($unit['outgoing'], 2) }}</td>
                                <td>{{ number_format($unit['net'], 2) }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
