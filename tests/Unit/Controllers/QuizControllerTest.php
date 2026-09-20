<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\QuizController;
use App\Models\Category;
use App\Models\Question;
use App\Models\User;
use App\Repositories\CategoryRepository;
use App\Services\QuizHistoryService;
use App\Services\QuizService;
use App\Services\UserAnswerService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class QuizControllerTest extends TestCase
{
    public function test_show_import_form_lists_the_available_categories(): void
    {
        $category = new Category(['name' => 'História']);
        $category->setAttribute('id', 'category-7');

        $categoryRepository = Mockery::mock(CategoryRepository::class);
        $categoryRepository->shouldReceive('allOrdered')->once()->andReturn(collect([$category]));

        $controller = new QuizController(
            Mockery::mock(QuizService::class),
            Mockery::mock(UserAnswerService::class),
            Mockery::mock(QuizHistoryService::class),
            $categoryRepository
        );

        $view = $controller->showImportForm();

        $this->assertSame('eidos.import', $view->name());
        $this->assertSame([$category], $view->getData()['categories']->all());
    }

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
            Mockery::mock(QuizHistoryService::class),
            Mockery::mock(CategoryRepository::class)
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
            Mockery::mock(QuizHistoryService::class),
            Mockery::mock(CategoryRepository::class)
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

    public function test_import_passes_the_selected_category_to_the_service(): void
    {
        Storage::fake('local');

        $user = new User;
        $user->setAttribute('id', 'user-1');
        $this->actingAs($user);

        $categoryRepository = Mockery::mock(CategoryRepository::class);
        $categoryRepository->shouldReceive('existsById')->once()->with('category-7')->andReturnTrue();

        $quizService = Mockery::mock(QuizService::class);
        $quizService
            ->shouldReceive('importQuestionsFromCsv')
            ->once()
            ->with(Mockery::pattern('#^imports/.+\.csv$#'), 'user-1', 'category-7');

        $controller = new QuizController(
            $quizService,
            Mockery::mock(UserAnswerService::class),
            Mockery::mock(QuizHistoryService::class),
            $categoryRepository
        );
        $request = Request::create('/import', 'POST', ['category_id' => 'category-7'], [], [
            'csv_file' => UploadedFile::fake()->createWithContent(
                'questions.csv',
                "pergunta,alternativa_a,alternativa_b,alternativa_c,alternativa_d,resposta_correta\nPergunta,A,B,C,D,a"
            ),
        ]);
        $request->setUserResolver(fn () => $user);

        $response = $controller->import($request);

        $this->assertSame(route('dashboard'), $response->getTargetUrl());
    }

    public function test_import_rejects_a_category_that_does_not_exist(): void
    {
        Storage::fake('local');

        $user = new User;
        $user->setAttribute('id', 'user-1');
        $this->actingAs($user);

        $categoryRepository = Mockery::mock(CategoryRepository::class);
        $categoryRepository->shouldReceive('existsById')->once()->with('missing')->andReturnFalse();

        $quizService = Mockery::mock(QuizService::class);
        $quizService->shouldNotReceive('importQuestionsFromCsv');

        $controller = new QuizController(
            $quizService,
            Mockery::mock(UserAnswerService::class),
            Mockery::mock(QuizHistoryService::class),
            $categoryRepository
        );
        $request = Request::create('/import', 'POST', ['category_id' => 'missing'], [], [
            'csv_file' => UploadedFile::fake()->create('questions.csv', 1, 'text/csv'),
        ]);

        $this->expectException(ValidationException::class);

        $controller->import($request);
    }
}
