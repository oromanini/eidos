<?php

namespace App\Repositories;

use App\Models\QuizHistory;

class QuizHistoryRepository
{
    public function create(array $data): QuizHistory
    {
        return QuizHistory::create($data);
    }
}
