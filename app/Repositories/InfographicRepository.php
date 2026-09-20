<?php

namespace App\Repositories;

use App\Models\Infographic;

class InfographicRepository
{
    public function create(array $data): Infographic
    {
        return Infographic::create($data);
    }
}
