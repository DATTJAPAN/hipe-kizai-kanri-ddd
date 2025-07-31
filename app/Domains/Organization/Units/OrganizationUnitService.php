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

        // Tap on query to always get only organization related data
        $this->repository->tapQueryAlways(
            fn ($query) => $query->where('org_id', activeUser()->org_id)
        );
    }

    public function createUsingBuilder(OrganizationUnitBuilder $builder): OrganizationUnit
    {
        return $this->create($builder->build()->toArray());
    }
}
