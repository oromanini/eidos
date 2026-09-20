<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Collection;

class CategoryRepository
{
    public function allOrdered(): Collection
    {
        return Category::query()->orderBy('name')->get();
    }

    public function existsById(int|string $categoryId): bool
    {
        return Category::query()->whereKey($categoryId)->exists();
    }
}
