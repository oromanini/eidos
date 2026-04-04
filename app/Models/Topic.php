<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use MongoDB\Laravel\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category_id',
        'user_id',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $topic): void {
            if (blank($topic->category_id)) {
                $topic->category_id = Category::firstOrCreateGeneralKnowledge()->getKey();
            }
        });
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }



    public function knowledge(): HasOne
    {
        return $this->hasOne(Knowledge::class);
    }

    public function infographics(): HasMany
    {
        return $this->hasMany(Infographic::class);
    }

    public function audios(): HasMany
    {
        return $this->hasMany(Audio::class);
    }

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function assignUncategorizedToGeneralKnowledge(): void
    {
        $defaultCategoryId = Category::firstOrCreateGeneralKnowledge()->getKey();

        static::query()
            ->where(function ($query): void {
                $query->whereNull('category_id')
                    ->orWhere('category_id', '');
            })
            ->update(['category_id' => $defaultCategoryId]);
    }
}
