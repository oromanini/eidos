<?php

namespace Tests\Feature;

use App\Repositories\QuestionRepository;
use App\Repositories\TopicRepository;
use App\Services\QuizService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QuizServiceTest extends TestCase
{
    use RefreshDatabase;

    protected QuizService $quizService;

    public function setUp(): void
    {
        parent::setUp();

        $this->quizService = new QuizService(
            new QuestionRepository(),
            new TopicRepository()
        );
    }

    public function test_it_can_import_questions_from_a_csv_file(): void
    {
        $csvData = "tema,pergunta,alternativa_a,alternativa_b,alternativa_c,alternativa_d,resposta_correta\n"
            . "História Antiga,Qual a capital do Império Romano?,Roma,Atenas,Cartago,Alexandria,a\n";

        Storage::fake('local');
        $file = UploadedFile::fake()->createWithContent('questions.csv', $csvData);

        $this->quizService->importQuestionsFromCsv($file->store('imports'));

        $this->assertDatabaseHas('topics', [
            'name' => 'História Antiga',
        ]);

        $this->assertDatabaseHas('questions', [
            'question_text' => 'Qual a capital do Império Romano?',
            'correct_answer' => 'a',
        ]);
    }
}
