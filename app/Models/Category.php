<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MongoDB\Laravel\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public const GENERAL_KNOWLEDGE_NAME = 'Conhecimentos gerais';

    protected $fillable = [
        'name',
        'description',
    ];

    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class);
    }

    public static function firstOrCreateGeneralKnowledge(): self
    {
        return static::query()->firstOrCreate(
            ['name' => self::GENERAL_KNOWLEDGE_NAME],
            ['description' => 'Categoria padrão para tópicos sem categoria.']
        );
    }
}
