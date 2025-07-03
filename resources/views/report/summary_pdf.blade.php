<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>General Business Report</title>
    <style>
        /* Base styles for PDF rendering */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            margin: 20px;
            background: #fff;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        h1 {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 10px;
        }
        h2 {
            font-size: 18px;
            font-weight: 600;
            color: #374151;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        h2 .icon {
            margin-right: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #f9fafb;
            font-weight: 600;
            color: #4b5563;
        }
        tr:nth-child(even) {
            background: #f9fafb;
        }
        .font-bold {
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-sm {
            font-size: 11px;
        }
        .text-gray-600 {
            color: #4b5563;
        }
        .bg-gray-50 {
            background: #f9fafb;
        }
        .bg-blue-50 {
            background: #eff6ff;
        }
        .text-blue-600 {
            color: #2563eb;
        }
        .p-4 {
            padding: 16px;
        }
        .mb-4 {
            margin-bottom: 16px;
        }
        .mb-10 {
            margin-bottom: 40px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
        .list-disc {
            list-style: disc;
            padding-left: 20px;
        }
        .footer {
            margin-top: 20px;
            font-size: 10px;
            color: #6b7280;
            text-align: right;
        }
        /* Ensure page breaks are handled */
        .section {
            page-break-inside: avoid;
        }
        @page {
            margin: 20mm;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>General Business Summary</h1>

    <div class="section mb-10">
        <h2><span class="icon">🧾</span>Sales Report</h2>
        <div class="grid mb-4">
            <div class="bg-blue-50 p-4">
                <p class="text-sm text-gray-600">Total Sales</p>
                <p class="font-bold text-blue-600">{{ number_format($totalSales, 2) }} TZS</p>
            </div>
            <div class="bg-blue-50 p-4">
                <p class="text-sm text-gray-600">Total Transactions</p>
                <p class="font-bold text-blue-600">{{ $totalTransactions }}</p>
            </div>
        </div>
        <p class="text-sm text-gray-600 mb-2">Items Sold:</p>
        <ul class="list-disc">
            @foreach($productsSoldByUnit as $item)
                <li>{{ $item->qty }} {{ $item->unit_type }}</li>
            @endforeach
        </ul>
    </div>

    <div class="section mb-10">
        <h2>Profit & Loss</h2>
        <table>
            <tbody>
            <tr>
                <th class="p-4">Revenue</th>
                <td class="p-4">{{ number_format($revenue, 2) }} TZS</td>
            </tr>
            <tr>
                <th class="p-4">Cost of Goods Sold</th>
                <td class="p-4">{{ number_format($cost, 2) }} TZS</td>
            </tr>
            <tr>
                <th class="p-4">Expenses</th>
                <td class="p-4">{{ number_format($expenses, 2) }} TZS</td>
            </tr>
            <tr class="font-bold bg-gray-50">
                <th class="p-4">Net Profit</th>
                <td class="p-4">{{ number_format($profit, 2) }} TZS</td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="section mb-10">
        <h2>Stock Report</h2>
        <table>
            <thead>
            <tr class="bg-gray-50">
                <th class="p-4">Product</th>
                <th class="p-4">Stock</th>
                <th class="p-4">Cost</th>
                <th class="p-4">Sell</th>
                <th class="p-4">Profit/Unit</th>
                <th class="p-4">Value</th>
                <th class="p-4">Total Profit</th>
            </tr>
            </thead>
            <tbody>
            @foreach($stockItems as $item)
                <tr>
                    <td class="p-4">{{ $item['name'] }}</td>
                    <td class="p-4">{{ $item['stock'] }}</td>
                    <td class="p-4">{{ number_format($item['cost'], 2) }}</td>
                    <td class="p-4">{{ number_format($item['sell'], 2) }}</td>
                    <td class="p-4">{{ number_format($item['margin'], 2) }}</td>
                    <td class="p-4">{{ number_format($item['value'], 2) }}</td>
                    <td class="p-4">{{ number_format($item['profit'], 2) }}</td>
                </tr>
            @endforeach
            <tr class="font-bold bg-gray-50">
                <th class="p-4" colspan="5">Total Stock Value</th>
                <th class="p-4">{{ number_format($totalStockValue, 2) }} TZS</th>
                <th class="p-4"></th>
            </tr>
            <tr class="font-bold bg-gray-50">
                <th class="p-4" colspan="6">Total Potential Profit</th>
                <th class="p-4">{{ number_format($totalStockProfitValue, 2) }} TZS</th>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="section mb-10">
        <h2>Expenses Report</h2>
        <table>
            <thead>
            <tr class="bg-gray-50">
                <th class="p-4">Category</th>
                <th class="p-4">Total</th>
            </tr>
            </thead>
            <tbody>
            @foreach($expenseBreakdown as $row)
                <tr>
                    <td class="p-4">{{ $row->category }}</td>
                    <td class="p-4">{{ number_format($row->total, 2) }} TZS</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="section mb-10">
        <h2>Top Selling Products</h2>
        <table>
            <thead>
            <tr class="bg-gray-50">
                <th class="p-4">Product</th>
                <th class="p-4">Qty Sold</th>
                <th class="p-4">Total Revenue</th>
                <th class="p-4">Profit</th>
            </tr>
            </thead>
            <tbody>
            @php
                $totalTopQty = 0;
                $totalTopRevenue = 0;
                $totalTopProfit = 0;
            @endphp
            @foreach($topProducts as $product)
                @php
                    $totalTopQty += $product->total_qty;
                    $totalTopRevenue += $product->total_revenue;
                    $totalTopProfit += $product->profit;
                @endphp
                <tr>
                    <td class="p-4">{{ $product->product->name ?? 'N/A' }}</td>
                    <td class="p-4">{{ $product->total_qty }}</td>
                    <td class="p-4">{{ number_format($product->total_revenue, 2) }} TZS</td>
                    <td class="p-4">{{ number_format($product->profit, 2) }} TZS</td>
                </tr>
            @endforeach
            <tr class="font-bold bg-gray-50">
                <th class="p-4">Total</th>
                <th class="p-4">{{ $totalTopQty }}</th>
                <th class="p-4">{{ number_format($totalTopRevenue, 2) }} TZS</th>
                <th class="p-4">{{ number_format($totalTopProfit, 2) }} TZS</th>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="section mb-10">
        <h2><span class="icon">👤</span>User Performance</h2>
        <table>
            <thead>
            <tr class="bg-gray-50">
                <th class="p-4">User</th>
                <th class="p-4">Revenue</th>
                <th class="p-4">Cost</th>
                <th class="p-4">Profit</th>
            </tr>
            </thead>
            <tbody>
            @php
                $totalUserRevenue = 0;
                $totalUserCost = 0;
                $totalUserProfit = 0;
            @endphp
            @foreach($userPerformance as $user)
                @php
                    $totalUserRevenue += $user->revenue;
                    $totalUserCost += $user->cost;
                    $totalUserProfit += $user->profit;
                @endphp
                <tr>
                    <td class="p-4">{{ $user->user->name ?? 'N/A' }}</td>
                    <td class="p-4">{{ number_format($user->revenue, 2) }} TZS</td>
                    <td class="p-4">{{ number_format($user->cost, 2) }} TZS</td>
                    <td class="p-4">{{ number_format($user->profit, 2) }} TZS</td>
                </tr>
            @endforeach
            <tr class="font-bold bg-gray-50">
                <th class="p-4">Total</th>
                <th class="p-4">{{ number_format($totalUserRevenue, 2) }} TZS</th>
                <th class="p-4">{{ number_format($totalUserCost, 2) }} TZS</th>
                <th class="p-4">{{ number_format($totalUserProfit, 2) }} TZS</th>
            </tr>
            </tbody>
        </table>
    </div>

    <p class="footer">Generated at {{ now()->format('Y-m-d H:i') }}</p>
</div>
</body>
</html>
