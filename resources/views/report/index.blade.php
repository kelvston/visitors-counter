@extends('layouts.header')

@section('content')
    <h2 class="mb-2 ">📊 Reports Dashboard</h2>

    <div class="py-4 px-3 breadcrumb">
        <div class="row g-4">

            {{-- 1. Sales Report --}}
            <div class="col-md-6">
                <div class="p-4 rounded-2xl shadow bg-white hover:shadow-lg transition card-hover-effect h-100">
                    <h3 class="text-lg font-semibold mb-1">🧾 Sales Report</h3>
                    <p class="text-sm text-gray-600">Track sales by date, product, user and quantity sold.</p>
                    <a href="{{ route('reports.sales') }}" class="text-blue-600 mt-2 inline-block">View Sales Report →</a>
                </div>
            </div>

            {{-- 2. Profit & Loss --}}
            <div class="col-md-6">
                <div class="p-4 rounded-2xl shadow bg-white hover:shadow-lg transition card-hover-effect h-100">
                    <h3 class="text-lg font-semibold mb-1">💰 Profit & Loss</h3>
                    <p class="text-sm text-gray-600">Understand your profitability by day, week, month, or product.</p>
                    <a href="{{ route('reports.profitLoss') }}" class="text-blue-600 mt-2 inline-block">View P&L Report →</a>
                </div>
            </div>

            {{-- 3. Stock Report --}}
            <div class="col-md-6">
                <div class="p-4 rounded-2xl shadow bg-white hover:shadow-lg transition card-hover-effect h-100">
                    <h3 class="text-lg font-semibold mb-1">📦 Stock Report</h3>
                    <p class="text-sm text-gray-600">Monitor stock levels, value, and low stock alerts.</p>
                    <a href="{{ route('reports.stock') }}" class="text-blue-600 mt-2 inline-block">View Stock Report →</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-4 rounded-2xl shadow bg-white hover:shadow-lg transition card-hover-effect h-100">
                    <h3 class="text-lg font-semibold mb-1">📦 Stock Movement</h3>
                    <p class="text-sm text-gray-600">Monitor stock levels, value, and low stock alerts.</p>
                    <a href="{{ route('reports.stockMovement') }}" class="text-blue-600 mt-2 inline-block">View Stock Report →</a>
                </div>
            </div>

            {{-- 4. Expenses Report --}}
            <div class="col-md-6">
                <div class="p-4 rounded-2xl shadow bg-white hover:shadow-lg transition card-hover-effect h-100">
                    <h3 class="text-lg font-semibold mb-1">🧾 Expenses Report</h3>
                    <p class="text-sm text-gray-600">View and categorize your business expenses.</p>
                    <a href="{{ route('reports.expensess') }}" class="text-blue-600 mt-2 inline-block">View Expenses Report →</a>
                </div>
            </div>

            {{-- 5. Top Selling --}}
            <div class="col-md-6">
                <div class="p-4 rounded-2xl shadow bg-white hover:shadow-lg transition card-hover-effect h-100">
                    <h3 class="text-lg font-semibold mb-1">🔥 General Report</h3>
                    <p class="text-sm text-gray-600">See what’s selling best and when.</p>
                    <a href="{{ route('reports.summary.pdf') }}" class="text-blue-600 mt-2 inline-block">View Top Sellers →</a>
                </div>
            </div>


        </div>
    </div>
@endsection
