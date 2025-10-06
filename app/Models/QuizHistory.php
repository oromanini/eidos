<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'topic_id',
        'score',
        'total_questions',
        'percentage',
        'duration_in_seconds',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function getFormattedDurationAttribute(): string
    {
        $minutes = floor($this->duration_in_seconds / 60);
        $seconds = $this->duration_in_seconds % 60;

        return str_pad($minutes, 2, '0', STR_PAD_LEFT).':'.str_pad($seconds, 2, '0', STR_PAD_LEFT);
    }
}
