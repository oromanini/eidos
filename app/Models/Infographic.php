<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MongoDB\Laravel\Eloquent\Model;

class Infographic extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'title',
        'file_name',
        'file_url',
        'file_type',
        'file_size',
    ];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }
}
