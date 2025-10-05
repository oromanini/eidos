<?php

namespace App\Services;

use App\Models\Topic;
use App\Repositories\QuestionRepository;
use App\Repositories\TopicRepository;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

class QuizService
{
    protected QuestionRepository $questionRepository;

    protected TopicRepository $topicRepository;

    public function __construct(QuestionRepository $questionRepository, TopicRepository $topicRepository)
    {
        $this->questionRepository = $questionRepository;
        $this->topicRepository = $topicRepository;
    }

    public function importQuestionsFromCsv(string $filePath, int $userId): void
    {
        if (! Storage::exists($filePath)) {
            return;
        }

        $fileContent = Storage::get($filePath);
        $lines = explode(PHP_EOL, $fileContent);

        $topicName = 'Tópico importado em '.now()->format('d/m/Y');
        $topicDescription = '';
        $csvDataLines = [];

        foreach ($lines as $line) {
            if (str_starts_with($line, '# TEMA:')) {
                $parts = explode(':', $line, 2);
                if (isset($parts[1])) {
                    $topicName = trim($parts[1]);
                }
            } elseif (str_starts_with($line, '# DESCRIÇÃO:')) {
                $parts = explode(':', $line, 2);
                if (isset($parts[1])) {
                    $topicDescription = trim($parts[1]);
                }
            } elseif (! empty(trim($line))) {
                $csvDataLines[] = $line;
            }
        }

        // Se não houver linhas de dados, podemos criar o tópico vazio ou simplesmente retornar.
        // Neste caso, vamos criar o tópico e depois verificar as questões.
        if (empty($csvDataLines)) {
            Topic::firstOrCreate(
                ['name' => $topicName, 'user_id' => $userId],
                ['description' => $topicDescription]
            );
            Storage::delete($filePath);

            return;
        }

        $csvContent = implode(PHP_EOL, $csvDataLines);
        $csv = Reader::createFromString($csvContent);
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords();

        $questionsToCreate = [];
        foreach ($records as $record) {
            // ---> INÍCIO DA VALIDAÇÃO MELHORADA <---
            $questionText = trim($record['pergunta']);
            $altA = trim($record['alternativa_a']);
            $altB = trim($record['alternativa_b']);
            $altC = trim($record['alternativa_c']);
            $altD = trim($record['alternativa_d']);
            $correctAnswer = trim($record['resposta_correta']);

            // Pula a linha se qualquer campo essencial estiver faltando.
            if (empty($questionText) || empty($altA) || empty($altB) || empty($altC) || empty($altD) || empty($correctAnswer)) {
                continue;
            }
            // ---> FIM DA VALIDAÇÃO MELHORADA <---

            $questionsToCreate[] = [
                'question_text' => $questionText,
                'correct_answer' => $correctAnswer,
                'options' => [
                    'a' => $altA,
                    'b' => $altB,
                    'c' => $altC,
                    'd' => $altD,
                ],
            ];
        }

        // Apenas cria o tópico se houver questões válidas para ele.
        if (! empty($questionsToCreate)) {
            $topic = Topic::firstOrCreate(
                ['name' => $topicName, 'user_id' => $userId],
                ['description' => $topicDescription]
            );

            foreach ($questionsToCreate as $questionData) {
                $questionData['topic_id'] = $topic->id;
                $this->questionRepository->create($questionData);
            }
        }

        Storage::delete($filePath);
    }
}
