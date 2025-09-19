<?php

namespace App\Repositories;

use App\Models\Question;

class QuestionRepository
{
    public function create(array $data): Question
    {
        return Question::create($data);
    }
}
