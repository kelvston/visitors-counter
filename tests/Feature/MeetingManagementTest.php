<?php

namespace Tests\Feature;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MeetingManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_record_a_meeting_and_view_its_minutes(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->post(route('admin.meetings.store'), [
                'title' => 'Library planning meeting',
                'meeting_at' => '2026-12-01T10:30',
                'venue' => 'Conference Room',
                'attendees' => 'A. Member, B. Member',
                'minutes' => 'Discussed the annual plan.',
                'minutes_attachment' => UploadedFile::fake()->createWithContent('minutes.pdf', '%PDF meeting minutes'),
            ])
            ->assertRedirect(route('admin.meetings.index'))
            ->assertSessionHas('status');

        $meeting = Meeting::firstOrFail();
        $this->assertSame('Library planning meeting', $meeting->title);
        $this->assertSame('Discussed the annual plan.', $meeting->minutes);
        Storage::disk('local')->assertExists($meeting->minutes_attachment);

        $this->get(route('admin.meetings.index'))
            ->assertOk()
            ->assertSee('Library planning meeting')
            ->assertSee(route('admin.meetings.minutes.download', $meeting))
            ->assertSee('Publication')
            ->assertSee('Admin')
            ->assertSee('Administration')
            ->assertSee('Meeting Management')
            ->assertSee('Manage Users');
        $this->assertSame('administration', $meeting->section);
    }

    public function test_admin_can_download_minutes_pdf(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create(['is_admin' => true]);
        $attachment = UploadedFile::fake()->createWithContent('minutes.pdf', '%PDF recorded minutes')
            ->store('meetings/minutes', 'local');
        $meeting = $this->createMeeting(['minutes_attachment' => $attachment]);

        $response = $this->actingAs($admin)
            ->get(route('admin.meetings.minutes.download', $meeting))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition', 'attachment; filename=library-planning-meeting.pdf');

        $this->assertSame('%PDF recorded minutes', $response->streamedContent());
    }

    public function test_admin_can_attach_and_play_meeting_audio(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create(['is_admin' => true]);
        $audio = $this->fakeWavRecording('meeting.wav');

        $this->actingAs($admin)
            ->post(route('admin.meetings.store'), [
                'title' => 'Recorded planning meeting',
                'meeting_at' => '2026-12-01T10:30',
                'venue' => 'Conference Room',
                'minutes' => 'Planning notes.',
                'audio_attachment' => $audio,
            ])
            ->assertRedirect(route('admin.meetings.index'));

        $meeting = Meeting::where('title', 'Recorded planning meeting')->firstOrFail();
        Storage::disk('local')->assertExists($meeting->audio_attachment);

        $this->get(route('admin.meetings.index'))
            ->assertOk()
            ->assertSee(route('admin.meetings.audio', $meeting))
            ->assertSee('audio', false);

        $this->get(route('admin.meetings.audio', $meeting))
            ->assertOk()
            ->assertHeader('content-type', 'audio/wav');

        $academic = User::factory()->create([
            'staff_category' => 'academic',
            'can_manage_academic_minutes' => true,
        ]);
        $this->actingAs($academic)
            ->get(route('academics.meetings.audio', $meeting))
            ->assertNotFound();

        $oldAudioPath = $meeting->audio_attachment;
        $this->actingAs($admin)
            ->put(route('admin.meetings.update', $meeting), [
                'title' => 'Recorded planning meeting',
                'meeting_at' => '2026-12-01T10:30',
                'venue' => 'Conference Room',
                'minutes' => 'Updated planning notes.',
                'audio_attachment' => $this->fakeWavRecording('replacement.wav'),
            ])
            ->assertRedirect(route('admin.meetings.index'));

        $meeting->refresh();
        Storage::disk('local')->assertMissing($oldAudioPath);
        Storage::disk('local')->assertExists($meeting->audio_attachment);

        $replacementPath = $meeting->audio_attachment;
        $this->delete(route('admin.meetings.destroy', $meeting))
            ->assertRedirect(route('admin.meetings.index'));
        Storage::disk('local')->assertMissing($replacementPath);
    }

    public function test_academic_user_can_attach_and_play_meeting_audio(): void
    {
        Storage::fake('local');
        $academic = User::factory()->create([
            'staff_category' => 'academic',
            'can_manage_academic_minutes' => true,
        ]);

        $this->actingAs($academic)
            ->post(route('academics.meetings.store'), [
                'title' => 'Academic council recording',
                'meeting_at' => '2026-12-03T09:00',
                'venue' => 'Senate Room',
                'minutes' => 'Curriculum discussion.',
                'audio_attachment' => $this->fakeWavRecording('council.wav'),
            ])
            ->assertRedirect(route('academics.meetings.index'));

        $meeting = Meeting::where('title', 'Academic council recording')->firstOrFail();
        Storage::disk('local')->assertExists($meeting->audio_attachment);

        $this->get(route('academics.meetings.audio', $meeting))
            ->assertOk()
            ->assertHeader('content-type', 'audio/wav');

        $this->get(route('academics.meetings.index'))
            ->assertOk()
            ->assertSee(route('academics.meetings.audio', $meeting))
            ->assertSee('Open recording');
    }

    public function test_admin_can_attach_browser_recorded_webm_audio(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create(['is_admin' => true]);
        $webm = "\x1A\x45\xDF\xA3".str_repeat("\x00", 60);

        $this->actingAs($admin)
            ->post(route('admin.meetings.store'), [
                'title' => 'Browser recording',
                'meeting_at' => '2026-12-01T10:30',
                'venue' => 'Conference Room',
                'minutes' => 'Meeting notes.',
                'audio_attachment' => UploadedFile::fake()->createWithContent('recording.webm', $webm),
            ])
            ->assertRedirect(route('admin.meetings.index'))
            ->assertSessionHasNoErrors();

        $meeting = Meeting::where('title', 'Browser recording')->firstOrFail();
        Storage::disk('local')->assertExists($meeting->audio_attachment);
    }

    public function test_academic_user_cannot_access_meeting_records(): void
    {
        $academic = User::factory()->create(['staff_category' => 'academic']);

        $this->actingAs($academic)
            ->get(route('admin.meetings.index'))
            ->assertForbidden();

        $this->get(route('proposal'))
            ->assertOk()
            ->assertSee('Proposals')
            ->assertDontSee('Manage Users');

        $this->get(route('academics.meetings.index'))
            ->assertOk()
            ->assertSee('Publication')
            ->assertSee('Academics')
            ->assertSee('Proposal Management')
            ->assertSee('Meeting Minutes Management')
            ->assertDontSee('Admin');
    }

    public function test_admin_sees_both_publication_sections_and_can_access_academics(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'staff_category' => 'academic',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.meetings.index'))
            ->assertOk()
            ->assertSee('Publication')
            ->assertSee('Admin')
            ->assertSee('Administration')
            ->assertSee('Meeting Management')
            ->assertSee('Manage Users')
            ->assertSee('Academics')
            ->assertSee('Proposal Management')
            ->assertSee('Meeting Minutes Management');

        $this->get(route('academics.meetings.index'))->assertOk();
        $this->get(route('proposal'))->assertOk();
    }

    public function test_admin_can_edit_and_remove_a_meeting(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create(['is_admin' => true]);
        $meeting = $this->createMeeting();

        $this->actingAs($admin)
            ->put(route('admin.meetings.update', $meeting), [
                'title' => 'Updated meeting',
                'meeting_at' => '2026-12-02T11:00',
                'venue' => 'Board Room',
                'attendees' => 'C. Member',
                'minutes' => 'Updated minutes.',
            ])
            ->assertRedirect(route('admin.meetings.index'));

        $this->assertDatabaseHas('meetings', [
            'id' => $meeting->id,
            'title' => 'Updated meeting',
            'venue' => 'Board Room',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.meetings.destroy', $meeting))
            ->assertRedirect(route('admin.meetings.index'));

        $this->assertDatabaseMissing('meetings', ['id' => $meeting->id]);
    }

    public function test_academics_can_manage_academic_meetings_without_access_to_administration_meetings(): void
    {
        Storage::fake('local');
        $academic = User::factory()->create(['staff_category' => 'academic']);
        $academicManager = User::factory()->create([
            'staff_category' => 'academic',
            'can_manage_academic_minutes' => true,
        ]);
        $adminMeeting = $this->createMeeting(['title' => 'Administration minutes']);

        $this->actingAs($academicManager)
            ->post(route('academics.meetings.store'), [
                'title' => 'Academic council',
                'meeting_at' => '2026-12-03T09:00',
                'venue' => 'Senate Room',
                'attendees' => 'Academic staff',
                'minutes' => 'Discussed the curriculum.',
            ])
            ->assertRedirect(route('academics.meetings.index'));

        $academicMeeting = Meeting::where('title', 'Academic council')->firstOrFail();
        $this->assertSame('academic', $academicMeeting->section);

        $this->actingAs($academic)
            ->get(route('academics.meetings.index'))
            ->assertOk()
            ->assertSee('Academic council')
            ->assertDontSee('Record academic meeting')
            ->assertDontSee('Edit');

        $this->actingAs($academic)
            ->post(route('academics.meetings.store'), [
                'title' => 'Reader cannot record',
                'meeting_at' => '2026-12-03T09:00',
                'venue' => 'Senate Room',
                'minutes' => 'Should be denied.',
            ])
            ->assertForbidden();

        $this->actingAs($academicManager)
            ->get(route('academics.meetings.index'))
            ->assertOk()
            ->assertSee('Academic council')
            ->assertDontSee('Administration minutes');

        $this->actingAs($academicManager)->get(route('academics.meetings.edit', $adminMeeting))->assertNotFound();
        $this->actingAs($academicManager)->put(route('academics.meetings.update', $adminMeeting), [
            'title' => 'Unauthorized update',
            'meeting_at' => '2026-12-03T09:00',
            'venue' => 'Senate Room',
            'minutes' => 'Should not update.',
        ])->assertNotFound();
        $this->actingAs($academicManager)->delete(route('academics.meetings.destroy', $adminMeeting))->assertNotFound();

        $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->get(route('admin.meetings.index'))
            ->assertOk()
            ->assertSee('Administration minutes')
            ->assertDontSee('Academic council');
    }

    public function test_academic_meeting_attendance_searches_academic_staff_and_records_permission_absence(): void
    {
        $academic = User::factory()->create([
            'name' => 'Academic Attendee',
            'email' => 'academic.attendee@example.test',
            'staff_category' => 'academic',
        ]);
        User::factory()->create([
            'name' => 'Administrative Attendee',
            'email' => 'administrative.attendee@example.test',
            'staff_category' => 'administrative',
        ]);
        $recorder = User::factory()->create([
            'staff_category' => 'academic',
            'can_manage_academic_minutes' => true,
        ]);

        $this->actingAs($recorder)
            ->get(route('academics.meetings.create'))
            ->assertOk()
            ->assertSee('academic.attendee@example.test')
            ->assertDontSee('administrative.attendee@example.test')
            ->assertSee('Absent with permission')
            ->assertSee('Search staff by name or email');

        $this->post(route('academics.meetings.store'), [
            'title' => 'Academic council',
            'meeting_at' => '2026-12-03T09:00',
            'venue' => 'Senate Room',
            'attendance_submitted' => '1',
            'attendance' => [
                ['user_id' => $academic->id, 'status' => 'absent_with_permission'],
            ],
            'minutes' => 'Discussed the curriculum.',
        ])->assertRedirect(route('academics.meetings.index'));

        $meeting = Meeting::where('title', 'Academic council')->firstOrFail();
        $this->assertSame([[
            'user_id' => $academic->id,
            'name' => 'Academic Attendee',
            'status' => 'absent_with_permission',
        ]], $meeting->attendance);

        $this->get(route('academics.meetings.index'))
            ->assertOk()
            ->assertSee('Academic Attendee — Absent with permission');
    }

    public function test_academic_meeting_rejects_administrative_staff_as_attendee(): void
    {
        $administrative = User::factory()->create(['staff_category' => 'administrative']);
        $academic = User::factory()->create([
            'staff_category' => 'academic',
            'can_manage_academic_minutes' => true,
        ]);

        $this->actingAs($academic)
            ->from(route('academics.meetings.create'))
            ->post(route('academics.meetings.store'), [
                'title' => 'Academic council',
                'meeting_at' => '2026-12-03T09:00',
                'venue' => 'Senate Room',
                'attendance_submitted' => '1',
                'attendance' => [
                    ['user_id' => $administrative->id, 'status' => 'present'],
                ],
                'minutes' => 'Discussed the curriculum.',
            ])
            ->assertRedirect(route('academics.meetings.create'))
            ->assertSessionHasErrors('attendance.0.user_id');

        $this->assertDatabaseMissing('meetings', ['title' => 'Academic council']);
    }

    public function test_administration_and_academic_meeting_lists_filter_by_meeting_date(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $academic = User::factory()->create(['staff_category' => 'academic']);

        $this->createMeeting([
            'title' => 'Administration in range',
            'meeting_at' => '2026-12-10 09:00:00',
        ]);
        $this->createMeeting([
            'title' => 'Administration outside range',
            'meeting_at' => '2026-12-21 09:00:00',
        ]);
        $this->createMeeting([
            'title' => 'Academic in range',
            'meeting_at' => '2026-12-15 09:00:00',
            'section' => 'academic',
        ]);
        $this->createMeeting([
            'title' => 'Academic outside range',
            'meeting_at' => '2026-12-25 09:00:00',
            'section' => 'academic',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.meetings.index', ['from' => '2026-12-10', 'to' => '2026-12-20']))
            ->assertOk()
            ->assertSee('Administration in range')
            ->assertDontSee('Administration outside range')
            ->assertDontSee('Academic in range')
            ->assertSee('name="from"', false)
            ->assertSee('name="to"', false);

        $this->actingAs($academic)
            ->get(route('academics.meetings.index', ['from' => '2026-12-10', 'to' => '2026-12-20']))
            ->assertOk()
            ->assertSee('Academic in range')
            ->assertDontSee('Academic outside range')
            ->assertDontSee('Administration in range')
            ->assertSee('name="from"', false)
            ->assertSee('name="to"', false);
    }

    public function test_administrative_staff_can_read_minutes_but_only_assigned_managers_can_write(): void
    {
        $reader = User::factory()->create(['staff_category' => 'administrative']);
        $manager = User::factory()->create([
            'staff_category' => 'administrative',
            'can_manage_administrative_minutes' => true,
        ]);
        $meeting = $this->createMeeting();

        $this->actingAs($reader)
            ->get(route('admin.meetings.index'))
            ->assertOk()
            ->assertSee($meeting->title)
            ->assertDontSee('Record meeting')
            ->assertDontSee('Edit');
        $this->actingAs($reader)->get(route('admin.meetings.create'))->assertForbidden();

        $this->actingAs($manager)
            ->get(route('admin.meetings.create'))
            ->assertOk();
        $this->actingAs($manager)
            ->post(route('admin.meetings.store'), [
                'title' => 'Administrative council',
                'meeting_at' => '2026-12-05T10:00',
                'venue' => 'Board Room',
                'minutes' => 'Administrative decisions.',
            ])
            ->assertRedirect(route('admin.meetings.index'));

        $this->actingAs($reader)
            ->delete(route('admin.meetings.destroy', $meeting))
            ->assertForbidden();

        $crossCategoryManager = User::factory()->create([
            'staff_category' => 'administrative',
            'can_manage_academic_minutes' => true,
        ]);
        $this->actingAs($crossCategoryManager)
            ->get(route('academics.meetings.create'))
            ->assertOk();
    }

    private function createMeeting(array $overrides = []): Meeting
    {
        return Meeting::create(array_merge([
            'title' => 'Library planning meeting',
            'meeting_at' => '2026-12-01 10:30:00',
            'venue' => 'Conference Room',
            'attendees' => 'A. Member',
            'minutes' => 'Meeting minutes.',
        ], $overrides));
    }

    private function fakeWavRecording(string $filename): UploadedFile
    {
        $wav = pack('a4Va4a4VvvVVvv', 'RIFF', 36, 'WAVE', 'fmt ', 16, 1, 1, 8000, 16000, 2, 16)
            .'data'.pack('V', 0);

        return UploadedFile::fake()->createWithContent($filename, $wav);
    }
}
