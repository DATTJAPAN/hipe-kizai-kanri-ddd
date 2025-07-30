<?php

declare(strict_types=1);

namespace App\Domains\Organization\Units;

use App\Core\Exceptions\HasCrudExceptionFactory;
use Exception;

class OrganizationUnitException extends Exception
{
    use HasCrudExceptionFactory;
}
