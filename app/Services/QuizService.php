<?php

namespace App\Services;

use App\Repositories\QuestionRepository;
use League\Csv\Reader;

class QuizService
{
    protected QuestionRepository $questionRepository;

    public function __construct(QuestionRepository $questionRepository)
    {
        $this->questionRepository = $questionRepository;
    }

    public function importQuestionsFromCsv(string $filePath): void
    {
        $fullPath = storage_path('app/' . $filePath);

        if (!file_exists($fullPath)) {
            return;
        }

        $csv = Reader::createFromPath($fullPath, 'r');
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();

        foreach ($records as $record) {
            $data = [
                'topic_id' => 1, // Por enquanto, usamos um tema fixo
                'question_text' => $record['pergunta'],
                'correct_answer' => $record['resposta_correta'],
                'options' => json_encode([
                    'a' => $record['alternativa_a'],
                    'b' => $record['alternativa_b'],
                    'c' => $record['alternativa_c'],
                    'd' => $record['alternativa_d'],
                ]),
            ];

            $this->questionRepository->create($data);
        }
    }
}
