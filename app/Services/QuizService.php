<?php

namespace App\Services;

use App\Models\Topic; // Importe o model Topic
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
        if (!Storage::exists($filePath)) {
            return;
        }

        $fileContent = Storage::get($filePath);
        $lines = explode(PHP_EOL, $fileContent);

        $topicName = 'Tópico importado em ' . now()->format('d/m/Y');
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
            } elseif (!empty(trim($line))) {
                $csvDataLines[] = $line;
            }
        }

        $topic = Topic::firstOrCreate(
            ['name' => $topicName, 'user_id' => $userId],
            ['description' => $topicDescription]
        );

        if (empty($csvDataLines)) {
            Storage::delete($filePath);
            return;
        }

        $csvContent = implode(PHP_EOL, $csvDataLines);
        $csv = Reader::createFromString($csvContent);
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords();

        foreach ($records as $record) {
            if (empty(trim($record['pergunta']))) {
                continue;
            }

            $data = [
                'topic_id' => $topic->id,
                'question_text' => trim($record['pergunta']),
                'correct_answer' => trim($record['resposta_correta']),
                'options' => [
                    'a' => trim($record['alternativa_a']),
                    'b' => trim($record['alternativa_b']),
                    'c' => trim($record['alternativa_c']),
                    'd' => trim($record['alternativa_d']),
                ],
            ];

            $this->questionRepository->create($data);
        }

        Storage::delete($filePath);
    }
}
