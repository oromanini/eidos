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
