<?php

declare(strict_types=1);

namespace App\Domains\Shared\Domains\Organizations;

use DomainException;

class OrganizationAffiliationException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Missing connection between the organization and this entity');
    }
}
