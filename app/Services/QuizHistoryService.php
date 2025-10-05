<?php

namespace App\Services;

use App\Models\QuizHistory;
use App\Repositories\QuizHistoryRepository;

class QuizHistoryService
{
    public function __construct(protected QuizHistoryRepository $repository) {}

    public function logQuizCompletion(array $data): QuizHistory
    {
        return $this->repository->create($data);
    }
}
