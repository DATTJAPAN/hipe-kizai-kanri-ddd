<?php

declare(strict_types=1);

namespace App\Domains\Shared\Domains\Authorization;

use App\Domains\Shared\Repository\BaseRepository;

class RoleRepository extends BaseRepository
{
    public function __construct(Role $model)
    {
        parent::__construct(model: $model);
    }
}
