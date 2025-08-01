<?php

declare(strict_types=1);

namespace App\Domains\Organization\Tags;

use App\Domains\Shared\Models\OrganizationTag;
use App\Domains\Shared\Services\BaseService;

class OrganizationTagService extends BaseService
{
    public function __construct()
    {
        parent::__construct(
            model: $model = new OrganizationTag,
            repository: new OrganizationTagRepository(model: $model),
            exceptionClass: OrganizationTagException::class,
        );

        // Tap on the query to always get only organization-related data
        $this->repository->tapQueryAlways(
            fn ($query) => $query->where('org_id', activeOrganizationUser()->org_id)
        );
    }
}
