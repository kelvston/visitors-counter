<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockAdjustmentsExport;
use App\Models\StockAdjustment;
use Carbon\Carbon;

class InventoryController extends Controller
{
    public function auditLog(Request $request)
    {
        $query = StockAdjustment::with(['product', 'user'])->latest();

        // Apply filters
        if ($request->filled('product_name')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->product_name . '%');
            });
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $adjustments = $query->paginate(10);

        return view('stock_adjustments.audit_log', compact('adjustments'));
    }
    public function exportAudit(Request $request)
    {
        $query = StockAdjustment::with('product');

        if ($request->filled('product_name')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->product_name . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', Carbon::parse($request->from_date)->startOfDay());
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->to_date)->endOfDay());
        }

        $logs = $query->get();

        if ($request->format() === 'excel') {
            return Excel::download(new StockAdjustmentsExport($logs), 'stock_audit_log.xlsx');
        }

        if ($request->format() === 'pdf') {
            $pdf = Pdf::loadView('inventory.audit_pdf', ['logs' => $logs]);
            return $pdf->download('stock_audit_log.pdf');
        }

        return back()->with('error', 'Invalid export format selected.');
    }
}
