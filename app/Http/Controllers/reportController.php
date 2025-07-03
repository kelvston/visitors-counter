<?php

namespace App\Http\Controllers;


use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Expense;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;


class ReportController extends Controller
{
    public function index()
    {
        return view('report.index');
    }

    public function sales(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $userId = $request->input('user');
        $productId = $request->input('product');  // new

        $query = Sale::query();

        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($productId) {
            $query->where('product_id', $productId);
        }

        $sales = $query->get();

        $totalSalesAmount = $sales->sum('subtotal');
        $totalTransactions = $sales->count();

        $totalItemsSoldByUnit = $sales
            ->groupBy('unit_type')
            ->map(fn($group) => $group->sum('quantity'))
            ->toArray();

        $productsBreakdownQuery = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(
                'products.name as product_name',
                'sale_items.unit_type',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue')
            )
            ->when($from, fn($q) => $q->whereDate('sale_items.created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('sale_items.created_at', '<=', $to))
            ->when($userId, fn($q) => $q->where('sale_items.user_id', $userId))
            ->when($productId, fn($q) => $q->where('sale_items.product_id', $productId))
            ->groupBy('products.name', 'sale_items.unit_type')
            ->orderBy('products.name');

        $productsBreakdown = $productsBreakdownQuery->get();

        $rawSalesByUserQuery = DB::table('sale_items')
            ->join('users', 'sale_items.user_id', '=', 'users.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(
                'users.id as user_id',
                'users.name as user_name',
                'products.name as product_name',
                'sale_items.unit_type',
                DB::raw('COUNT(*) as transactions'),
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue')
            )
            ->when($from, fn($q) => $q->whereDate('sale_items.created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('sale_items.created_at', '<=', $to))
            ->when($userId, fn($q) => $q->where('sale_items.user_id', $userId))
            ->when($productId, fn($q) => $q->where('sale_items.product_id', $productId))
            ->groupBy('users.id', 'users.name', 'products.name', 'sale_items.unit_type')
            ->orderBy('users.name')
            ->orderBy('products.name');

        $rawSalesByUser = $rawSalesByUserQuery->get();

        // Group salesByUserBreakdown as before
        $salesByUserBreakdown = [];
        foreach ($rawSalesByUser as $row) {
            $uid = $row->user_id;
            if (!isset($salesByUserBreakdown[$uid])) {
                $salesByUserBreakdown[$uid] = [
                    'user_name' => $row->user_name,
                    'transactions' => 0,
                    'total_revenue' => 0,
                    'products' => []
                ];
            }
            $salesByUserBreakdown[$uid]['transactions'] += $row->transactions;
            $salesByUserBreakdown[$uid]['total_revenue'] += $row->total_revenue;
            $salesByUserBreakdown[$uid]['products'][] = [
                'product_name' => $row->product_name,
                'unit_type' => $row->unit_type,
                'total_quantity' => $row->total_quantity,
            ];
        }
        $salesByUserBreakdown = array_values($salesByUserBreakdown);

        $users = DB::table('users')->select('id', 'name')->get();
        $products = DB::table('products')->select('id', 'name')->orderBy('name')->get();

        return view('report.sales', compact(
            'totalSalesAmount',
            'totalTransactions',
            'totalItemsSoldByUnit',
            'productsBreakdown',
            'salesByUserBreakdown',
            'users',
            'products',
            'productId'
        ));
    }



    public function downloadCSV(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $userId = $request->input('user');

        $sales = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('users', 'sale_items.user_id', '=', 'users.id')
            ->select(
                'products.name as product_name',
                'sale_items.quantity',
                'sale_items.unit_type',
                'sale_items.unit_price',
                'sale_items.subtotal',
                'users.name as user_name',
                'sale_items.created_at'
            )
            ->when($from, fn($q) => $q->whereDate('sale_items.created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('sale_items.created_at', '<=', $to))
            ->when($userId, fn($q) => $q->where('sale_items.user_id', $userId))
            ->orderBy('sale_items.created_at')
            ->get();

        $filename = "sales_report.csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $columns = [
            'Product Name',
            'Quantity',
            'Unit Type',
            'Unit Price',
            'Subtotal',
            'Sold By',
            'Sold At',
        ];

        $callback = function () use ($sales, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($sales as $sale) {
                fputcsv($file, [
                    $sale->product_name,
                    $sale->quantity,
                    $sale->unit_type,
                    $sale->unit_price,
                    $sale->subtotal,
                    $sale->user_name,
                    \Carbon\Carbon::parse($sale->created_at)->format('n/j/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }



    public function profitLoss(Request $request)
    {
        $start = $request->input('start_date', now()->startOfMonth());
        $end = $request->input('end_date', now());

        $sales = Sale::whereBetween('sale_items.created_at', [$start, $end])
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(
                'products.name as product_name',
                DB::raw('DATE(sale_items.created_at) as sale_date'),
                DB::raw('SUM(sale_items.quantity * sale_items.unit_price) as revenue'),
                DB::raw('SUM(sale_items.quantity * products.cost_price) as cost')
            )
            ->groupBy('products.name', DB::raw('DATE(sale_items.created_at)'))
            ->orderBy('sale_date')
            ->get();

        $expenses = Expense::whereBetween('created_at', [$start, $end])->sum('amount');

        $totalRevenue = $sales->sum('revenue');
        $totalCost = $sales->sum('cost');
        $profit = $totalRevenue - $totalCost - $expenses;

        return view('report.profit_loss', [
            'salesBreakdown' => $sales,
            'revenue' => $totalRevenue,
            'cost' => $totalCost,
            'expenses' => $expenses,
            'profit' => $profit,
            'start' => $start,
            'end' => $end,
        ]);
    }




    public function stock(Request $request)
    {
        $query = Product::query();

        if ($request->filled('product_name')) {
            $query->where('name', 'LIKE', '%' . $request->product_name . '%');
        }

        $products = $query->get();

        $lowStockOnly = $request->boolean('low_stock');

        $stockReport = $products->map(function ($product) use ($lowStockOnly) {
            $data = [
                'product_name' => $product->name,
                'stock_quantity' => $product->stock,
                'cost_price' => $product->cost_price,
                'selling_price' => $product->price_per_unit ?? 0,
                'stock_value' => $product->stock * $product->cost_price,
                'profit_margin' => ($product->price_per_unit ?? 0) - $product->cost_price,
                'low_stock' => $product->stock < 10,
            ];

            return $data;
        });

        if ($lowStockOnly) {
            $stockReport = $stockReport->filter(fn($p) => $p['low_stock'])->values();
        }

        return view('report.stock', compact('stockReport'));
    }


    public function expenses(Request $request)
    {
        $start = $request->input('start_date', now()->startOfMonth());
        $end = $request->input('end_date', now());

        $expenses = Expense::whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('category');

        $total = $expenses->flatten()->sum('amount');

        return view('report.expenses', compact('expenses', 'total', 'start', 'end'));
    }

    public function expensesReport(Request $request)
    {
        $start = $request->input('start_date', now()->startOfMonth()->toDateString());
        $end = $request->input('end_date', now()->toDateString());

        $query = \DB::table('expenses')
            ->select('category', \DB::raw('SUM(amount) as total_amount'))
            ->whereBetween('date', [$start, $end])
            ->groupBy('category');

        $expensesByCategory = $query->get();

        $totalExpenses = $expensesByCategory->sum('total_amount');

        return view('report.expenses', compact('expensesByCategory', 'totalExpenses', 'start', 'end'));
    }


    public function topSelling(Request $request)
    {
        $start = $request->input('start_date', now()->startOfMonth());
        $end = $request->input('end_date', now());

        $topProducts = Sale::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('product_id')
            ->with('product')
            ->orderByDesc('total_quantity')
            ->take(10)
            ->get();

        return view('reports.top_selling', compact('topProducts', 'start', 'end'));
    }

    public function userPerformance(Request $request)
    {
        $start = $request->input('start_date', now()->startOfMonth());
        $end = $request->input('end_date', now());

        $userPerformance = Sale::with('user', 'items')
            ->whereBetween('created_at', [$start, $end])
            ->get()
            ->groupBy('user_id')
            ->map(function ($sales) {
                $totalRevenue = 0;
                $totalProfit = 0;

                foreach ($sales as $sale) {
                    foreach ($sale->items as $item) {
                        $totalRevenue += $item->quantity * $item->selling_price;
                        $totalProfit += ($item->selling_price - $item->cost_price) * $item->quantity;
                    }
                }

                return [
                    'user_name' => $sales->first()->user->name ?? 'Unknown',
                    'total_sales' => $sales->count(),
                    'revenue' => $totalRevenue,
                    'profit' => $totalProfit,
                ];
            });

        return view('reports.user_performance', compact('userPerformance', 'start', 'end'));
    }

    public function downloadProductsCSV(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $userId = $request->input('user');

        $products = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(
                'products.name as product_name',
                'sale_items.unit_type',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue')
            )
            ->when($from, fn($q) => $q->whereDate('sale_items.created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('sale_items.created_at', '<=', $to))
            ->when($userId, fn($q) => $q->where('sale_items.user_id', $userId))
            ->groupBy('products.name', 'sale_items.unit_type')
            ->orderBy('products.name')
            ->get();

        $filename = "products_breakdown_" . now()->format('Ymd_His') . ".csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $columns = [
            'Product Name',
            'Unit',
            'Quantity Sold',
            'Total Revenue (TZS)'
        ];

        $callback = function () use ($products, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->product_name,
                    $product->unit_type,
                    $product->total_quantity,
                    number_format($product->total_revenue, 2),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function downloadUsersCSV(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $userId = $request->input('user');

        $rawSalesByUser = DB::table('sale_items')
            ->join('users', 'sale_items.user_id', '=', 'users.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(
                'users.name as user_name',
                'products.name as product_name',
                'sale_items.unit_type',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue')
            )
            ->when($from, fn($q) => $q->whereDate('sale_items.created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('sale_items.created_at', '<=', $to))
            ->when($userId, fn($q) => $q->where('sale_items.user_id', $userId))
            ->groupBy('users.name', 'products.name', 'sale_items.unit_type')
            ->orderBy('users.name')
            ->orderBy('products.name')
            ->get();

        $filename = "sales_by_user_" . now()->format('Ymd_His') . ".csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $columns = [
            'User Name',
            'Product Name',
            'Unit',
            'Quantity Sold',
            'Total Revenue (TZS)',
        ];

        $callback = function () use ($rawSalesByUser, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($rawSalesByUser as $row) {
                fputcsv($file, [
                    $row->user_name,
                    $row->product_name,
                    $row->unit_type,
                    $row->total_quantity,
                    number_format($row->total_revenue, 2),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadProfitLossCSV(Request $request)
    {
        $start = $request->input('start_date', now()->startOfMonth());
        $end = $request->input('end_date', now());

        $sales = Sale::whereBetween('sale_items.created_at', [$start, $end])
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(
                'products.name as product_name',
                DB::raw('DATE(sale_items.created_at) as sale_date'),
                DB::raw('SUM(sale_items.quantity * sale_items.unit_price) as revenue'),
                DB::raw('SUM(sale_items.quantity * products.cost_price) as cost')
            )
            ->groupBy('products.name', DB::raw('DATE(sale_items.created_at)'))
            ->orderBy('sale_date')
            ->get();

        $csvHeader = ['Date', 'Product', 'Revenue', 'Cost', 'Profit'];

        $filename = 'profit_loss_' . now()->format('Ymd_His') . '.csv';
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename=$filename");

        fputcsv($handle, $csvHeader);

        foreach ($sales as $row) {
            fputcsv($handle, [
                $row->sale_date,
                $row->product_name,
                number_format($row->revenue, 2),
                number_format($row->cost, 2),
                number_format($row->revenue - $row->cost, 2),
            ]);
        }

        fclose($handle);
        exit;
    }
    public function exportStock(Request $request)
    {
        $products = Product::all();

        $stockReport = $products->map(function ($product) {
            return [
                'Product Name' => $product->name,
                'Stock Quantity' => $product->stock,
                'Cost Price' => $product->cost_price,
                'Selling Price' => $product->selling_price ?? 0,
                'Stock Value' => $product->stock * $product->cost_price,
                'Status' => $product->stock < 10 ? 'Low Stock' : 'Sufficient',
            ];
        });

        $filename = 'stock_report_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($stockReport) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, array_keys($stockReport->first())); // Header

            foreach ($stockReport as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function stockMovement(Request $request)
    {
        $start = $request->input('start', now()->subMonths(5)->startOfMonth());
        $end = $request->input('end', now()->endOfMonth());

        // Incoming stock grouped by month & unit_type
        $incoming = DB::table('stock_adjustments')
            ->select(
                DB::raw("DATE_FORMAT(stock_adjustments.created_at, '%Y-%m') as month"),
                'unit_type',
                DB::raw("SUM(quantity) as total_incoming")
            )
            ->join('products', 'stock_adjustments.product_id', '=', 'products.id')
            ->where('quantity', '>', 0)
            ->whereBetween('stock_adjustments.created_at', [$start, $end])
            ->groupBy('month', 'unit_type')
            ->get()
            ->groupBy('month');

        // Outgoing stock grouped by month & unit_type
        $outgoing = DB::table('sale_items')
            ->select(
                DB::raw("DATE_FORMAT(sale_items.created_at, '%Y-%m') as month"),
                'unit_type',
                DB::raw("SUM(quantity) as total_outgoing")
            )
            ->whereBetween('sale_items.created_at', [$start, $end])
            ->groupBy('month', 'unit_type')
            ->get()
            ->groupBy('month');

        // Get all months combined from both datasets
        $months = collect($incoming->keys())
            ->merge($outgoing->keys())
            ->unique()
            ->sort();

        $movement = $months->map(function ($month) use ($incoming, $outgoing) {
            $monthIncoming = $incoming->get($month) ?? collect();
            $monthOutgoing = $outgoing->get($month) ?? collect();

            // Get all unit types for this month
            $units = $monthIncoming->pluck('unit_type')
                ->merge($monthOutgoing->pluck('unit_type'))
                ->unique();

            $unitData = $units->map(function ($unit) use ($monthIncoming, $monthOutgoing) {
                $incomingQty = optional($monthIncoming->firstWhere('unit_type', $unit))->total_incoming ?? 0;
                $outgoingQty = optional($monthOutgoing->firstWhere('unit_type', $unit))->total_outgoing ?? 0;
                return [
                    'unit_type' => $unit,
                    'incoming' => $incomingQty,
                    'outgoing' => $outgoingQty,
                    'net' => $incomingQty - $outgoingQty,
                ];
            });

            return [
                'month' => \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y'),
                'units' => $unitData,
            ];
        });

        return view('report.stock_movement', compact('movement', 'start', 'end'));
    }

    public function summaryPdf()
    {
//        $sales = Sale::all();
        $sales = Sale::with('product')->get();
        // Sales
        $totalSales = $sales->sum('subtotal');
        $totalTransactions = $sales->count();
        $productsSoldByUnit = Sale::select('sale_items.unit_type', DB::raw('SUM(sale_items.quantity) as qty'))
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->groupBy('unit_type')->get();

        // Profit & Loss
        $revenue = $sales->sum('subtotal');
//        $cost = $sales->sum(fn($s) => $s->quantity * ($s->cost_price ?? 0));
        $cost = $sales->sum(function($saleItem) {
            return $saleItem->quantity * ($saleItem->product->cost_price ?? 0);
        });
        $expenses = Expense::sum('amount');
        $profit = $revenue - $cost - $expenses;


        // Stock Report
        $stockItems = Product::all()->map(function ($p) {
            $cost = (float)$p->cost_price;
            $sell = (float)$p->price_per_unit;
            $stock = (float)$p->stock;
            $margin = $sell - $cost;
            return [
                'name' => $p->name,
                'stock' => $stock,
                'cost' => $cost,
                'sell' => $sell,
                'margin' => $margin, // profit per unit
                'value' => $stock * $cost,
                'profit' => $stock * $margin, // ✅ total profit for that product
            ];
        });

// Totals
        $totalStockValue = $stockItems->sum('value');
        $totalStockProfitValue = $stockItems->sum('profit'); // ✅ NEW


        // Expenses by category
        $expenseBreakdown = Expense::select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')->get();

        // Top Selling Products
//        $topProducts = Sale::select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
//            ->with('product')
//            ->groupBy('product_id')
//            ->orderByDesc('total_revenue')
//            ->take(10)
//            ->get();
        $topProducts = Sale::select(
            'product_id',
            DB::raw('SUM(quantity) as total_qty'),
            DB::raw('SUM(subtotal) as total_revenue'),
            DB::raw('SUM(subtotal - (quantity * products.cost_price)) as profit')
        )
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_revenue')
            ->take(10)
            ->get();

        //user performance
        $userPerformance =  Sale::select(
            'sale_items.user_id',
            DB::raw('SUM(sale_items.subtotal) as revenue'),
            DB::raw('SUM(sale_items.quantity * products.cost_price) as cost'),
            DB::raw('SUM(sale_items.subtotal - (sale_items.quantity * products.cost_price)) as profit')
        )
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->with('user')
            ->groupBy('sale_items.user_id')
            ->get();

        // TODO: Implement returns if you have a returns table

        return PDF::loadView('report.summary_pdf', compact(
            'totalSales', 'totalTransactions', 'productsSoldByUnit',
            'revenue', 'cost', 'expenses', 'profit',
            'stockItems', 'totalStockValue','totalStockProfitValue',
            'expenseBreakdown',
            'topProducts',
            'userPerformance'
        ))->download('general_report_' . now()->format('Ymd_His') . '.pdf');

    }





}
