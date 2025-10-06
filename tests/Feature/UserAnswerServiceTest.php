<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\Topic;
use App\Models\User;
use App\Repositories\UserAnswerRepository;
use App\Services\UserAnswerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAnswerServiceTest extends TestCase
{
    use RefreshDatabase;

    protected UserAnswerService $userAnswerService;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userAnswerService = new UserAnswerService(new UserAnswerRepository);
        $this->user = User::factory()->create();
    }

    public function test_it_can_save_a_user_answer(): void
    {
        $user = User::factory()->create();

        $topic = Topic::create([
            'name' => 'Teste',
            'description' => 'Tema para testes',
            'user_id' => $this->user->id,
        ]);

        $question = Question::create([
            'topic_id' => $topic->id,
            'question_text' => 'Qual o maior planeta?',
            'correct_answer' => 'a',
            'options' => json_encode(['a' => 'Júpiter']),
        ]);

        $this->userAnswerService->saveUserAnswer($user->id, $question->id, 'a', true);

        $this->assertDatabaseHas('user_answers', [
            'user_id' => $user->id,
            'question_id' => $question->id,
            'user_answer' => 'a',
            'is_correct' => true,
        ]);
    }
}
