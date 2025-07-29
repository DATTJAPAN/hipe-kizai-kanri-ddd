<?php

declare(strict_types=1);

namespace App\Domains\System\Organizations;

use App\Domains\Shared\Models\Organization;
use App\Domains\Shared\Repositories\BaseRepository;

/**
 * @extends BaseRepository<Organization>
 */
class OrganizationRepository extends BaseRepository
{
    public function __construct(Organization $model)
    {
        parent::__construct($model);
    }
}
