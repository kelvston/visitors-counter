<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CounterReportExport implements WithMultipleSheets
{
    /**
     * Return both sheets for the export.
     */
    public function sheets(): array
    {
        // Counter Report sheet
        $counterSheet = new CounterSheetExport();

        // Newsletter Report sheet
        $newsletterSheet = new NewsletterSheetExport();

        return [
            'Counter Report' => $counterSheet,
            'Newsletter Report' => $newsletterSheet,
        ];
    }
}

class CounterSheetExport implements FromCollection, WithHeadings, WithTitle
{
    /**
     * Fetch data for the Counter Report sheet.
     */
    public function collection()
    {
        $today = Carbon::today();
        return DB::table('counters')
            ->select(
                DB::raw('DATE(counters.created_at) as date'),
                'users.id as user_id',
                DB::raw('SUM(CASE WHEN counters.gender = 1 THEN 1 ELSE 0 END) as male_count'),
                DB::raw('SUM(CASE WHEN counters.gender = 2 THEN 1 ELSE 0 END) as female_count'),
                DB::raw('SUM(CASE WHEN counters.gender = 3 THEN 1 ELSE 0 END) as other_count'),
                DB::raw('COUNT(*) as total_count'),
                'users.*',
            )
            ->join('users', 'counters.user_id', '=', 'users.id')
            ->whereNull('counters.deleted_at')
            ->whereDate('counters.created_at', $today)
            ->groupBy(DB::raw('DATE(counters.created_at)'), 'users.id')
            ->orderByDesc(DB::raw('DATE(counters.created_at)'))
            ->get();
    }

    /**
     * Define headings for the Counter Report sheet.
     */
    public function headings(): array
    {
        return ['Date', 'User ID', 'Male Count', 'Female Count', 'Other Count', 'Total Count', 'User Name', 'Email'];
    }

    /**
     * Set the title for the Counter Report sheet.
     */
    public function title(): string
    {
        return 'Visitors Counter Report';
    }
}

class NewsletterSheetExport implements FromCollection, WithHeadings, WithTitle
{
    /**
     * Fetch data for the Newsletter Report sheet.
     */
    public function collection()
    {
        $today = Carbon::today();
        return DB::table('news_letters')
            ->select(
                DB::raw('DATE(news_letters.created_at) as date'),
                'users.name as user_name',
                DB::raw("SUM(CASE WHEN news_letters.name = 'Guardian' THEN news_letters.counts ELSE 0 END) as guardian_count"),
                DB::raw("SUM(CASE WHEN news_letters.name = 'Nipashe' THEN news_letters.counts ELSE 0 END) as nipashe_count"),
                DB::raw("SUM(CASE WHEN news_letters.name = 'Habari Leo' THEN news_letters.counts ELSE 0 END) as habari_leo_count"),
                DB::raw("SUM(CASE WHEN news_letters.name = 'Uhuru' THEN news_letters.counts ELSE 0 END) as uhuru_count"),
                DB::raw("SUM(CASE WHEN news_letters.name = 'East African' THEN news_letters.counts ELSE 0 END) as east_african_count"),
                DB::raw("SUM(CASE WHEN news_letters.name = 'Mwananchi' THEN news_letters.counts ELSE 0 END) as mwananchi_count"),
                DB::raw("SUM(CASE WHEN news_letters.name = 'Citizen' THEN news_letters.counts ELSE 0 END) as citizen_count"),
                DB::raw("SUM(CASE WHEN news_letters.name = 'Daily News' THEN news_letters.counts ELSE 0 END) as daily_news_count"),
                DB::raw("SUM(CASE WHEN news_letters.name IN ('Guardian', 'Nipashe', 'Habari Leo', 'Uhuru', 'East African', 'Mwananchi', 'Citizen', 'Daily News') THEN news_letters.counts ELSE 0 END) as total_count")
            )
            ->join('users', 'news_letters.user_id', '=', 'users.id')
            ->whereDate('news_letters.created_at', $today) // Filter by today's date
            ->groupBy(DB::raw('DATE(news_letters.created_at)'), 'users.name')
            ->orderByDesc(DB::raw('DATE(news_letters.created_at)'))->get();
    }

    /**
     * Define headings for the Newsletter Report sheet.
     */
    public function headings(): array
    {
        return ['Date', 'User Name', 'Guardian', 'Nipashe', 'Habari Leo', 'Uhuru', 'East African', 'Mwananchi', 'Citizen', 'Daily News', 'Total Count'];
    }

    /**
     * Set the title for the Newsletter Report sheet.
     */
    public function title(): string
    {
        return 'Newsletter Report';
    }
}
