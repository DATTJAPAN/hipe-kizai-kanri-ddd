<?php

declare(strict_types=1);

namespace App\Domains\Organization\Locations;

use App\Domains\Shared\Models\OrganizationLocation;
use App\Domains\Shared\Services\BaseService;

class OrganizationLocationService extends BaseService
{
    public function __construct()
    {
        parent::__construct(
            model: $model = new OrganizationLocation,
            repository: new OrganizationLocationRepository(model: $model),
            exceptionClass: OrganizationLocationException::class,
        );

        // Tap on the query to always get only organization-related data
        $this->repository->tapQueryAlways(
            function ($query) {
                $query->forOrganization(orgIdOrPrefixedId: activeOrganizationUser()->org_id);
                return $query;
            }
        );
    }
}
