<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the questions for the topic.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
