<?php

namespace Tests\Feature;

use App\Models\LibraryCalendarEvent;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibraryCalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_signed_in_users_can_view_calendar_items(): void
    {
        $reader = User::factory()->create(['staff_category' => 'administrative']);
        $event = $this->createEvent([
            'title' => 'Library orientation',
            'starts_at' => '2026-12-10 09:00:00',
            'ends_at' => '2026-12-10 11:00:00',
        ]);

        $this->actingAs($reader)
            ->get(route('calendar.index', ['month' => '2026-12']))
            ->assertOk()
            ->assertSee('Library Calendar')
            ->assertSee('Library orientation')
            ->assertSee('December 2026')
            ->assertSee('data-calendar-date="2026-12-10"', false)
            ->assertSee('Activities on this date')
            ->assertDontSee('File an event or upcoming activity')
            ->assertDontSee('Add calendar item');

        $this->actingAs($reader)
            ->get(route('calendar.create'))
            ->assertForbidden();
    }

    public function test_admin_assigned_calendar_manager_can_create_update_and_remove_items(): void
    {
        $manager = User::factory()->create([
            'staff_category' => 'academic',
            'can_manage_calendar' => true,
        ]);

        $this->actingAs($manager)
            ->get(route('calendar.index'))
            ->assertOk()
            ->assertSee('Add calendar item')
            ->assertSee('File an event or upcoming activity');

        $this->actingAs($manager)
            ->post(route('calendar.store'), [
                'title' => 'Founders Day',
                'type' => 'holiday',
                'starts_at' => '2026-12-09T00:00',
                'ends_at' => '2026-12-09T23:59',
                'is_all_day' => '1',
                'location' => 'All library branches',
                'description' => 'Library closed.',
            ])
            ->assertRedirect(route('calendar.index', ['month' => '2026-12']))
            ->assertSessionHas('status', 'Calendar item added.');

        $event = LibraryCalendarEvent::firstOrFail();
        $this->assertTrue($event->is_all_day);

        $this->actingAs($manager)
            ->put(route('calendar.update', $event), [
                'title' => 'Updated Founders Day',
                'type' => 'holiday',
                'starts_at' => '2026-12-09T00:00',
                'ends_at' => '2026-12-09T23:59',
                'location' => 'Main Library',
            ])
            ->assertRedirect(route('calendar.index', ['month' => '2026-12']));

        $this->assertFalse($event->fresh()->is_all_day);
        $this->assertSame('Updated Founders Day', $event->fresh()->title);

        $this->actingAs($manager)
            ->delete(route('calendar.destroy', $event))
            ->assertRedirect(route('calendar.index', ['month' => '2026-12']));
        $this->assertDatabaseMissing('library_calendar_events', ['id' => $event->id]);
    }

    public function test_calendar_manager_is_authorized_by_server_and_month_navigation_includes_spanning_items(): void
    {
        $reader = User::factory()->create();
        $administrator = User::factory()->create(['is_admin' => true]);
        $event = $this->createEvent([
            'title' => 'Year-end library closure',
            'type' => 'schedule',
            'starts_at' => '2026-12-30 12:00:00',
            'ends_at' => '2027-01-02 12:00:00',
        ]);

        $this->actingAs($reader)
            ->post(route('calendar.store'), [
                'title' => 'Unauthorized item',
                'type' => 'event',
                'starts_at' => '2026-12-10T09:00',
                'ends_at' => '2026-12-10T10:00',
            ])
            ->assertForbidden();

        $this->actingAs($reader)
            ->get(route('calendar.index', ['month' => '2027-01']))
            ->assertOk()
            ->assertSee('Year-end library closure');

        $this->actingAs($administrator)
            ->get(route('calendar.create'))
            ->assertOk();
        $this->assertFalse($event->is_all_day);
    }

    public function test_calendar_rejects_invalid_type_and_end_before_start(): void
    {
        $manager = User::factory()->create(['can_manage_calendar' => true]);

        $this->actingAs($manager)
            ->from(route('calendar.create'))
            ->post(route('calendar.store'), [
                'title' => 'Invalid item',
                'type' => 'unknown',
                'starts_at' => '2026-12-10T11:00',
                'ends_at' => '2026-12-10T09:00',
            ])
            ->assertSessionHasErrors(['type', 'ends_at']);

        $this->assertDatabaseCount('library_calendar_events', 0);
    }

    public function test_calendar_manager_can_only_file_items_from_today_onward(): void
    {
        CarbonImmutable::setTestNow('2026-11-14 12:00:00');

        try {
            $manager = User::factory()->create(['can_manage_calendar' => true]);
            $this->actingAs($manager)
                ->from(route('calendar.index'))
                ->post(route('calendar.store'), [
                    'title' => 'Past item',
                    'type' => 'event',
                    'starts_at' => '2026-11-13T09:00',
                    'ends_at' => '2026-11-13T10:00',
                ])
                ->assertSessionHasErrors(['starts_at']);

            $this->assertDatabaseCount('library_calendar_events', 0);

            $this->actingAs($manager)
                ->post(route('calendar.store'), [
                    'title' => 'Today item',
                    'type' => 'event',
                    'starts_at' => '2026-11-14T00:00',
                    'ends_at' => '2026-11-14T01:00',
                ])
                ->assertRedirect(route('calendar.index', ['month' => '2026-11']));

            $this->assertDatabaseHas('library_calendar_events', ['title' => 'Today item']);

            $pastEvent = $this->createEvent([
                'starts_at' => '2026-11-13 09:00:00',
                'ends_at' => '2026-11-13 10:00:00',
            ]);
            $this->actingAs($manager)
                ->put(route('calendar.update', $pastEvent), [
                    'title' => 'Updated past item',
                    'type' => 'event',
                    'starts_at' => '2026-11-13T09:00',
                    'ends_at' => '2026-11-13T10:00',
                ])
                ->assertRedirect(route('calendar.index', ['month' => '2026-11']));
            $this->assertDatabaseHas('library_calendar_events', ['title' => 'Updated past item']);

            $this->actingAs($manager)
                ->get(route('calendar.index', ['month' => '2026-11']))
                ->assertOk()
                ->assertSee('Working day (Monday–Friday)')
                ->assertSee('data-calendar-date="2026-11-13"', false)
                ->assertSee('data-calendar-can-file="false"', false)
                ->assertSee('data-calendar-date="2026-11-16"', false)
                ->assertSee('data-calendar-can-file="true"', false);
        } finally {
            CarbonImmutable::setTestNow();
        }
    }

    private function createEvent(array $overrides = []): LibraryCalendarEvent
    {
        return LibraryCalendarEvent::create(array_merge([
            'title' => 'Library event',
            'type' => 'event',
            'starts_at' => '2026-12-01 09:00:00',
            'ends_at' => '2026-12-01 10:00:00',
            'is_all_day' => false,
        ], $overrides));
    }
}
