<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
//    public function index()
//    {
//        $auditLogs = AuditLog::with('user')
//            ->orderBy('created_at', 'desc')
//            ->paginate(5);
//
//        // Decode changes JSON into arrays
//        foreach ($auditLogs as $log) {
//            if (is_string($log->changes)) {
//                $log->changes = json_decode($log->changes, true);
//            }
//        }
//
//        return view('audit_logs.index', compact('auditLogs'));
//    }
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $auditLogs = $query->limit(10)->get(); // Collection, NOT paginated

        $users = \App\Models\User::all();

        return view('audit_logs.index', compact('auditLogs', 'users'));
    }


    public function export(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $logs = $query->get();

        $csvFileName = 'audit_logs_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$csvFileName\"",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'User', 'Action', 'IP Address', 'Created At', 'Changes']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    optional($log->user)->name,
                    $log->action,
                    $log->ip_address,
                    $log->created_at,
                    json_encode($log->changes),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }




}
