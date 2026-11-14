<?php

namespace Tests\Feature;

use App\Mail\ProposalDeadlineReminder;
use App\Models\Proposal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ProposalDeadlineReminderTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_academic_users_receive_one_reminder_at_seven_and_three_days_before_deadline(): void
    {
        Carbon::setTestNow('2026-11-14 08:00:00');
        Mail::fake();

        $academicOne = User::factory()->create(['staff_category' => 'academic']);
        $academicTwo = User::factory()->create(['staff_category' => 'academic']);
        $administrative = User::factory()->create(['staff_category' => 'administrative']);

        $sevenDayProposal = $this->createProposal(Carbon::today()->addDays(7));
        $threeDayProposal = $this->createProposal(Carbon::today()->addDays(3));

        $this->artisan('proposals:send-deadline-reminders')->assertSuccessful();
        $this->artisan('proposals:send-deadline-reminders')->assertSuccessful();

        Mail::assertSentCount(4);
        Mail::assertSent(ProposalDeadlineReminder::class, fn (ProposalDeadlineReminder $mail) =>
            $mail->proposal->is($sevenDayProposal)
            && $mail->daysRemaining === 7
            && $mail->hasTo($academicOne->email)
        );
        Mail::assertSent(ProposalDeadlineReminder::class, fn (ProposalDeadlineReminder $mail) =>
            $mail->proposal->is($threeDayProposal)
            && $mail->daysRemaining === 3
            && $mail->hasTo($academicTwo->email)
        );
        Mail::assertNotSent(ProposalDeadlineReminder::class, fn (ProposalDeadlineReminder $mail) =>
            $mail->hasTo($administrative->email)
        );
        $this->assertDatabaseCount('proposal_reminders', 4);
    }

    private function createProposal(Carbon $deadline): Proposal
    {
        return Proposal::create([
            'title' => 'Submission deadline test',
            'timeline' => $deadline->toDateString(),
            'timeline_from' => Carbon::today()->toDateString(),
            'timeline_to' => $deadline->toDateString(),
            'destination' => 'Library office',
            'details' => 'Submission details',
            'attachment' => 'proposals/test.pdf',
        ]);
    }
}
