<?php

declare(strict_types=1);

namespace App\Domains\Organization\Networks;

use App\Domains\Shared\Repository\BaseRepository;

class OrganizationNetworkRepository extends BaseRepository
{
    public function __construct(OrganizationNetwork $model)
    {
        parent::__construct($model);
    }
}
