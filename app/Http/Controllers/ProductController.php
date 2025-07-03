<?php

namespace App\Http\Controllers;

use App\Imports\ProductsImport;
use App\Imports\StockAdjustmentImport;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\StockAdjustment;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Excel as ExcelService;


class ProductController extends Controller
{

    public function autocomplete(Request $request)
    {
        $search = $request->get('term');
        $results = Product::where('name', 'LIKE', "%{$search}%")
            ->select('id', 'name', 'unit_type') // Select all necessary columns
            ->get();
        $formattedResults = $results->map(function ($product) {
            return [
                'id' => $product->id,
                'label' => $product->name . ' (' . $product->unit_type . ')', // What's displayed in the autocomplete list
                'value' => $product->name, // What goes into the input field after selection
                'unit_type' => $product->unit_type, // The actual unit type
            ];
        });


        return response()->json($formattedResults);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
//        dd($request->all());

        $query = Product::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('barcode', 'like', "%$search%");
            });
        }
        if ($request->filled('unit_type')) {
            $query->where('unit_type', $request->input('unit_type'));
        }
        if ($request->filled('stock_status')) {
            $status = $request->input('stock_status');
            if ($status === 'in_stock') {
                $query->where('stock', '>=', 10);
            } elseif ($status === 'low_stock') {
                $query->whereBetween('stock', [1, 9]);
            } elseif ($status === 'out_of_stock') {
                $query->where('stock', '=', 0);
            }
        }
        if ($request->filled('price_min') && is_numeric($request->price_min)) {
            $query->where('price_per_unit', '>=', $request->price_min);
        }

        if ($request->filled('price_max') && is_numeric($request->price_max)) {
            $query->where('price_per_unit', '<=', $request->price_max);
        }
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'desc');
        $query->orderBy($sort, $order);
        $products = $query->paginate(10)->appends($request->query());

        return view('products.index', compact('products'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
//    public function store(Request $request)
//    {
//        DB::beginTransaction();
//
//            // 1. Validate the incoming request data
//            $request->validate([
//                'items' => 'required|array|min:1', // Ensure 'items' is an array and not empty
//                'items.*.product_id' => 'required|exists:products,id', // Each item must have a valid product_id
//                'items.*.quantity' => 'required|numeric|min:0.01', // Each item must have a positive quantity
//            ]);
//        try{
//            foreach ($request->input('items') as $itemData) {
//
//                $productId = $itemData['product_id'];
//                $quantity = (float) $itemData['quantity'];
//
//                $product = Product::find($productId);
//                if (!$product) {
//                    // This shouldn't happen if 'exists' validation works, but as a safeguard
//                    throw ValidationException::withMessages([
//                        'product_id' => 'The selected product does not exist.',
//                    ]);
//                }
//                $unitPrice = $product->price_per_unit;
//                $subtotal = $quantity * $product->price_per_unit;
//
//                $saleItem = Sale::create([
//                    'product_id' => $productId,
//                    'quantity' => $quantity,
//                    'unit_type' => $product->unit_type,
//                    'unit_price' => $product->price_per_unit,
//                    'subtotal' => $subtotal,
//                    'user_id' => auth()->id(),
//                ]);
//                if ($request['print_receipt']) {
//                    $receipt = Receipt::create([
//                        'sale_item_id' => $saleItem->id,
//                        'receipt_number' => 'RCPT-' . strtoupper(uniqid()),
//                        'receipt_data' => json_encode([
//                            'item' => $product->name,
//                            'unit_type' => $product->unit_type,
//                            'quantity' => $quantity,
//                            'unit_price' => $unitPrice,
//                            'subtotal' => $subtotal,
//                        ]),
//                        'total_amount' => $subtotal,
//                        'user_id' => auth()->id(),
//                        'printed_at' => now(),
//                    ]);
//
//                    $pdf = Pdf::loadView('receipts.print', [
//                        'receipt' => $receipt,
//                        'product' => $product,
//                        'quantity' => $quantity,
//                        'unit_price' => $unitPrice,
//                        'subtotal' => $subtotal
//                    ]);
//                }
//
//                logAudit('created', 'App\Models\Product', $product->id, ['name' => $product->name,'quantity' => $old['quantity'] ?? null,
//                    'subtotal' => $old['subtotal'] ?? null,]);
//
//
//                $product->stock = (($product->stock) - ($saleItem->quantity));
//                $product->save();
//
//                $old = $product->getOriginal();
//
//                logAudit('created', 'App\Models\Product', $product->id, ['name' => $product->name,'quantity' => $old['quantity'] ?? null,
//                    'subtotal' => $old['subtotal'] ?? null,]);
//
//
//                DB::commit(); // Commit the transaction if all items are saved successfully
//                return response()->json([
//                    'success' => true,
//                    'message' => 'Sale recorded successfully!',
//                    'sale_item' => $saleItem
//                ], 200);
//            }





            // 4. Return a success response


//        } catch (ValidationException $e) {
//            return response()->json([
//                'success' => false,
//                'message' => 'Validation failed',
//                'errors' => $e->errors()
//            ], 422);
//        } catch (\Exception $e) {
//            return response()->json([
//                'success' => false,
//                'message' => 'An error occurred while recording the sale: ' . $e->getMessage()
//            ], 500);
//        }
//    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
            ]);

            $allSaleItems = [];
            $allItemsData = []; // To collect all items for receipt
            $userId = auth()->id();

            foreach ($request->input('items') as $itemData) {
                $productId = $itemData['product_id'];
                $quantity = (float) $itemData['quantity'];

                $product = Product::find($productId);
                $unitPrice = $product->price_per_unit;
                $subtotal = $quantity * $unitPrice;

                // Create Sale record
                $saleItem = Sale::create([
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_type' => $product->unit_type,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                    'user_id' => $userId,
                ]);

                $allSaleItems[] = $saleItem;

                // Prepare data for receipt
                $allItemsData[] = [
                    'item' => $product->name,
                    'unit_type' => $product->unit_type,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ];

                // Update stock and log
                $oldStock = $product->stock;
                $product->stock = $oldStock - $quantity;
                $product->save();

                logAudit('created', 'App\Models\Product', $product->id, [
                    'name' => $product->name,
                    'old_stock' => $oldStock,
                    'new_stock' => $product->stock,
                    'quantity_sold' => $quantity,
                    'subtotal' => $subtotal,
                ]);
            }

            // Calculate total amount for all items
            $totalAmount = array_sum(array_column($allItemsData, 'subtotal'));

            $receipt = null;
            $pdfBase64 = null;

            if ($request['print_receipt'] == 1) {

                // Create single Receipt for all sale items
                $receipt = Receipt::create([
                    // No sale_item_id here since multiple items - or you can create a separate 'sale_id' for this transaction if needed
                    'receipt_number' => 'RCPT-' . strtoupper(uniqid()),
                    'receipt_data' => json_encode($allItemsData), // array of all items
                    'total_amount' => $totalAmount,
                    'user_id' => $userId,
                    'printed_at' => now(),
                ]);

                $shopName = Setting::where('key', 'shop_name')->value('value') ?? 'Default Shop Name';
                $shopAddress = Setting::where('key', 'shop_address')->value('value') ?? '';
                $contactPhone = Setting::where('key', 'contact_phone')->value('value') ?? '';
                $contactEmail = Setting::where('key', 'contact_email')->value('value') ?? '';
                $footer = Setting::where('key', 'receipt_footer_text')->value('value') ?? '';

                $pdf = Pdf::loadView('receipts.print', [
                    'receipt' => $receipt,
                    'shopName' => $shopName,
                    'shopAddress' => $shopAddress,
                    'contactPhone' => $contactPhone,
                    'contactEmail' => $contactEmail,
                    'footer' => $contactEmail,
                ]);

                $pdfBase64 = base64_encode($pdf->output());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale recorded successfully!',
                'sale_items' => $allSaleItems,
                'receipt' => $receipt,
                'receipt_pdf' => $pdfBase64,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Sale failed: ' . $e->getMessage(),
            ], 500);
        }
    }



    public function salesSummary(Request $request)
    {
        try {


            $today = Carbon::today();
            $yesterday = Carbon::yesterday();
            $startOfWeek = Carbon::now()->startOfWeek(); // Monday
            $endOfWeek = Carbon::now()->endOfWeek();     // Sunday
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
            $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
            $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();
            $startOfThreeMonthsAgo = Carbon::now()->subMonths(2)->startOfMonth(); // Current month + 2 previous = 3 months

            $summary = [
                'today' => $this->getPeriodTotals($today, $today),
                'yesterday' => $this->getPeriodTotals($yesterday, $yesterday),
                'this_week' => $this->getPeriodTotals($startOfWeek, $endOfWeek),
                'this_month' => $this->getPeriodTotals($startOfMonth, $endOfMonth),
                'last_month' => $this->getPeriodTotals($startOfLastMonth, $endOfLastMonth),
                'last_three_months' => $this->getPeriodTotals($startOfThreeMonthsAgo, $endOfMonth), // From 3 months ago to end of current month
                'total_sales' => $this->getPeriodTotals(null, null), // All time
            ];


            return response()->json($summary);

        } catch (\Exception $e) {
            \Log::error('Error in salesSummary: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load sales summary data due to a server error.',
                'error_details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method to get total quantity by unit type for a given period.
     *
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return array
     */
    private function getPeriodTotals($startDate, $endDate = null)
    {
        $query = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(
                'products.unit_type',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue')
            );

        if ($startDate) {
            $query->where('sale_items.created_at', '>=', $startDate->startOfDay());
        }
        if ($endDate) {
            $query->where('sale_items.created_at', '<=', $endDate->endOfDay());
        }

        $results = $query->groupBy('products.unit_type')
            ->get()
            ->keyBy('unit_type');

        $items = 0;
        $litre = 0;
        $kg = 0;
        $itemsRevenue = 0;   // <--- NEW
        $litreRevenue = 0;   // <--- NEW
        $kgRevenue = 0;      // <--- NEW

        if ($results->has('item')) {
            $items = (float) $results->get('item')->total_quantity;
            $itemsRevenue = (float) $results->get('item')->total_revenue; // <--- NEW
        }
        if ($results->has('litre')) {
            $litre = (float) $results->get('litre')->total_quantity;
            $litreRevenue = (float) $results->get('litre')->total_revenue; // <--- NEW
        }
        if ($results->has('kg')) {
            $kg = (float) $results->get('kg')->total_quantity;
            $kgRevenue = (float) $results->get('kg')->total_revenue;      // <--- NEW
        }

        // Calculate total quantity and total revenue across all unit types for the period
        $totalSumQuery = DB::table('sale_items'); // Use a new query for overall totals

        if ($startDate) {
            $totalSumQuery->where('created_at', '>=', $startDate->startOfDay());
        }
        if ($endDate) {
            $totalSumQuery->where('created_at', '<=', $endDate->endOfDay());
        }
        $totalQuantity = (float) $totalSumQuery->sum('quantity');

        $totalRevenue = (float) $totalSumQuery->sum('subtotal'); // <--- NEW
//        dd($totalRevenue->get());

        Log::info('Period Totals for ' . ($startDate ? $startDate->format('Y-m-d') : 'All Time') . ' to ' . ($endDate ? $endDate->format('Y-m-d') : 'All Time'), [
            'items_qty' => $items,
            'items_revenue' => $itemsRevenue, // <--- Log new values
            'litre_qty' => $litre,
            'litre_revenue' => $litreRevenue, // <--- Log new values
            'kg_qty' => $kg,
            'kg_revenue' => $kgRevenue,       // <--- Log new values
            'total_quantity' => $totalQuantity,
            'total_revenue' => number_format($totalRevenue, 0, '.', '') // <-- No decimals


        ]);

        return [
            'items' => number_format($items, 2, '.', ''),
            'litre' => number_format($litre, 2, '.', ''),
            'kg' => number_format($kg, 2, '.', ''),
            'total_quantity' => number_format($totalQuantity, 2, '.', ''),
            'total_revenue' =>  number_format($totalRevenue, 0, '.', ',')
// <--- ADD THIS LINE TO RETURN REVENUE
        ];
    }


    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:255|unique:products,barcode',
            'unit_type' => 'required|in:item,kg,litre',
            'price_per_unit' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
        ]);
        $existing = Product::whereRaw('LOWER(name) = ?', [strtolower($validated['name'])])->first();

        if ($existing) {
            $existing->stock += $validated['stock'];
             $existing->price_per_unit = $validated['price_per_unit'];
             $existing->cost_price = $validated['cost_price'];
            $existing->save();
            return redirect()->route('products.index')->with('success', 'Existing product stock updated successfully!');
        }

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product added successfully!');

    }





    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|unique:products,barcode,' . $product->id,
            'unit_type' => 'required|in:item,kg,litre',
            'price_per_unit' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
        ]);

        $product->update($request->all());

        return redirect()->route('products.index')->with('success', 'Product updated!');
    }

    public function recentSales(Request $request)
    {
        try {
            $recentSales = DB::table('sale_items')
                ->join('products', 'sale_items.product_id', '=', 'products.id')
                ->select(
                    'products.name',
                    'products.unit_type',
                    'sale_items.quantity',
                    'sale_items.subtotal',
                    'sale_items.created_at'
                )
                ->orderByDesc('sale_items.created_at')
                ->take(3)
                ->get()
                ->map(function ($item) {
                    return [
                        'name' => $item->name,
                        'unit_type' => $item->unit_type,
                        'quantity' => number_format((float) $item->quantity, 2, '.', ''),
                        'subtotal' => number_format((float) $item->subtotal, 0, '.', ''),
                        'created_at' => Carbon::parse($item->created_at)->format('Y-m-d H:i:s')
                    ];
                });


            return response()->json([
                'success' => true,
                'data' => $recentSales
            ]);
        } catch (\Exception $e) {
            Log::error('Error in recentSales: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load recent sales data due to a server error.',
                'error_details' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function export(Request $request)
    {
        $query = Product::query();

        // Apply filters just like in index()
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('barcode', 'like', "%$search%");
            });
        }

        if (!empty($request->unit_type)) {
            $query->where('unit_type', $request->unit_type);
        }

        if (!empty($request->stock_status)) {
            if ($request->stock_status === 'in_stock') {
                $query->where('stock', '>=', 10);
            } elseif ($request->stock_status === 'low_stock') {
                $query->whereBetween('stock', [1, 9]);
            } elseif ($request->stock_status === 'out_of_stock') {
                $query->where('stock', 0);
            }
        }

        if (is_numeric($request->price_min)) {
            $query->where('price_per_unit', '>=', $request->price_min);
        }

        if (is_numeric($request->price_max)) {
            $query->where('price_per_unit', '<=', $request->price_max);
        }

        $products = $query->orderBy('id', 'desc')->get();

        // Create CSV
        $filename = 'products_export_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = ['ID', 'Name', 'Barcode', 'Unit Type', 'Price per Unit','Cost Price', 'Stock'];

        $callback = function () use ($products, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->barcode ?? '-',
                    $product->unit_type,
                    $product->price_per_unit,
                    $product->cost_price,
                    $product->stock,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request, ExcelService $excel)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            $excel->import(new ProductsImport, $request->file('file'));

            return redirect()->back()->with('success', 'Products imported successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to import: ' . $e->getMessage());
        }
    }

    public function adjustStock(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:add,remove',
            'quantity' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:255',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $originalStock = $product->stock;

        if ($validated['type'] === 'add') {
            $product->stock += $validated['quantity'];
        } else {
            if ($product->stock < $validated['quantity']) {
                return back()->withErrors(['quantity' => 'Not enough stock to remove']);
            }
            $product->stock -= $validated['quantity'];
        }

        $product->save();

        StockAdjustment::create([
            'product_id' => $product->id,
            'type' => $validated['type'],
            'quantity' => $validated['quantity'],
            'reason' => $validated['reason'],
            'user_id' => auth()->id(),
            'action' => $validated['type'],
        ]);

        return redirect()->route('products.index')->with('success', 'Stock adjusted successfully!');
    }
    public function showAdjustmentForm()
    {
        $products = Product::orderBy('name')->get();
        return view('stock_adjustments.create', compact('products'));
    }

    public function importExcel(Request $request, ExcelService $excel)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);
        $excel->import(new StockAdjustmentImport(auth()->id()), $request->file('file'));

        return redirect()->route('stock.adjust.form')->with('success', 'Stock adjustments imported successfully.');
    }


}
