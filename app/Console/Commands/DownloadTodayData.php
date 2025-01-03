<?php

namespace App\Console\Commands;

use App\Models\Counter;
use Illuminate\Console\Command;

class DownloadTodayData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:download-today';
    protected $description = 'Download today\'s visitor data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \Log::info('DownloadTodayData command is running.');
        // Get today's date
        $today = now()->format('Y-m-d');

        // Fetch today's data
        $data = Counter::whereDate('created_at', $today)->get();

        // Convert data to CSV format (or any format you prefer)
        $csvFileName = "visitor_data_{$today}.csv";
        $csvFilePath = storage_path("app/public/{$csvFileName}");

        $file = fopen($csvFilePath, 'w');

        // Add headers to CSV
        fputcsv($file, ['ID', 'Gender', 'Timestamp']); // Adjust according to your columns

        // Add data rows to CSV
        foreach ($data as $visitor) {
            fputcsv($file, [$visitor->id, $visitor->gender, $visitor->created_at]);
        }

        fclose($file);

        // Optionally, store the file or send it via email
        // Storage::putFileAs('visitors', $csvFilePath, $csvFileName);

        $this->info('Today\'s visitor data has been downloaded.');
    }
}
