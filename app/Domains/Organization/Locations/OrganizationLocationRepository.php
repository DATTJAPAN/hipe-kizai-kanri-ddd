<?php

declare(strict_types=1);

namespace App\Domains\Organization\Locations;

use App\Domains\Shared\Models\OrganizationLocation;
use App\Domains\Shared\Repositories\BaseRepository;

class OrganizationLocationRepository extends BaseRepository
{
    public function __construct(OrganizationLocation $model)
    {
        parent::__construct($model);
    }
}
