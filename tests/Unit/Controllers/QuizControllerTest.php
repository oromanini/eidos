<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\QuizController;
use App\Models\Question;
use App\Models\User;
use App\Services\QuizHistoryService;
use App\Services\QuizService;
use App\Services\UserAnswerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Mockery;
use Tests\TestCase;

class QuizControllerTest extends TestCase
{
    public function test_answer_treats_uppercase_correct_answer_as_valid(): void
    {
        $user = new User;
        $user->setAttribute('id', 'user-1');
        $this->actingAs($user);

        $question = new Question([
            'question_text' => 'Qual alternativa é correta?',
            'correct_answer' => 'B',
            'options' => [
                'a' => 'A',
                'b' => 'B',
                'c' => 'C',
                'd' => 'D',
            ],
        ]);
        $question->setAttribute('id', 'question-1');

        $answerService = Mockery::mock(UserAnswerService::class);
        $answerService
            ->shouldReceive('saveUserAnswer')
            ->once()
            ->with('user-1', 'question-1', 'b', true);

        Session::put('quiz.score', 0);
        $controller = new QuizController(
            Mockery::mock(QuizService::class),
            $answerService,
            Mockery::mock(QuizHistoryService::class)
        );
        $request = Request::create('/quiz/answer', 'POST', ['answer' => 'b']);

        $response = $controller->answer($request, $question);

        $this->assertSame(200, $response->status());
        $this->assertSame([
            'is_correct' => true,
            'correct_answer' => 'b',
        ], $response->getData(true));
        $this->assertSame(1, Session::get('quiz.score'));
    }

    public function test_answer_does_not_increment_the_score_when_the_answer_is_wrong(): void
    {
        $user = new User;
        $user->setAttribute('id', 'user-1');
        $this->actingAs($user);

        $question = new Question([
            'question_text' => 'Qual alternativa é correta?',
            'correct_answer' => 'a',
            'options' => ['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'],
        ]);
        $question->setAttribute('id', 'question-1');

        $answerService = Mockery::mock(UserAnswerService::class);
        $answerService
            ->shouldReceive('saveUserAnswer')
            ->once()
            ->with('user-1', 'question-1', 'c', false);

        Session::put('quiz.score', 0);
        $controller = new QuizController(
            Mockery::mock(QuizService::class),
            $answerService,
            Mockery::mock(QuizHistoryService::class)
        );

        $response = $controller->answer(
            Request::create('/quiz/answer', 'POST', ['answer' => 'c']),
            $question
        );

        $this->assertSame([
            'is_correct' => false,
            'correct_answer' => 'a',
        ], $response->getData(true));
        $this->assertSame(0, Session::get('quiz.score'));
    }
}
