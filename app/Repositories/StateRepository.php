<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Customer\Models\State;

class StateRepository
{
    public function getAllOrderedByName(): Collection
    {
        return State::query()
            ->orderBy('name')
            ->get();
    }
}
