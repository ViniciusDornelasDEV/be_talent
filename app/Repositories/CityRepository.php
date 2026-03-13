<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Customer\Models\City;

class CityRepository
{
    public function getByStateIdOrderedByName(int $stateId): Collection
    {
        return City::query()
            ->where('state_id', $stateId)
            ->orderBy('name')
            ->get();
    }
}
