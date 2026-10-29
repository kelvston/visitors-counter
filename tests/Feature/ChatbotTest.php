<?php

namespace Tests\Feature;

use App\Models\Counter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    public function test_chatbot_answers_greetings_using_the_trained_model(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('chatbot.respond'), ['message' => 'Hello'])
            ->assertOk()
            ->assertJsonPath('reply', 'Hello! Ask me about visitor totals, such as "How many visitors came this week?"');
    }

    public function test_chatbot_reports_the_signed_in_users_visitor_count(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();

        Counter::create([
            'user_id' => $user->id,
            'gender' => 1,
            'counts' => 5,
        ]);
        Counter::create([
            'user_id' => $user->id,
            'gender' => 2,
            'counts' => 3,
        ]);
        Counter::create([
            'user_id' => $anotherUser->id,
            'gender' => 1,
            'counts' => 100,
        ]);

        $this->actingAs($user)
            ->postJson(route('chatbot.respond'), ['message' => 'How many male visitors today?'])
            ->assertOk()
            ->assertJsonPath('reply', 'There were 5 male visitors today.');
    }

    public function test_chatbot_rejects_empty_messages(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('chatbot.respond'), ['message' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('message');
    }

    public function test_chatbot_guides_unrecognized_questions_to_visitor_count_queries(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('chatbot.respond'), ['message' => 'What is the weather?'])
            ->assertOk()
            ->assertJsonPath('reply', 'I could not understand that yet. An administrator can improve my responses by adding examples and retraining the model.');
    }

    public function test_chatbot_requires_authentication(): void
    {
        $this->postJson(route('chatbot.respond'), ['message' => 'How many visitors today?'])
            ->assertUnauthorized();
    }

    public function test_chatbot_page_redirects_to_the_visitor_summary(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('chatbot.index'))
            ->assertRedirect(route('counter.report'));
    }
}
