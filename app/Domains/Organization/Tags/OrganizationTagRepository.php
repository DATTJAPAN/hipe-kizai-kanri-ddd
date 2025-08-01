<?php

declare(strict_types=1);

namespace App\Domains\Organization\Tags;

use App\Domains\Shared\Models\OrganizationTag;
use App\Domains\Shared\Repositories\BaseRepository;

class OrganizationTagRepository extends BaseRepository
{
    public function __construct(OrganizationTag $model)
    {
        parent::__construct($model);
    }
}
