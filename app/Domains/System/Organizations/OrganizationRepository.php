<?php

declare(strict_types=1);

namespace App\Domains\System\Organizations;

use App\Domains\Shared\Domains\Organizations\Organization;
use App\Domains\Shared\Repository\BaseRepository;

/**
 * @extends \App\Domains\Shared\BaseRepository<Organization>
 */
class OrganizationRepository extends BaseRepository
{
    public function __construct(Organization $model)
    {
        parent::__construct(model: $model);
    }
}
