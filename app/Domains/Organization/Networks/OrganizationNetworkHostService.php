<?php

declare(strict_types=1);

namespace App\Domains\Organization\Networks;

use App\Domains\Shared\Models\OrganizationNetworkHost;
use App\Domains\Shared\Services\BaseService;

class OrganizationNetworkHostService extends BaseService
{
    public function __construct()
    {
        parent::__construct(
            model: $model = new OrganizationNetworkHost,
            repository: new OrganizationNetworkHostRepository(model: $model),
            exceptionClass: OrganizationNetworkHostException::class,
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
