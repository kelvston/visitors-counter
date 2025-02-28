<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


Artisan::command('email:daily-report', function () {
    // Fetch data for summary (Modify as needed)
    $summary = DB::table('counters')
        ->select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(CASE WHEN gender = 1 THEN 1 ELSE 0 END) as male_count'),
            DB::raw('SUM(CASE WHEN gender = 2 THEN 1 ELSE 0 END) as female_count'),
            DB::raw('SUM(CASE WHEN gender = 3 THEN 1 ELSE 0 END) as other_count'),
            DB::raw('COUNT(*) as total_count')
        )
        ->whereNull('deleted_at')
        ->groupBy(DB::raw('DATE(created_at)'))
        ->orderByDesc(DB::raw('DATE(created_at)'))
        ->get();

    // Admin Email
    $adminEmail = 'kelvinstony9@gmail.com';

    // Send email with the summary
    Mail::to($adminEmail)->send(new \App\Mail\ExcelMail($summary));

    $this->info('Daily visitor report email sent successfully at 10 PM!');
})->describe('Send the daily visitor counter report to the admin at 10 PM.');
