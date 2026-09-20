<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
    public function firstOrCreateGeneral(): Category
    {
        return Category::query()->firstOrCreate(
            ['name' => 'Assuntos gerais'],
            ['description' => 'Categoria padrão para tópicos.']
        );
    }
}
