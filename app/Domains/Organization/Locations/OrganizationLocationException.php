<?php

declare(strict_types=1);

namespace App\Domains\Organization\Locations;

use App\Core\Exceptions\HasCrudExceptionFactory;
use Exception;

class OrganizationLocationException extends Exception
{
    use HasCrudExceptionFactory;
}
