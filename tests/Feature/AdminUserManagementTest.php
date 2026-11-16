<?php

namespace Tests\Feature;

use App\Models\Counter;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_user_details_password_and_category(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();

        $this->actingAs($admin)
            ->put(route('admin.users.update', $user), [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'staff_category' => 'academic',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
                'can_manage_academic_minutes' => '1',
                'can_manage_proposals' => '1',
                'can_manage_calendar' => '1',
                'can_train_chatbot' => '1',
                'can_access_settings_navigation' => '1',
            ])
            ->assertRedirect(route('admin.users.index'));

        $user->refresh();
        $this->assertSame('Updated Name', $user->name);
        $this->assertSame('updated@example.com', $user->email);
        $this->assertSame('academic', $user->staff_category);
        $this->assertTrue(Hash::check('new-password-123', $user->password));
        $this->assertTrue($user->canManageMinutesFor('academic'));
        $this->assertTrue($user->canManageProposals());
        $this->assertTrue($user->canManageCalendar());
        $this->assertTrue($user->canTrainChatbot());
        $this->assertTrue($user->canAccessSettingsNavigation());
        $this->assertFalse($user->canManageMinutesFor('administrative'));
    }

    public function test_admin_assigns_settings_navigation_visibility_to_a_user(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('proposal'))
            ->assertOk()
            ->assertDontSee('Settings');

        $this->actingAs($admin)
            ->put(route('admin.users.update', $user), [
                'name' => $user->name,
                'email' => $user->email,
                'staff_category' => $user->staff_category,
                'can_access_settings_navigation' => '1',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->actingAs($user->fresh())
            ->get(route('proposal'))
            ->assertOk()
            ->assertSee('Settings')
            ->assertDontSee('Manage Users')
            ->assertDontSee('Train Chatbot');
    }

    public function test_non_admin_cannot_access_user_management(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_admin_can_archive_user_without_removing_visitor_records(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        Counter::create(['user_id' => $user->id, 'gender' => 1, 'counts' => 4]);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index'));

        $this->assertSoftDeleted('users', ['id' => $user->id]);
        $this->assertDatabaseHas('counters', ['user_id' => $user->id, 'counts' => 4]);
    }

    public function test_user_cannot_remove_their_own_admin_account(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertForbidden();

        $this->assertNotSoftDeleted('users', ['id' => $admin->id]);
    }

    public function test_admin_can_remove_another_admin_without_removing_their_own_access(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $otherAdmin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $otherAdmin))
            ->assertRedirect(route('admin.users.index'));

        $this->assertSoftDeleted('users', ['id' => $otherAdmin->id]);
        $this->assertTrue($admin->fresh()->isAdmin());
    }

    public function test_every_staff_member_can_read_proposals_but_only_managers_can_submit(): void
    {
        $academic = User::factory()->create(['staff_category' => 'academic']);
        $administrative = User::factory()->create(['staff_category' => 'administrative']);

        $this->actingAs($academic)
            ->get(route('proposal'))
            ->assertOk()
            ->assertSee('Proposals Pending Submission')
            ->assertDontSee('Submit Proposal');

        $this->actingAs($administrative)
            ->get(route('proposals.create'))
            ->assertForbidden();
        $this->actingAs($administrative)
            ->post(route('proposals.store'), [])
            ->assertForbidden();
        $this->actingAs($administrative)
            ->get(route('proposal'))
            ->assertOk()
            ->assertDontSee('Submit Proposal');

        $manager = User::factory()->create([
            'staff_category' => 'administrative',
            'can_manage_proposals' => true,
        ]);
        $this->actingAs($manager)
            ->get(route('proposals.create'))
            ->assertOk();
        $this->actingAs($manager)
            ->get(route('proposal'))
            ->assertSee('Submit Proposal');
    }

    public function test_admin_bootstrap_command_promotes_an_existing_account_by_email(): void
    {
        $user = User::factory()->create(['email' => 'first-admin@example.com']);

        $this->artisan('users:make-admin', ['email' => $user->email])
            ->expectsOutputToContain('Administrator access granted')
            ->assertExitCode(0);

        $this->assertTrue($user->fresh()->isAdmin());
    }

    public function test_database_seeder_creates_the_admin_account_with_supplied_password(): void
    {
        app(DatabaseSeeder::class)->run('a-test-only-password');

        $admin = User::where('email', 'admin@aru.ac.tz')->firstOrFail();
        $this->assertTrue($admin->isAdmin());
        $this->assertSame('academic', $admin->staff_category);
        $this->assertTrue(Hash::check('a-test-only-password', $admin->password));
    }
}
