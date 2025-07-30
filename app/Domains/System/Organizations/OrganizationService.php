<?php

declare(strict_types=1);

namespace App\Domains\System\Organizations;

use App\Domains\Shared\Models\Organization;
use App\Domains\Shared\Services\BaseService;

class OrganizationService extends BaseService
{
    public function __construct()
    {
        parent::__construct(
            model: $model = new Organization,
            repository: new OrganizationRepository(model: $model),
            exceptionClass: OrganizationException::class,
        );
    }
}
