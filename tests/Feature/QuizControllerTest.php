<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_answer_treats_uppercase_correct_answer_as_valid(): void
    {
        $user = User::factory()->create();
        $topic = Topic::create([
            'name' => 'Teste',
            'description' => 'Tema de teste',
            'user_id' => $user->id,
        ]);

        $question = Question::create([
            'topic_id' => $topic->id,
            'question_text' => 'Qual alternativa é correta?',
            'correct_answer' => 'B',
            'options' => [
                'a' => 'A',
                'b' => 'B',
                'c' => 'C',
                'd' => 'D',
            ],
        ]);

        $response = $this->actingAs($user)->postJson(route('quiz.answer', $question), [
            'answer' => 'b',
        ]);

        $response->assertOk()
            ->assertJson([
                'is_correct' => true,
                'correct_answer' => 'b',
            ]);
    }
}
