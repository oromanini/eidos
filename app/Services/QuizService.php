<?php

namespace App\Services;

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
    
    public function importQuestionsFromCsv(string $filePath): void
    {
        if (!Storage::exists($filePath)) {
            return;
        }

        $fileContent = Storage::get($filePath);

        $csv = Reader::createFromString($fileContent);
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();

        foreach ($records as $record) {
            if (empty(trim($record['tema'])) || empty(trim($record['pergunta']))) {
                continue;
            }

            $topic = $this->topicRepository->findByNameOrCreate($record['tema']);

            $data = [
                'topic_id' => $topic->id,
                'question_text' => $record['pergunta'],
                'correct_answer' => $record['resposta_correta'],
                'options' => [
                    'a' => $record['alternativa_a'],
                    'b' => $record['alternativa_b'],
                    'c' => $record['alternativa_c'],
                    'd' => $record['alternativa_d'],
                ],
            ];

            $this->questionRepository->create($data);
        }

        Storage::delete($filePath);
    }}
