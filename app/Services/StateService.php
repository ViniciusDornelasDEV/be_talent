<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CityRepository;
use App\Repositories\StateRepository;
use Illuminate\Database\Eloquent\Collection;

class StateService
{
    public function __construct(
        protected StateRepository $stateRepository,
        protected CityRepository $cityRepository
    ) {}

    public function listStates(): Collection
    {
        return $this->stateRepository->getAllOrderedByName();
    }

    public function listCitiesByState(int $stateId): Collection
    {
        return $this->cityRepository->getByStateIdOrderedByName($stateId);
    }
}
