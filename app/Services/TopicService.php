<?php

namespace App\Services;

use App\Models\Topic;
use App\Repositories\TopicRepository;

class TopicService
{
    protected TopicRepository $topicRepository;

    public function __construct(TopicRepository $topicRepository)
    {
        $this->topicRepository = $topicRepository;
    }

    public function createTopic(string $name, ?string $description = null): Topic
    {
        return $this->topicRepository->create([
            'name' => $name,
            'description' => $description,
        ]);
    }
}
