<?php

namespace App\Console\Commands;

use App\Mail\ProposalDeadlineReminder;
use App\Models\Proposal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendProposalDeadlineReminders extends Command
{
    protected $signature = 'proposals:send-deadline-reminders';

    protected $description = 'Email academic staff 7 and 3 days before proposal submission deadlines';

    public function handle(): int
    {
        $sent = 0;
        $today = Carbon::today();
        $academicUsers = User::query()->where('staff_category', 'academic')->get(['id', 'email']);

        foreach ([7, 3] as $daysRemaining) {
            $deadline = $today->copy()->addDays($daysRemaining);
            $proposals = Proposal::query()
                ->whereDate('timeline_to', $deadline)
                ->get();

            foreach ($proposals as $proposal) {
                foreach ($academicUsers as $user) {
                    $alreadySent = DB::table('proposal_reminders')
                        ->where('proposal_id', $proposal->id)
                        ->where('user_id', $user->id)
                        ->where('days_before', $daysRemaining)
                        ->exists();

                    if ($alreadySent) {
                        continue;
                    }

                    Mail::to($user->email)->send(new ProposalDeadlineReminder($proposal, $daysRemaining));

                    DB::table('proposal_reminders')->insert([
                        'proposal_id' => $proposal->id,
                        'user_id' => $user->id,
                        'days_before' => $daysRemaining,
                        'sent_at' => now(),
                    ]);
                    $sent++;
                }
            }
        }

        $this->info("Sent {$sent} proposal deadline reminder(s).");

        return self::SUCCESS;
    }
}
