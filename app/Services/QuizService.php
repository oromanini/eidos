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
        $lines = explode(PHP_EOL, $fileContent);

        $topicName = 'Tópico importado em ' . now()->format('d/m/Y');
        $topicDescription = '';
        $csvDataLines = [];

        // Lógica de extração de metadados melhorada
        foreach ($lines as $line) {
            if (str_starts_with($line, '# TEMA:')) {
                // Separa a linha no primeiro ':' e pega a segunda parte
                $parts = explode(':', $line, 2);
                if (isset($parts[1])) {
                    $topicName = trim($parts[1]);
                }
            } elseif (str_starts_with($line, '# DESCRIÇÃO:')) {
                // Faz o mesmo para a descrição
                $parts = explode(':', $line, 2);
                if (isset($parts[1])) {
                    $topicDescription = trim($parts[1]);
                }
            } elseif (!empty(trim($line))) {
                $csvDataLines[] = $line;
            }
        }

        // O resto do método permanece o mesmo...
        $topic = $this->topicRepository->createOrUpdate($topicName, $topicDescription);

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
    }}
