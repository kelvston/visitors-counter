<?php

namespace Tests\Feature;

use App\Models\Counter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CounterTest extends TestCase
{
    use RefreshDatabase;

    public function test_signed_in_user_can_submit_a_visitor_count(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('counter.increment'), ['gender' => 1])
            ->assertRedirect(route('counter.report'));

        $this->assertDatabaseHas('counters', [
            'user_id' => $user->id,
            'gender' => 1,
            'counts' => 1,
        ]);
    }

    public function test_signed_in_user_can_submit_multiple_visitor_counts(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('counter.increment.multiple'), [
                'maleCountInput' => 2,
                'femaleCountInput' => 3,
                'otherCountInput' => 1,
            ])
            ->assertRedirect(route('counter.report'));

        $this->assertDatabaseHas('counters', ['user_id' => $user->id, 'gender' => 1, 'counts' => 2]);
        $this->assertDatabaseHas('counters', ['user_id' => $user->id, 'gender' => 2, 'counts' => 3]);
        $this->assertDatabaseHas('counters', ['user_id' => $user->id, 'gender' => 3, 'counts' => 1]);
    }

    public function test_counter_requires_authentication(): void
    {
        $this->get(route('counter.report'))->assertRedirect(route('login'));
        $this->post(route('counter.increment'), ['gender' => 1])->assertRedirect(route('login'));

        $this->assertSame(0, Counter::count());
    }

    public function test_single_count_requires_a_valid_gender(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('counter.report'))
            ->post(route('counter.increment'), ['gender' => ''])
            ->assertSessionHasErrors('gender');

        $this->assertSame(0, Counter::count());
    }
}
