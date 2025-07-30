<?php

declare(strict_types=1);

namespace App\Domains\Organization\Units;

use App\Domains\Shared\Models\OrganizationUnit;
use App\Domains\Shared\Services\BaseService;

class OrganizationUnitService extends BaseService
{
    public function __construct()
    {
        parent::__construct(
            model: $model = new OrganizationUnit,
            repository: new OrganizationUnitRepository(model: $model),
            exceptionClass: OrganizationUnitException::class,
        );
    }

    public function createUsingBuilder(OrganizationUnitBuilder $builder): OrganizationUnit
    {
        return $this->create($builder->build()->toArray());
    }
}
