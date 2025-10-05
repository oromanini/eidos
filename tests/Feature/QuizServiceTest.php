<?php

namespace Tests\Feature;

use App\Models\Topic;
use App\Services\QuizService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QuizServiceTest extends TestCase
{
    use RefreshDatabase;

    protected QuizService $quizService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->quizService = $this->app->make(QuizService::class);
    }

    public function test_import_questions_when_csv_is_valid_should_create_topic_and_question(): void
    {
        // Arrange
        $csvData = "tema,pergunta,alternativa_a,alternativa_b,alternativa_c,alternativa_d,resposta_correta\n"
            ."História Antiga,Qual a capital do Império Romano?,Roma,Atenas,Cartago,Alexandria,a\n";
        Storage::fake('local');
        $file = UploadedFile::fake()->createWithContent('questions.csv', $csvData);

        // Act
        $this->quizService->importQuestionsFromCsv($file->store('imports'));

        // Assert
        $this->assertDatabaseCount('topics', 1);
        $this->assertDatabaseCount('questions', 1);

        $topic = Topic::where('name', 'História Antiga')->first();
        $this->assertNotNull($topic);

        $question = $topic->questions()->first();
        $this->assertNotNull($question);

        $this->assertEquals('Qual a capital do Império Romano?', $question->question_text);
        $this->assertEquals('a', $question->correct_answer);

        // Asserção corrigida: compara o array, não o JSON.
        $this->assertEquals([
            'a' => 'Roma',
            'b' => 'Atenas',
            'c' => 'Cartago',
            'd' => 'Alexandria',
        ], $question->options);
    }

    public function test_import_questions_when_csv_is_empty_should_not_create_records(): void
    {
        // Arrange
        $csvData = "tema,pergunta,alternativa_a,alternativa_b,alternativa_c,alternativa_d,resposta_correta\n";
        Storage::fake('local');
        $file = UploadedFile::fake()->createWithContent('empty.csv', $csvData);

        // Act
        $this->quizService->importQuestionsFromCsv($file->store('imports'));

        // Assert
        $this->assertDatabaseCount('topics', 0);
        $this->assertDatabaseCount('questions', 0);
    }

    public function test_import_questions_when_csv_has_invalid_rows_should_skip_them(): void
    {
        // Arrange
        $csvData = "tema,pergunta,alternativa_a,alternativa_b,alternativa_c,alternativa_d,resposta_correta\n"
            ."Ciências,Qual a fórmula da água?,H2O,O2,CO2,N2,a\n"
            .",Esta pergunta não tem tema,A,B,C,D,b\n"
            ."Geografia,,Esta pergunta não tem texto,X,Y,Z,c\n";
        Storage::fake('local');
        $file = UploadedFile::fake()->createWithContent('mixed.csv', $csvData);

        // Act
        $this->quizService->importQuestionsFromCsv($file->store('imports'));

        // Assert
        $this->assertDatabaseCount('topics', 1);
        $this->assertDatabaseCount('questions', 1);
        $this->assertDatabaseHas('topics', ['name' => 'Ciências']);
        $this->assertDatabaseMissing('topics', ['name' => 'Geografia']);
    }
}
