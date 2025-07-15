<?php

declare(strict_types=1);

namespace App\Domains\Organization\Users;

use App\Domains\Shared\Repository\BaseRepository;

class OrganizationUserRepository extends BaseRepository
{
    public function __construct(OrganizationUser $model)
    {
        parent::__construct(model: $model);
    }
}
