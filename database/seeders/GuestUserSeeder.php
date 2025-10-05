<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GuestUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Usuário Convidado',
                'email' => 'convidado@eidos.com',
                'password' => Hash::make('password'),
            ]
        );
    }
}
