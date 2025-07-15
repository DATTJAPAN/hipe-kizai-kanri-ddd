<?php

declare(strict_types=1);

namespace App\Domains\Organization\Units;

use App\Domains\Shared\Repository\BaseRepository;

class OrganizationUnitRepository extends BaseRepository
{
    public function __construct(OrganizationUnit $model)
    {
        parent::__construct($model);
    }
}
