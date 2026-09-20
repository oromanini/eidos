<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use App\Repositories\QuestionRepository;
use App\Repositories\TopicRepository;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

class QuizService
{
    protected QuestionRepository $questionRepository;

    protected TopicRepository $topicRepository;

    public function __construct(
        QuestionRepository $questionRepository,
        TopicRepository $topicRepository,
        protected CategoryRepository $categoryRepository
    ) {
        $this->questionRepository = $questionRepository;
        $this->topicRepository = $topicRepository;
    }

    public function importQuestionsFromCsv(string $filePath, int|string $userId): void
    {
        if (! Storage::exists($filePath)) {
            return;
        }

        $fileContent = Storage::get($filePath);
        $lines = preg_split('/\R/', $fileContent) ?: [];

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
            $defaultCategory = $this->categoryRepository->firstOrCreateGeneral();
            $this->topicRepository->firstOrCreateForUser(
                $topicName,
                $userId,
                $topicDescription,
                $defaultCategory->getKey()
            );
            Storage::delete($filePath);

            return;
        }

        $csvContent = implode(PHP_EOL, $csvDataLines);
        $csv = Reader::createFromString($csvContent);
        $csv->setHeaderOffset(0);

        $requiredHeaders = [
            'pergunta',
            'alternativa_a',
            'alternativa_b',
            'alternativa_c',
            'alternativa_d',
            'resposta_correta',
        ];

        if (array_diff($requiredHeaders, $csv->getHeader())) {
            Storage::delete($filePath);

            return;
        }

        $records = $csv->getRecords();

        $questionsToCreate = [];
        foreach ($records as $record) {
            // ---> INÍCIO DA VALIDAÇÃO MELHORADA <---
            $questionText = trim($record['pergunta']);
            $altA = trim($record['alternativa_a']);
            $altB = trim($record['alternativa_b']);
            $altC = trim($record['alternativa_c']);
            $altD = trim($record['alternativa_d']);
            $correctAnswer = strtolower(trim($record['resposta_correta']));

            // Pula a linha se qualquer campo essencial estiver faltando.
            if (
                empty($questionText)
                || empty($altA)
                || empty($altB)
                || empty($altC)
                || empty($altD)
                || ! in_array($correctAnswer, ['a', 'b', 'c', 'd'], true)
            ) {
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
            $defaultCategory = $this->categoryRepository->firstOrCreateGeneral();
            $topic = $this->topicRepository->firstOrCreateForUser(
                $topicName,
                $userId,
                $topicDescription,
                $defaultCategory->getKey()
            );

            foreach ($questionsToCreate as $questionData) {
                $questionData['topic_id'] = $topic->getKey();
                $this->questionRepository->create($questionData);
            }
        }

        Storage::delete($filePath);
    }
}
