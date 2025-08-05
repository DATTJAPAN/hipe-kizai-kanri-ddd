<?php

declare(strict_types=1);

namespace App\Domains\System\Organizations;

use App\Core\Exceptions\HasCrudExceptionFactory;
use Exception;

class OrganizationException extends Exception
{
    use HasCrudExceptionFactory;
}
