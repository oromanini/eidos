<?php

namespace App\Repositories;

use App\Models\Topic;

class TopicRepository
{
    public function create(array $data): Topic
    {
        return Topic::create($data);
    }
}
