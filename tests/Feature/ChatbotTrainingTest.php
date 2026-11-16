<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ChatbotTrainingTest extends TestCase
{
    use RefreshDatabase;

    private string $trainingDataPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->trainingDataPath = storage_path('framework/testing/chatbot-training.json');
        File::delete($this->trainingDataPath);
        config(['services.chatbot.training_data_path' => $this->trainingDataPath]);
    }

    protected function tearDown(): void
    {
        File::delete($this->trainingDataPath);

        parent::tearDown();
    }

    public function test_training_page_requires_authentication(): void
    {
        $this->get(route('chatbot.training.index'))
            ->assertRedirect(route('login'));
    }

    public function test_users_without_training_permission_cannot_access_training_endpoints(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('chatbot.training.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->post(route('chatbot.training.examples.store'), [
                'text' => 'Tell me the visitor total from yesterday',
                'intent' => 'total_visits_by_gender_in_period',
                'period_entity' => 'yesterday',
            ])
            ->assertForbidden();

        $this->actingAs($user)
            ->post(route('chatbot.training.train'))
            ->assertForbidden();

        $this->assertFileDoesNotExist($this->trainingDataPath);
    }

    public function test_admin_can_access_training_page_without_a_separate_assignment(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('chatbot.training.index'))
            ->assertOk()
            ->assertSee('Chatbot NLP training')
            ->assertSee('Train Chatbot');
    }

    public function test_admin_assigned_trainer_can_view_training_page_and_add_example(): void
    {
        $trainer = User::factory()->create([
            'can_train_chatbot' => true,
            'can_access_settings_navigation' => true,
        ]);

        $this->actingAs($trainer)
            ->get(route('chatbot.training.index'))
            ->assertOk()
            ->assertSee('Chatbot NLP training')
            ->assertSee('Train Chatbot');

        $this->actingAs($trainer)
            ->post(route('chatbot.training.examples.store'), [
                'text' => 'Tell me the visitor total from yesterday',
                'intent' => 'total_visits_by_gender_in_period',
                'period_entity' => 'yesterday',
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $trainingData = json_decode(File::get($this->trainingDataPath), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('Tell me the visitor total from yesterday', $trainingData['texts'][0]);
        $this->assertSame('yesterday', $trainingData['annotations'][0]['entities']['period']);
    }

    public function test_entity_annotations_must_appear_in_the_training_text(): void
    {
        $user = User::factory()->create(['can_train_chatbot' => true]);

        $this->actingAs($user)
            ->from(route('chatbot.training.index'))
            ->post(route('chatbot.training.examples.store'), [
                'text' => 'How many visitors today?',
                'intent' => 'total_visits_by_gender_in_period',
                'period_entity' => 'last month',
            ])
            ->assertRedirect(route('chatbot.training.index'))
            ->assertSessionHasErrors('entities');

        $this->assertFileDoesNotExist($this->trainingDataPath);
    }
}
