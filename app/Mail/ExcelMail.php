<?php

namespace App\Mail;

use App\Exports\CounterReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExcelMail extends Mailable
{
    use Queueable, SerializesModels;

    public $summary; // Define the property

    /**
     * Create a new message instance.
     */
    public function __construct($summary)
    {
        $this->summary = $summary;
    }

    /**
     * Get the message envelope.
     */
    public function build()
    {
        // Generate the Excel file in memory with both sheets
        $excelContent = Excel::raw(new CounterReportExport(), \Maatwebsite\Excel\Excel::XLSX);

        // Return the email with the attachment
        return $this->from(new \Illuminate\Mail\Mailables\Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')))
                    ->subject('VISITORS COUNTER REPORT')
                    ->view('emails.counter')
                    ->with(['summary' => $this->summary])
                    ->attachData($excelContent, 'combined_report.xlsx', [
                        'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ]);
    }
}
