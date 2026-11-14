<?php

namespace App\Mail;

use App\Models\Proposal;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProposalDeadlineReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Proposal $proposal,
        public int $daysRemaining,
    ) {
    }

    public function build(): self
    {
        return $this->subject("Proposal submission deadline in {$this->daysRemaining} days: {$this->proposal->title}")
            ->view('emails.proposal-deadline-reminder');
    }
}
