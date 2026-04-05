<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MongoDB\Laravel\Eloquent\Model;

class Knowledge extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'summary_doc_url',
        'summary_doc_embed_url',
        'summary_html',
        'summary_toc',
        'open_questions',
    ];

    protected $casts = [
        'summary_toc' => 'array',
        'open_questions' => 'array',
    ];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }
}
