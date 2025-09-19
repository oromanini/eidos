<?php

namespace App\Repositories;

use App\Models\UserAnswer;

class UserAnswerRepository
{
    public function create(array $data): UserAnswer
    {
        return UserAnswer::create($data);
    }
}
