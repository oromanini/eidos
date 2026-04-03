<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Users are stored in the relational auth database.
     */
    protected $connection = 'mysql';

    protected $fillable = [
        'name',
        'email',
        'avatar',
        'password',
        'provider_name',
        'provider_id',
        'approved_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class);
    }
}
