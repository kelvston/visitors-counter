<?php

namespace Tests\Feature;

use App\Models\MailConversation;
use App\Models\MailMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StaffMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_compose_and_reply_to_any_active_staff_member(): void
    {
        $sender = User::factory()->create(['staff_category' => 'academic']);
        $sameCategory = User::factory()->create(['staff_category' => 'academic']);
        $otherCategory = User::factory()->create(['staff_category' => 'administrative']);
        $deleted = User::factory()->create(['staff_category' => 'administrative']);
        $deleted->delete();

        $this->actingAs($sender)
            ->get(route('staff-mail.create'))
            ->assertOk()
            ->assertSee($sameCategory->email)
            ->assertSee($otherCategory->email)
            ->assertDontSee($sender->email)
            ->assertDontSee($deleted->email);

        $this->actingAs($sender)
            ->post(route('staff-mail.store'), [
                'to' => [$otherCategory->id],
                'subject' => 'Curriculum update',
                'body' => '<p>Please review the <strong>attached update</strong>.</p>',
            ])
            ->assertRedirect(route('staff-mail.sent'))
            ->assertSessionHas('status', 'Message sent.');

        $conversation = MailConversation::firstOrFail();
        $message = MailMessage::firstOrFail();
        $this->assertSame($sender->id, $conversation->sender_id);
        $this->assertSame($otherCategory->id, $conversation->recipient_id);
        $this->assertSame('<p>Please review the <strong>attached update</strong>.</p>', $message->body);

        $this->actingAs($otherCategory)
            ->get(route('staff-mail.inbox'))
            ->assertOk()
            ->assertSee('Curriculum update')
            ->assertSee('1 unread')
            ->assertSee('aria-label="Mail, 1 unread"', false);

        $this->actingAs($otherCategory)
            ->post(route('staff-mail.reply', $conversation), [
                'body' => 'I will review it.',
            ])
            ->assertRedirect(route('staff-mail.show', $conversation));

        $this->assertDatabaseHas('mail_messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $otherCategory->id,
            'recipient_id' => $sender->id,
            'body' => 'I will review it.',
        ]);

        $this->actingAs($sender)
            ->post(route('staff-mail.store'), [
                'to' => [$sender->id],
                'subject' => 'Not allowed',
                'body' => 'A sender cannot address their own account.',
            ])
            ->assertSessionHasErrors('to.0');

        $this->actingAs($sender)
            ->post(route('staff-mail.store'), [
                'to' => [$deleted->id],
                'subject' => 'Not allowed',
                'body' => 'Deleted accounts cannot receive mail.',
            ])
            ->assertSessionHasErrors('to.0');

        $this->assertDatabaseCount('mail_conversations', 1);
    }

    public function test_opening_inbox_thread_marks_received_messages_read_and_allows_reply(): void
    {
        $sender = User::factory()->create(['staff_category' => 'administrative']);
        $recipient = User::factory()->create(['staff_category' => 'administrative']);
        $conversation = MailConversation::create([
            'subject' => 'Office schedule',
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
        ]);
        $firstMessage = $conversation->messages()->create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'body' => 'Can we discuss the schedule?',
        ]);
        $firstMessage->recipientCopies()->create([
            'user_id' => $recipient->id,
            'type' => 'to',
        ]);

        $this->actingAs($recipient)
            ->get(route('staff-mail.show', $conversation))
            ->assertOk()
            ->assertSee('Can we discuss the schedule?')
            ->assertSee('Reply');

        $this->assertNotNull($firstMessage->recipientCopies()->firstOrFail()->fresh()->read_at);

        $this->actingAs($recipient)
            ->post(route('staff-mail.reply', $conversation), [
                'body' => 'Yes, tomorrow works.',
            ])
            ->assertRedirect(route('staff-mail.show', $conversation));

        $this->assertDatabaseHas('mail_messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $recipient->id,
            'recipient_id' => $sender->id,
            'body' => 'Yes, tomorrow works.',
        ]);

        $this->actingAs($sender)
            ->get(route('staff-mail.inbox'))
            ->assertOk()
            ->assertSee('Office schedule');
    }

    public function test_mail_supports_multiple_to_cc_and_private_bcc_with_reply_all(): void
    {
        $sender = User::factory()->create(['staff_category' => 'academic']);
        $toOne = User::factory()->create(['staff_category' => 'academic']);
        $toTwo = User::factory()->create(['staff_category' => 'academic']);
        $cc = User::factory()->create(['staff_category' => 'academic']);
        $bcc = User::factory()->create(['staff_category' => 'academic']);

        $this->actingAs($sender)
            ->post(route('staff-mail.store'), [
                'to' => [$toOne->id, $toTwo->id],
                'cc' => [$cc->id],
                'bcc' => [$bcc->id],
                'subject' => 'Recipient privacy',
                'body' => '<p>Group update</p>',
            ])
            ->assertRedirect(route('staff-mail.sent'));

        $conversation = MailConversation::firstOrFail();
        $message = MailMessage::firstOrFail();
        $this->assertEqualsCanonicalizing([
            ['user_id' => $toOne->id, 'type' => 'to'],
            ['user_id' => $toTwo->id, 'type' => 'to'],
            ['user_id' => $cc->id, 'type' => 'cc'],
            ['user_id' => $bcc->id, 'type' => 'bcc'],
        ], $message->recipientCopies()->get(['user_id', 'type'])->map(fn ($copy) => [
            'user_id' => (int) $copy->user_id,
            'type' => $copy->type,
        ])->all());

        $this->actingAs($toOne)
            ->get(route('staff-mail.show', $conversation))
            ->assertOk()
            ->assertSee('To: '.$toOne->name)
            ->assertSee($toTwo->name)
            ->assertSee('CC: '.$cc->name)
            ->assertDontSee($bcc->name);

        $this->actingAs($sender)
            ->get(route('staff-mail.show', $conversation))
            ->assertOk()
            ->assertSee('BCC: '.$bcc->name);

        $this->actingAs($bcc)
            ->get(route('staff-mail.show', $conversation))
            ->assertOk()
            ->assertSee('BCC: you')
            ->assertDontSee('BCC: '.$bcc->name);

        $this->actingAs($toOne)
            ->post(route('staff-mail.reply', $conversation), [
                'body' => '<p>Reply all</p>',
            ])
            ->assertRedirect(route('staff-mail.show', $conversation));

        $reply = MailMessage::latest('id')->firstOrFail();
        $replyRecipients = $reply->recipientCopies()->orderBy('user_id')->get(['user_id', 'type']);
        $this->assertEqualsCanonicalizing([
            ['user_id' => $sender->id, 'type' => 'to'],
            ['user_id' => $toTwo->id, 'type' => 'to'],
            ['user_id' => $cc->id, 'type' => 'cc'],
        ], $replyRecipients->map(fn ($copy) => [
            'user_id' => (int) $copy->user_id,
            'type' => $copy->type,
        ])->all());

        $this->actingAs($toTwo)
            ->get(route('staff-mail.inbox'))
            ->assertOk()
            ->assertSee('Recipient privacy');

        $this->actingAs($bcc)
            ->get(route('staff-mail.inbox'))
            ->assertOk()
            ->assertSee('Recipient privacy');
    }

    public function test_rich_text_is_sanitized_before_it_is_rendered(): void
    {
        $sender = User::factory()->create(['staff_category' => 'administrative']);
        $recipient = User::factory()->create(['staff_category' => 'administrative']);

        $this->actingAs($sender)
            ->post(route('staff-mail.store'), [
                'to' => [$recipient->id],
                'subject' => 'Formatted mail',
                'body' => '<p onclick="alert(1)">Safe <strong>formatting</strong></p><script>alert("bad")</script><a href="javascript:alert(1)">bad link</a>',
            ])
            ->assertRedirect(route('staff-mail.sent'));

        $message = MailMessage::firstOrFail();
        $this->assertStringNotContainsString('onclick', $message->body);
        $this->assertStringNotContainsString('<script', $message->body);
        $this->assertStringNotContainsString('javascript:', $message->body);

        $this->actingAs($recipient)
            ->get(route('staff-mail.show', $message->conversation))
            ->assertOk()
            ->assertSee('<strong>formatting</strong>', false)
            ->assertDontSee('onclick', false)
            ->assertDontSee('alert("bad")', false);
    }

    public function test_mail_attachment_can_only_be_downloaded_by_conversation_participants(): void
    {
        Storage::fake('local');
        $sender = User::factory()->create(['staff_category' => 'academic']);
        $recipient = User::factory()->create(['staff_category' => 'academic']);
        $outsider = User::factory()->create(['staff_category' => 'academic']);
        $attachment = UploadedFile::fake()->createWithContent('agenda.pdf', '%PDF-1.4 test document');

        $this->actingAs($sender)
            ->post(route('staff-mail.store'), [
                'to' => [$recipient->id],
                'subject' => 'Meeting agenda',
                'body' => 'Please see the attached agenda.',
                'attachment' => $attachment,
            ])
            ->assertRedirect(route('staff-mail.sent'));

        $message = MailMessage::firstOrFail();
        Storage::disk('local')->assertExists($message->attachment_path);

        $this->actingAs($recipient)
            ->get(route('staff-mail.attachments.download', $message))
            ->assertOk();

        $this->actingAs($recipient)
            ->get(route('staff-mail.inbox'))
            ->assertOk()
            ->assertSee('agenda.pdf')
            ->assertSee(route('staff-mail.attachments.preview', $message));

        $this->get(route('staff-mail.attachments.preview', $message))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition', 'inline; filename=agenda.pdf');

        $this->get(route('staff-mail.show', $message->conversation))
            ->assertOk()
            ->assertSee('Preview attachment')
            ->assertSee(route('staff-mail.attachments.preview', $message));

        $this->actingAs($outsider)
            ->get(route('staff-mail.attachments.download', $message))
            ->assertNotFound();

        $this->get(route('staff-mail.attachments.preview', $message))
            ->assertNotFound();
    }

    public function test_users_cannot_view_another_users_conversation(): void
    {
        $sender = User::factory()->create(['staff_category' => 'academic']);
        $recipient = User::factory()->create(['staff_category' => 'academic']);
        $outsider = User::factory()->create(['staff_category' => 'academic']);
        $conversation = MailConversation::create([
            'subject' => 'Private note',
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
        ]);

        $this->actingAs($outsider)
            ->get(route('staff-mail.show', $conversation))
            ->assertNotFound();
    }
}
