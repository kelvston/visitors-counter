<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Receipt - {{ $receipt->receipt_number }}</title>
    <style>
        /* Reset and base */
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            margin: 0;
            padding: 1.5rem;
            background: #fff;
        }
        h1, h2, h3, h4, h5, h6 {
            margin: 0 0 0.5rem 0;
            font-weight: 700;
            color: #2c3e50;
        }

        .container {
            max-width: 700px;
            margin: auto;
            border: 1px solid #ddd;
            padding: 1.5rem;
            border-radius: 6px;
            box-shadow: 0 0 10px #eee;
        }

        .header {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .header h1 {
            font-size: 1.75rem;
            letter-spacing: 1.2px;
            color: #27ae60;
        }
        .header p {
            margin: 0;
            font-size: 0.9rem;
            color: #555;
        }

        .receipt-info {
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            color: #555;
            display: flex;
            justify-content: space-between;
        }

        .receipt-info div {
            width: 48%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }

        table th,
        table td {
            padding: 10px 8px;
            border: 1px solid #ccc;
            text-align: left;
            font-size: 0.95rem;
        }

        table th {
            background-color: #f7f9f9;
        }

        .total-row td {
            font-weight: 700;
            font-size: 1.1rem;
            background-color: #f0f4f7;
        }

        .footer {
            text-align: center;
            font-size: 0.85rem;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 1rem;
            margin-top: 2rem;
        }

        /* Print friendly */
        @media print {
            body {
                margin: 0;
                padding: 0;
                box-shadow: none;
                -webkit-print-color-adjust: exact;
            }
            .container {
                border: none;
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>{{ $shopName }}</h1>
        <p>{{ $shopAddress }}</p>
        <p>Phone: {{ $contactPhone }}</p>
        <p>Email: {{ $contactEmail }}</p>
        <p>Receipt</p>
    </div>

    <div class="receipt-info">
        <div>
            <strong>Receipt Number:</strong><br />
            {{ $receipt->receipt_number }}<br />
            <strong>Date:</strong><br />
            {{ $receipt->printed_at ? $receipt->printed_at->format('d M Y, H:i') : now()->format('d M Y, H:i') }}<br />
            <strong>Cashier:</strong><br />
            {{ $receipt->user->name ?? 'N/A' }}
        </div>
        <div>
            <strong>Customer:</strong><br />
            <!-- Optional customer details if available -->
            Walk-in Customer<br />
            <strong>Payment Method:</strong><br />
            Cash
        </div>
    </div>

    @php
        $items = json_decode($receipt->receipt_data, true) ?: [];
    @endphp

    <table>
        <thead>
        <tr>
            <th>Item</th>
            <th>Unit Type</th>
            <th>Quantity</th>
            <th>Unit Price (TZS)</th>
            <th>Subtotal (TZS)</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($items as $item)
            <tr>
                <td>{{ $item['item'] }}</td>
                <td>{{ $item['unit_type'] }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td>{{ number_format($item['unit_price'], 2) }}</td>
                <td>{{ number_format($item['subtotal'], 2) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr class="total-row">
            <td colspan="4" style="text-align:right">Total:</td>
            <td>{{ number_format($receipt->total_amount, 2) }}</td>
        </tr>
        </tfoot>
    </table>


    <div class="footer">
        {{$footer}}<br />
        <small>Powered by Phage Solutions ICT - East Africa</small>
    </div>
</div>
</body>
</html>
