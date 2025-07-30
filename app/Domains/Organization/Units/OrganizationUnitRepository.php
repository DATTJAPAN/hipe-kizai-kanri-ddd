<?php

declare(strict_types=1);

namespace App\Domains\Organization\Units;

use App\Domains\Shared\Models\OrganizationUnit;
use App\Domains\Shared\Repositories\BaseRepository;

/**
 * @extends BaseRepository<OrganizationUnit>
 */
class OrganizationUnitRepository extends BaseRepository
{
    public function __construct(OrganizationUnit $model)
    {
        parent::__construct($model);
    }
}
