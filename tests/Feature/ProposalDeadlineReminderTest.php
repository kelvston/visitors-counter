<?php

namespace Tests\Feature;

use App\Models\MailMessage;
use App\Models\Proposal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $academicOne = User::factory()->create(['staff_category' => 'academic']);
        $academicTwo = User::factory()->create(['staff_category' => 'academic']);
        $administrative = User::factory()->create(['staff_category' => 'administrative']);

        $sevenDayProposal = $this->createProposal(Carbon::today()->addDays(7));
        $threeDayProposal = $this->createProposal(Carbon::today()->addDays(3));

        $this->artisan('proposals:send-deadline-reminders')->assertSuccessful();
        $this->artisan('proposals:send-deadline-reminders')->assertSuccessful();

        $this->assertDatabaseCount('mail_conversations', 4);
        $this->assertDatabaseCount('mail_messages', 4);
        $this->assertDatabaseCount('mail_message_recipients', 4);
        $this->assertDatabaseCount('proposal_reminders', 4);

        $sevenDayMessage = MailMessage::query()
            ->where('body', 'like', '%in 7 days%')
            ->firstOrFail();
        $this->assertNull($sevenDayMessage->sender_id);
        $this->assertNull($sevenDayMessage->conversation->sender_id);
        $this->assertStringContainsString($sevenDayProposal->title, $sevenDayMessage->body);
        $this->assertStringContainsString($sevenDayProposal->timeline_to->format('d M Y'), $sevenDayMessage->body);
        $this->assertDatabaseHas('mail_message_recipients', [
            'mail_message_id' => $sevenDayMessage->id,
            'user_id' => $academicOne->id,
            'type' => 'to',
            'read_at' => null,
        ]);
        $this->assertDatabaseMissing('mail_message_recipients', [
            'user_id' => $administrative->id,
        ]);

        $this->actingAs($academicOne)
            ->get(route('staff-mail.inbox'))
            ->assertOk()
            ->assertSee('Library System')
            ->assertSee($sevenDayProposal->title);

        $this->actingAs($academicOne)
            ->get(route('staff-mail.show', $sevenDayMessage->conversation))
            ->assertOk()
            ->assertSee('Library System')
            ->assertSee('in 7 days')
            ->assertSee($sevenDayProposal->destination);
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
