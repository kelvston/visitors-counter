<?php

namespace App\Console\Commands;

use App\Models\MailConversation;
use App\Models\MailMessage;
use App\Models\Proposal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SendProposalDeadlineReminders extends Command
{
    protected $signature = 'proposals:send-deadline-reminders';

    protected $description = 'Send internal mail to academic staff 7 and 3 days before proposal deadlines';

    public function handle(): int
    {
        $createdCount = 0;
        $today = Carbon::today();
        $academicUsers = User::query()->where('staff_category', 'academic')->get(['id']);

        foreach ([7, 3] as $daysRemaining) {
            $deadline = $today->copy()->addDays($daysRemaining);
            $proposals = Proposal::query()
                ->whereDate('timeline_to', $deadline)
                ->get();

            foreach ($proposals as $proposal) {
                foreach ($academicUsers as $user) {
                    $created = DB::transaction(function () use ($proposal, $user, $daysRemaining): bool {
                        $alreadySent = DB::table('proposal_reminders')
                            ->where('proposal_id', $proposal->id)
                            ->where('user_id', $user->id)
                            ->where('days_before', $daysRemaining)
                            ->exists();

                        if ($alreadySent) {
                            return false;
                        }

                        $conversation = MailConversation::create([
                            'subject' => 'Proposal deadline reminder ('.$daysRemaining.' days): '.Str::limit($proposal->title, 200),
                            'sender_id' => null,
                            'recipient_id' => $user->id,
                        ]);
                        $message = MailMessage::create([
                            'conversation_id' => $conversation->id,
                            'sender_id' => null,
                            'recipient_id' => $user->id,
                            'body' => $this->reminderBody($proposal, $daysRemaining),
                        ]);
                        $message->recipientCopies()->create([
                            'user_id' => $user->id,
                            'type' => 'to',
                        ]);

                        DB::table('proposal_reminders')->insert([
                            'proposal_id' => $proposal->id,
                            'user_id' => $user->id,
                            'days_before' => $daysRemaining,
                            'sent_at' => now(),
                        ]);

                        return true;
                    });

                    $createdCount += (int) $created;
                }
            }
        }

        $this->info("Created {$createdCount} internal proposal deadline reminder(s).");

        return self::SUCCESS;
    }

    private function reminderBody(Proposal $proposal, int $daysRemaining): string
    {
        return '<p>This is an automatic reminder that the submission deadline is in '.$daysRemaining.' days.</p>'
            .'<p><strong>Proposal:</strong> '.e($proposal->title).'</p>'
            .'<p><strong>Deadline:</strong> '.$proposal->timeline_to->format('d M Y').'</p>'
            .'<p><strong>Where to submit:</strong> '.e($proposal->destination ?? 'Not specified').'</p>'
            .'<p>Please make sure your submission is sent before the deadline.</p>';
    }
}
