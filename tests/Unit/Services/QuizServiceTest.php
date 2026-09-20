<?php

namespace Tests\Unit\Services;

use App\Models\Question;
use App\Models\Topic;
use App\Repositories\QuestionRepository;
use App\Repositories\TopicRepository;
use App\Services\QuizService;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class QuizServiceTest extends TestCase
{
    private QuestionRepository $questionRepository;

    private TopicRepository $topicRepository;

    private QuizService $service;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->questionRepository = Mockery::mock(QuestionRepository::class);
        $this->topicRepository = Mockery::mock(TopicRepository::class);
        $this->service = new QuizService(
            $this->questionRepository,
            $this->topicRepository
        );
    }

    protected function tearDown(): void
    {
        Date::setTestNow();
        parent::tearDown();
    }

    public function test_it_returns_without_writing_when_the_file_does_not_exist(): void
    {
        $this->topicRepository->shouldNotReceive('firstOrCreateForUser');
        $this->questionRepository->shouldNotReceive('create');

        $this->service->importQuestionsFromCsv('imports/missing.csv', 'user-1', 'category-7');

        $this->assertTrue(true);
    }

    public function test_it_imports_metadata_and_all_valid_questions(): void
    {
        $path = 'imports/questions.csv';
        Storage::put($path, implode("\r\n", [
            '# TEMA: Arquitetura de Software',
            '# DESCRIÇÃO: Conceitos fundamentais',
            'pergunta,alternativa_a,alternativa_b,alternativa_c,alternativa_d,resposta_correta',
            'Pergunta 1,A1,B1,C1,D1,B',
            'Pergunta 2,A2,B2,C2,D2,d',
        ]));

        $topic = $this->modelWithKey(Topic::class, 'topic-1');

        $this->topicRepository
            ->shouldReceive('firstOrCreateForUser')
            ->once()
            ->with('Arquitetura de Software', 'user-1', 'Conceitos fundamentais', 'category-7')
            ->andReturn($topic);
        $this->questionRepository
            ->shouldReceive('create')
            ->once()
            ->with([
                'question_text' => 'Pergunta 1',
                'correct_answer' => 'b',
                'options' => ['a' => 'A1', 'b' => 'B1', 'c' => 'C1', 'd' => 'D1'],
                'topic_id' => 'topic-1',
            ])
            ->andReturn(new Question);
        $this->questionRepository
            ->shouldReceive('create')
            ->once()
            ->with([
                'question_text' => 'Pergunta 2',
                'correct_answer' => 'd',
                'options' => ['a' => 'A2', 'b' => 'B2', 'c' => 'C2', 'd' => 'D2'],
                'topic_id' => 'topic-1',
            ])
            ->andReturn(new Question);

        $this->service->importQuestionsFromCsv($path, 'user-1', 'category-7');

        Storage::assertMissing($path);
    }

    public function test_it_creates_an_empty_topic_with_the_default_name_when_the_file_has_no_data(): void
    {
        Date::setTestNow('2026-09-20 12:00:00');
        $path = 'imports/empty.csv';
        Storage::put($path, '');

        $topic = $this->modelWithKey(Topic::class, 'topic-1');

        $this->topicRepository
            ->shouldReceive('firstOrCreateForUser')
            ->once()
            ->with('Tópico importado em 20/09/2026', 'user-1', '', 'category-7')
            ->andReturn($topic);
        $this->questionRepository->shouldNotReceive('create');

        $this->service->importQuestionsFromCsv($path, 'user-1', 'category-7');

        Storage::assertMissing($path);
    }

    public function test_it_skips_incomplete_rows_and_invalid_answer_keys(): void
    {
        $path = 'imports/invalid-rows.csv';
        Storage::put($path, implode("\n", [
            '# TEMA: Ciências',
            'pergunta,alternativa_a,alternativa_b,alternativa_c,alternativa_d,resposta_correta',
            'Sem alternativa,A,,C,D,a',
            'Resposta inválida,A,B,C,D,e',
        ]));

        $this->topicRepository->shouldNotReceive('firstOrCreateForUser');
        $this->questionRepository->shouldNotReceive('create');

        $this->service->importQuestionsFromCsv($path, 'user-1', 'category-7');

        Storage::assertMissing($path);
    }

    public function test_it_rejects_a_csv_without_the_required_headers(): void
    {
        $path = 'imports/wrong-headers.csv';
        Storage::put($path, "question,answer\nPergunta,A");

        $this->topicRepository->shouldNotReceive('firstOrCreateForUser');
        $this->questionRepository->shouldNotReceive('create');

        $this->service->importQuestionsFromCsv($path, 'user-1', 'category-7');

        Storage::assertMissing($path);
    }

    /**
     * @template TModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TModel>  $modelClass
     * @return TModel
     */
    private function modelWithKey(string $modelClass, string $key)
    {
        $model = new $modelClass;
        $model->setAttribute($model->getKeyName(), $key);

        return $model;
    }
}
