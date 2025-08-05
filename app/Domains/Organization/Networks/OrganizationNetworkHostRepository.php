<?php

declare(strict_types=1);

namespace App\Domains\Organization\Networks;

use App\Domains\Shared\Models\OrganizationNetworkHost;
use App\Domains\Shared\Repositories\BaseRepository;

class OrganizationNetworkHostRepository extends BaseRepository
{
    public function __construct(OrganizationNetworkHost $model)
    {
        parent::__construct($model);
    }
}
