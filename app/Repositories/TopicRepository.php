<?php

namespace App\Repositories;

use App\Models\Topic;
use Illuminate\Support\Collection;

class TopicRepository
{
    public function create(array $data): Topic
    {
        return Topic::create($data);
    }

    public function firstOrCreateForUser(
        string $name,
        int|string $userId,
        string $description,
        int|string $categoryId
    ): Topic {
        $topic = Topic::firstOrCreate(
            ['name' => $name, 'user_id' => $userId],
            ['description' => $description, 'category_id' => $categoryId]
        );

        if (blank($topic->category_id)) {
            $topic->update(['category_id' => $categoryId]);
        }

        return $topic;
    }

    public function createOrUpdate(string $name, ?string $description): Topic
    {
        return Topic::updateOrCreate(
            ['name' => trim($name)],
            ['description' => trim($description)]
        );
    }

    public function findByNameOrCreate(string $name): Topic
    {
        return Topic::firstOrCreate(
            ['name' => trim($name)],
            ['description' => '']
        );
    }

    public function all(): Collection
    {
        return Topic::all();
    }
}
